<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateNameRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Http\Resources\User\UserResource;
use App\Services\UserProfileService;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(
        protected UserProfileService $profileService
    ) {}
    /**
     * Get my profile
     */
    public function profile(Request $request)
    {
        return new UserResource(
            $request->user()->load(['roles', 'addresses'])
        );
    }
    /**
     * Update my profile
     */
    public function update(UpdateNameRequest $request)
    {
        $user = $this->profileService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return new UserResource($user->load(['roles', 'addresses']));
    }

    /**
     * Change my password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $this->profileService->changePassword(
            $request->user(),
            $request->validated('password')
        );

        return new AuthenticatedResource($data);
    }
}
