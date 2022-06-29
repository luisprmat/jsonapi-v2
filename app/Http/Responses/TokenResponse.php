<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    public function __construct(private User $user)
    {
        //
    }

    public function toResponse($request)
    {
        $plainTextToken = $this->user->createToken(
            $request->device_name,
            $this->user->permissions->pluck('name')->toArray()
        )->plainTextToken;

        return response()->json([
            'plain-text-token' => $plainTextToken
        ]);
    }
}
