<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoginService;
use App\Validator\LoginValidator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function __construct(
        private LoginValidator $loginValidator,
        private LoginService $loginService,
    ) {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (url()->previous() != route('register') && Redirect::getIntendedUrl() === null) {
            Redirect::setIntendedUrl(url()->previous()); // make sure we redirect back to the page we came from
        }

        return view('auth.login');
    }

    protected function authenticated(Request $request, User $user)
    {
        if ($user->is_blocked) {
            $this->guard()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked. Please contact support.',
            ]);
        }

        return redirect($this->getRedirectUrl($user));
    }

    protected function getRedirectUrl(User $user): string
    {
        // Change this if you want to redirect to a different page after login
        if (Redirect::getIntendedUrl() !== null && rtrim(Redirect::getIntendedUrl(), '/') !== rtrim((route('home')), '/')) {
            return Redirect::getIntendedUrl();
        }

        if ($user->is_admin) {
            return route('filament.admin.pages.dashboard');
        }

        return route('filament.dashboard.pages.dashboard');
    }

    protected function validateLogin(Request $request)
    {
        $this->loginValidator->validateRequest($request);
    }

    protected function attemptLogin(Request $request)
    {
        return $this->loginService->attempt($this->credentials($request), $request->boolean('remember'));
    }
}
