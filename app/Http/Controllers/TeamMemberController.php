<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class TeamMemberController extends Controller {
    public function index() {
        $members = TeamMember::orderBy('sorting_position')->get();
        return view('admin.our_team.index', [
            'members' => $members
        ]);
    }

    public function member_add(Request $request) {
        // return $request->member_name;

        $request->validate(
            [
                'member_name' => 'required',
                'member_image' => 'max:1024|mimes:png,jpg,jpeg,webp,gif',
                'designation' => 'required',
                'sorting_position' => 'required',
            ]
        );

        $member = TeamMember::create([
            'member_name' => $request->member_name,
            'designation' => $request->designation,
            'sorting_position' => $request->sorting_position,
        ]);
        // return $request->all();

        if (!empty($request->member_image)) {
            $uploaded_image = $request->member_image;
            $ext = $uploaded_image->getClientOriginalExtension();
            $file_name = 'member-' . $member->id . '.' . $ext;

            Image::make($uploaded_image)->save(public_path('uploads/team_members/' . $file_name));

            $member->update([
                'member_image' => $file_name,
            ]);
        } else {
            $member->update([
                'member_image' => 'def-avatar.png',
            ]);
        }

        return back()->with('addSuccess', 'New Member Added Successfully!');
    }

    public function member_update(Request $request) {
        $member = TeamMember::find($request->id);

        $request->validate(
            [
                'member_name' => 'required',
                'member_image' => 'max:1024|mimes:png,jpg,jpeg,webp,gif',
                'designation' => 'required',
                'sorting_position' => 'required',
            ]
        );

        $member->update([
            'member_name' => $request->member_name,
            'designation' => $request->designation,
            'sorting_position' => $request->sorting_position,
        ]);
        // return $request->all();

        if (!empty($request->member_image)) {
            if ($member->member_image != 'def-avatar.png') {
                $old_image = public_path('uploads/team_members/' . $member->member_image);
                unlink($old_image);
            }

            $uploaded_image = $request->member_image;
            $ext = $uploaded_image->getClientOriginalExtension();
            $file_name = 'member-' . $member->id . '.' . $ext;


            Image::make($uploaded_image)->save(public_path('uploads/team_members/' . $file_name));

            $member->update([
                'member_image' => $file_name,
            ]);
        }

        return redirect()->route('admin.our_team.index')->with('updateSuccess', 'Member Updated Successfully!');
    }

    public function member_edit_page($id) {
        $member = TeamMember::find($id);
        return view('admin.our_team.edit', [
            'member' => $member
        ]);
    }

    public function member_delete($id) {
        if (TeamMember::find($id)->delete()) {
            return back()->with('dltSuccess', 'Member deleted successfully');
        } else {
            return back()->with('error', 'Something went wrong');
        }
    }
}
