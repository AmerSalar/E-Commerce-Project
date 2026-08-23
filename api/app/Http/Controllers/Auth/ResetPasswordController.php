<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $email = strtolower(trim($request->input('email')));

        $user = User::where(
            'email',
            $email
        )->first();

        if ($user) {

            $code = (string) random_int(100000, 999999);

            // an expiry date
            $expires_at = now()->addMinutes(5);

            // delete previous codes
            DB::table('password_reset_codes')
                ->where('email', $email)
                ->delete();

            // update or insert new code
            DB::table('password_reset_codes')
                ->insert([
                    'email' => $email,
                    'code' => Hash::make($code),
                    'expires_at' => $expires_at
                ]);

            // send password reset code to email
            // in this example we write it to a file
            File::put('fake_email.txt', "Verification code: {$code}");
        }

        // Lie if email was wrong, don't tell guest we don't have that email.
        // also return this if email was right and code was sent.
        return response()->json([
            'message' => 'Verification code was sent to the provided email.'
        ], 200);
    }

    public function verify(Request $request)
    {

        $table = "password_reset_codes";
        // front-end must save the email of step-1 in memory/state
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6']
        ]);

        $email = strtolower(trim($request->input('email')));
        $record = DB::table($table)->where('email', $email)->first();

        if (
            !$record ||
            now()->isAfter($record->expires_at) ||
            !Hash::check($request->input('code'), $record->code)
        ) {
            return response()->json(['message' => "Invalid or expired code!"], 401);
        }

        $token = Str::random(64);
        $expires_at = now()->addMinutes(5);

        DB::table($table)
            ->where('email', $email)
            ->update([
                'reset_token' => Hash::make($token),
                'expires_at' => $expires_at
            ]);

        return response()->json([
            'message' => "Verified. you can now reset password.",
            'reset_token' => $token
        ], 200);
    }

    public function reset(Request $request)
    {
        $table = "password_reset_codes";
        // front-end has email saved, and sends back the token from step-2
        // we use the token to check if user completed other steps before proceeding

        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:64'],
            'password' => [
                'required',
                'confirmed',
                Password::default()->letters()->numbers()->symbols()
            ],
        ]);

        $email = strtolower(trim($request->input('email')));

        $record = DB::table($table)
            ->where('email', $email)
            ->first();


        if (
            !$record ||
            !$record->reset_token ||
            now()->isAfter($record->expires_at) ||
            !Hash::check(
                $request->input('token'),
                $record->reset_token
            )
        ) {
            return response()->json(['message' => 'Unauthorized access!'], 401);
        }

        $user = User::where('email', $email)->firstOrFail();

        if (Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'New password can\'t be old password!'], 422);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        // we delete the reset password code and token record
        DB::table($table)->where('email', $email)->delete();

        $token = Auth::login($user);

        $data = new AuthData(
            user: $user,
            token: $token,
            message: "Password reset successfully. You are now logged-in",
        );

        return new AuthenticatedResource($data);
    }
}
