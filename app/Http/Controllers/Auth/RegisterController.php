<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Models\Member;
use App\Models\Package;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Notification;
use App\Notifications\DbStoreNotification;
use App\Utility\EmailUtility;
use Carbon\Carbon;
use Kutia\Larafirebase\Facades\Larafirebase;

class RegisterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    public function showRegistrationForm() {
        return view('frontend.user_registration');
    }

    protected function validator(array $data) {
        return Validator::make($data, [
            'user_id'  => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        // return $data['date_of_birth'];
        $approval = get_setting('member_approval_by_admin') == 1 ? 0 : 1;
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'user_id'  => $data['user_id'],
                'membership'  => 1,
                'email'       => $data['email'],
                'password'    => Hash::make($data['password']),
                'code'        => unique_code(),
                'approved'    => $approval,
            ]);
        } else {
            if (addon_activation('otp_system')) {
                $user = User::create([
                    'user_id'  => $data['user_id'],
                    'membership'  => 1,
                    'phone'       => '+' . $data['country_code'] . $data['phone'],
                    'password'    => Hash::make($data['password']),
                    'code'        => unique_code(),
                    'approved'    => $approval,
                    'verification_code' => rand(100000, 999999)
                ]);
            }
        }

        if (addon_activation('referral_system')) {
            if ($data['referral_code'] != null) {
                $reffered_user = User::where('code', $data['referral_code'])->first();
                if ($reffered_user != null) {
                    $user->referred_by = $reffered_user->id;
                    $user->save();
                }
            }
        }

        $member                             = new Member;
        $member->user_id                    = $user->id;
        $member->save();

        $member->gender                     = $data['gender'];
        $member->on_behalves_id             = $data['on_behalf'];
        if ($data['date_of_birth']) {
            $member->birthday                   = date('Y-m-d', strtotime($data['date_of_birth']));
        } else {
            $member->birthday                   = null;
        }

        // $member->birthday                   = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

        $package                                = Package::where('id', 1)->first();
        $member->current_package_id             = $package->id;
        $member->remaining_interest             = $package->express_interest;
        $member->remaining_photo_gallery        = $package->photo_gallery;
        $member->remaining_contact_view         = $package->contact;
        $member->remaining_profile_image_view   = $package->profile_image_view;
        $member->remaining_gallery_image_view   = $package->gallery_image_view;
        $member->auto_profile_match             = $package->auto_profile_match;
        $member->package_validity               = Date('Y-m-d', strtotime($package->validity . " days"));
        $member->save();

        if (addon_activation('otp_system') && $data['phone'] != null && get_setting('member_approval_by_admin') != 1) {
            $otpController = new OTPVerificationController;
            $otpController->send_code($user);
        }

        // Email to member
        if ($data['email'] != null  && env('MAIL_USERNAME') != null && get_setting('member_approval_by_admin') != 1) {
            $account_oppening_email = EmailTemplate::where('identifier', 'account_oppening_email')->first();
            if ($account_oppening_email->status == 1) {
                EmailUtility::account_oppening_email($user->id, $data['password']);
            }
        }
        // flash(translate('Registration successfully done! Now please wait for Approval.'));
        session()->flash('registration', 'Registration successfully done! Please note that if you are choosing ‘Gold’ or ‘Diamond’ package, your profile will be visible by all users. If you are not comfortable sharing your profile online then please choose ‘VIP Offline’ package which is the most popular one of our packages.');
        return $user;
    }

    public function register(Request $request) {
        // return $request->date_of_birth;
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                // return back()->with('registerError', 'Email or Phone');
            }
        } elseif (User::where('phone', '+' . $request->country_code . $request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        if (get_setting('member_approval_by_admin') != 1) {
            $this->guard()->login($user);
        }

        try {
            $notify_type = 'member_registration';
            $id = unique_notify_id();
            $notify_by = $user->id;
            $info_id = $user->id;
            $message = translate('A new member has been registered to your system. User ID: ') . $user->user_id;
            $route = route('members.index', $user->membership);

            // fcm 
            if (get_setting('firebase_push_notification') == 1) {
                $fcmTokens = User::where('user_type', 'admin')->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
                Larafirebase::withTitle($notify_type)
                    ->withBody($message)
                    ->sendMessage($fcmTokens);
            }
            // end of fcm
            Notification::send(User::where('user_type', 'admin')->where('email', 'ringmatrimonyinc@gmail.com')->first(), new DbStoreNotification($notify_type, $id, $notify_by, $info_id, $message, $route));
        } catch (\Exception $e) {
            // dd($e);
        }
        if ($user->email != null  && env('MAIL_USERNAME') != null && (get_email_template('account_opening_email_to_admin', 'status') == 1)) {
            $admin = User::where('user_type', 'admin')->first();
            EmailUtility::account_opening_email_to_admin($user, $admin);
        }
        if (get_setting('member_approval_by_admin') != 1 && $user->email != null) {
            event(new Registered($user));
            // return redirect()->route('user.login')->with('registerSuccess', 'Registration successful. Please verify your email.');
            // return back()->with('registerSuccess', 'Registration successful. Please verify your email.');
            flash(translate('Registration successful. Please verify your email.'))->success();
        }
        if (get_setting('member_approval_by_admin') != 1 && $user->email != null) {

            if (get_setting('email_verification') != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                // return redirect()->route('user.login')->with('registerSuccess', 'Registration successful.');
                // return back()->with('registerSuccess', 'Registration successful.');
                flash(translate('Registration successful.'))->success();
            } else {
                event(new Registered($user));
                // return back()->with('registerSuccess', 'Registration successful. Please verify your email.');
                // return redirect()->route('user.login')->with('registerSuccess', 'Registration successful. Please verify your email.');
                flash(translate('Registration successful. Please verify your email.'))->success();
            }
        }
        if (get_setting('member_approval_by_admin') != 1 && $user->phone != null) {
            // return redirect()->route('user.login')->with('registerSuccess', 'Registration successful. Please verify your phone number.');
            // return back()->with('registerSuccess', 'Registration successful. Please verify your phone number.');
            flash(translate('Registration successful. Please verify your phone number.'))->success();
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user) {
        //?? where should redirect user after registration
        if (get_setting('member_approval_by_admin') == 1) {
            return redirect()->route('home');
        } else {
            return redirect()->route('login');
        }
    }
}
