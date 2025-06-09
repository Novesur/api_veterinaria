<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Para un registro en especifico
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->resource->id,
            'name'=> $this->resource->name,
            'email'=> $this->resource->email,
            'gender'=> $this->resource->gender,
            'surname' => $this->resource->surname,
            'full_name' => $this->resource->name .' '. $this->resource->surname,
            'role_id' => $this->resource->role_id,
             'role'=>[
                "name" => $this->resource->role->name,
            ],
            'rol_name'=>$this->resource->role->name,
            'avatar' => $this->resource->avatar ? env("APP_URL")."storage/".$this->resource->avatar:NULL,
            'type_document' => $this->resource->type_document,
            "n_document" => $this->resource->n_document,
            "phone" => $this->resource->phone,
            "designation" => $this->resource->designation,
            "birthday" => $this->resource->birthday ? Carbon::parse($this->resource->birthday)->format("Y/m/d") :null,
        ];
    }
}
