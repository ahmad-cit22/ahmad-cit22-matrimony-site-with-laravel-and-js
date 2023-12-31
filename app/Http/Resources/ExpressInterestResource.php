<?php

namespace App\Http\Resources;

use App\User;
use Carbon\Carbon;
use App\Utility\MemberUtility;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpressInterestResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        $interested_by = User::find($this->interested_by);
        $package_update_alert = (get_setting('full_profile_show_according_to_membership') == 1 && auth()->user()->membership == 1) ? true : false;

        return [
            'id'                   => $this->id,
            'user_id'              => $this->interested_by,
            'package_update_alert' => $package_update_alert,
            'photo'                => uploaded_asset($interested_by->photo),
            'member_id'                 => $interested_by->user_id,
            'age'                  => Carbon::parse($interested_by->member->birthday)->age,
            'status'               => $this->status == 1 ? 'Approved' : 'Pending',
            'religion'             => MemberUtility::member_religion($this->interested_by),
            'country'              => MemberUtility::member_country($this->interested_by),
            'mothere_tongue'       => MemberUtility::member_mothere_tongue($this->interested_by),
        ];
    }
}
