<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct(
        #[CurrentUser('back-api')] protected $currentUser,
    ) {}

    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        $this->currentUser->update([
            'password' => Hash::make($request->string('password')),
        ]);

        return response()->json(['message' => 'Password updated.']);
    }
}
