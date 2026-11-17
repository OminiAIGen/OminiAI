<?php

namespace App\Http\Controllers;

use App\Actions\EmailConfirmation;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MailController extends Controller
{
    public function emailConfirmationMail($email_confirmation_code): RedirectResponse
    {
        $user = User::where('email_confirmation_code', $email_confirmation_code)->firstOrFail();

        EmailConfirmation::forUser($user)->confirm();

        return redirect()->route('login')->with(['message' => __('E-Mail confirmed succesfully.'), 'type' => 'success']);
    }

    public function sendPasswordResetMail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $resetCode = $user->mintPasswordResetCode();

            SendPasswordResetEmail::dispatchSync($user, $resetCode);

            return response()->json(['message' => __('Password reset link sent successfully. Please also check your spam folder.'), 'type' => 'success']);
        }

        return response()->json(['message' => __('Password reset mail sent successfully'), 'type' => 'success']);
    }

    public function passwordResetCallback($password_reset_code): View
    {
        $user = User::findByPasswordResetCode($password_reset_code);

        if ($user === null) {
            abort(404);
        }

        // The stored column holds only the hash; hand the plaintext code from the URL to the
        // form so the save step can match it.
        $user->password_reset_code = $password_reset_code;

        return view('panel.authentication.password_reset_final', compact('user', 'password_reset_code'));
    }

    public function passwordResetCallbackSave(Request $request): JsonResponse
    {
        $request->validate([
            'password'            => ['required', 'confirmed', Password::defaults()],
            'password_reset_code' => ['required', 'string'],
        ]);

        $user = User::findByPasswordResetCode($request->input('password_reset_code'));

        if ($user === null) {
            return response()->json(['errors' => [__('This password reset link is invalid or has expired.')]], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Burn the code so the link cannot be replayed.
        $user->clearPasswordResetCode();

        // Do not log the user in here: that would bypass email OTP and Google 2FA. Send them
        // through the normal login flow, where those checks still gate access.
        return response()->json(['link' => route('login')], 200);
    }
}
