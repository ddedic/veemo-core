<?php namespace Veemo\Core\Traits;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;



trait AuthenticatesAndRegistersUsersTrait
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The registrar implementation.
     *
     * @var Registrar
     */
    protected $registrar;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return $this->theme->view('modules.auth.register')->render();
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->registrar->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->registrar->create($request->all());
        $role = Role::where('slug', '=', config('veemo.core.users_default_role'))->firstOrFail();
        $user->syncRoles([$role->id]);
        $this->auth->login($user);

        return redirect($this->redirectRoute());
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectRoute()
    {

        if (property_exists($this, 'redirectRoute'))
        {
            // if redirectRoute has keyword backend, we'll assume that redirect goes to root backend url path
            if (preg_match("/backend/i", $this->redirectRoute)) {
                return config('veemo.core.backendPrefix');//. '.dashboard';
            }
        }

        // back to homepage
        return '/';
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return $this->theme->view('modules.auth.login')->render();
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required', 'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            return redirect()->intended($this->redirectRoute());
        }

        return redirect()->route($this->loginRoute())
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginRoute()
    {
        return property_exists($this, 'loginRoute') ? $this->loginRoute : 'frontend.login';
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return redirect()->route($this->redirectRoute());
    }

}