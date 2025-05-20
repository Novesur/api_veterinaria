<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;


class UserCollection extends ResourceCollection
{
    /**
     * Srive para un listado de registros
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => UserResource::collection($this->collection),
        ];
    }
}
