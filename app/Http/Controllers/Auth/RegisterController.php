<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegisterController extends Controller
{
    /**
     * @param RegisterUserRequest $request
     * @return JsonResource
     * @throws Throwable
     */
    public function register(RegisterUserRequest $request): JsonResource
    {
        $validated = $request->validated();
        $user = User::create($validated);

        return $user->toResource(UserResource::class);
    }
}
