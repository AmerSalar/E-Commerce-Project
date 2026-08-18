<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        DB::table($table)
            ->where('email', $email)
            ->update([
                'reset_token' => Hash::make($token)
            ]);

        return response()->json([
            'message' => "Verified. you can now reset password.",
            'reset_token' => $token
        ], 200);
    }
}
