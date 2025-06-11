<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => strval($this->id),
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'status' =>  $this->status ? true : false,
            'role' => $this->getRoleNames()->first(),
            'permissions' => $this->getAllPermissions()->map(function($permission){
                return [
                    'id' => $permission->id,
                    'name' => $permission->name
                ];
            })
        ];
    }
}
