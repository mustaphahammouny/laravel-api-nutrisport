<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct(
        #[CurrentUser('front-api')] protected $currentCustomer,
    ) {}

    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->currentCustomer->update([
            'password' => Hash::make($request->string('password')),
        ]);

        return response()->json(['message' => 'Password updated.']);
    }
}
