<?php namespace Veemo\Core\Http\Middleware;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;


class FrontendForceSslMiddleware {


    protected $config;

    protected $request;

    protected $redirect;

    /**
     * Create a new filter instance.
     *
     * @param  Repository $config
     * @param  Request $request
     * @param Redirector $redirect
     */
    public function __construct(Repository $config, Request $request, Redirector $redirect)
    {
        $this->config = $config;
        $this->request = $request;
        $this->redirect = $redirect;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $is_ssl = $this->config->get('veemo.core.frontendForceSsl', false);

        if ($is_ssl)
        {

            if( ! $this->request->secure())
            {
                 return $this->redirect->secure($this->request->getRequestUri());
            }

        }

        return $next($request);
    }

}
