<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'users',
            'id' => (string)$this->id,
            'attributes' => [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'display_picture' => $this->display_picture
            ],
            //'relationships' => new UserRelationshipResource($this),
            'links' => [
                'self' => route('users.show', ['id' => $this->id]),
            ]
        ];
    }
}
