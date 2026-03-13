<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        #[CurrentUser('front-api')] protected $currentCustomer,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json(CustomerResource::make($this->currentCustomer));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $this->currentCustomer->fill($request->validated());

        if ($this->currentCustomer->isDirty('email')) {
            $this->currentCustomer->email_verified_at = null;
        }

        $this->currentCustomer->save();

        return response()->json(['message' => 'Profile updated.']);
    }
}
