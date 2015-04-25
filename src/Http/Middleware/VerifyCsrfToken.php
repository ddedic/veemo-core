<?php namespace Veemo\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Routing\Redirector;
use Illuminate\Session\TokenMismatchException;



class VerifyCsrfToken extends \App\Http\Middleware\VerifyCsrfToken
{

    /**
     * The redirector utility.
     *
     * @var Redirector
     */
    protected $redirector;


    /**
     * Create a new VerifyCsrfToken instance.
     *
     * @param Encrypter  $encrypter
     * @param Redirector $redirector
     */
    public function __construct(Encrypter $encrypter, Redirector $redirector)
    {
        $this->redirector = $redirector;

        parent::__construct($encrypter);
    }

    /**
     * Handle the request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable                 $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {

            flash()->error('Your security token has expired. Please submit the form again.');

            return $this->redirector->back();
        }
    }
}