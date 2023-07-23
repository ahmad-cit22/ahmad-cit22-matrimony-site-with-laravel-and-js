<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ChatResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        $chats = $this->chats()->latest()->get();
        $user_to_show = auth()->id() !== $this->sender->id ? 'receiver' : 'sender';
        return [
            'receiver_id' => $this->receiver->user_id,
            'receiver_photo' => $this->receiver->photo != null ? uploaded_asset($this->receiver->photo) : static_asset('assets/frontend/default/img/avatar-place.png'),
            'sender_id' => $this->sender->user_id,
            'auth_user_photo' =>  uploaded_asset(auth()->user()->photo) !== null ? uploaded_asset(auth()->user()->photo) : static_asset('assets/frontend/default/img/avatar-place.png'),
            'messages' => $chats,
            // 'sender_messages'=>$this->sender->$chats,
            // 'receiver_messages'=>$this->receiver->$chats,
        ];
    }
}
