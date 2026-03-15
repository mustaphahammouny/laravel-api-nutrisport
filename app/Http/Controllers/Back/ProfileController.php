<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        #[CurrentUser('back-api')] protected $currentUser,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json(UserResource::make($this->currentUser));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $this->currentUser->fill($request->validated());

        if ($this->currentUser->isDirty('email')) {
            $this->currentUser->email_verified_at = null;
        }

        $this->currentUser->save();

        return response()->json(['message' => 'Profile updated.']);
    }
}
