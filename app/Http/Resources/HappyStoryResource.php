<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HappyStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $photos = [];
        $images = explode(',', $this->photos);
        foreach ($images as  $value) {
            $photos[] = uploaded_asset($value);
        }
        $package_update_alert = (get_setting('full_profile_show_according_to_membership') == 1 && (auth()->check() && auth()->user()->membership == 1)) ? true : false;
        return [
            'id'                   => $this->id,
            'user_id'              => $this->user_id,
            'package_update_alert' => $package_update_alert,
            'user_id'      => $this->user->user_id,
            'partner_name'         => $this->partner_name,
            'title'                => $this->title,
            'details'              => str_replace('&amp;', '&', str_replace('&nbsp;', ' ', strip_tags($this->details))),
            'date'                 => $this->created_at->format('d F, Y'),
            'thumb_img'            => uploaded_asset($images[0]),
            'photos'               => $photos,
            'video_provider'       => $this->video_provider,
            'video_link'           => $this->video_link,
        ];
    }
}
