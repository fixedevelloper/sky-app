<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->back();
    }

    public function signin(Request $request)
    {

        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), $rules = [
                'email' => ['required', 'email'],
                'password' => 'required',

            ], $messages = [
                'email.required' => 'Email field is required!',
                'password.required' => 'password  is required!',
            ]);
            if ($validator->fails()) {
                toastr()->error("Email or password required",  ["Failed loggedIn"]);
                return redirect()->back()
                    ->withErrors($validator)->with(['message' => $messages])
                    ->withInput();
            }

            logger($request->all());
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                toastr()->success("Authentication successful",  ["Success loggedIn"]);
                $request->session()->regenerate();
                logger('ici-----2');
                    return redirect()->route('dashboard');

            }
            logger('ici-----');
            toastr()->error("User not found or User not activate",  ["Failed loggedIn"]);
            return redirect()->route('signin');
        }
        return view('security.login');
    }

    public function lock(Request $request)
    {
        return view('auth.lock');
    }

}
