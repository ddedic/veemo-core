<?php namespace Veemo\Core\Exception;

use App\Exceptions\Handler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ExceptionHandler
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Exception
 */
class ExceptionHandler extends Handler
{

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request   $request
     * @param  Exception $e
     * @return Response
     */
    public function render($request, Exception $e)
    {
        $uri = explode('/', $request->getRequestUri());

        if ($uri[0] == config('veemo.core.backendPrefix')) {

            // get settings, where theme = backend
        } else {

            // get settings, where theme = frontend
        }


        if ($this->isHttpException($e)) {
            return $this->renderHttpException($e);
        } elseif (!config('app.debug')) {
            return response()->view("streams::errors.500", ['message' => $e->getMessage()]);
        } else {
            return parent::render($request, $e);
        }
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();

        if (!config('app.debug') && view()->exists("streams::errors.{$status}")) {
            return response()->view("streams::errors.{$status}", ['message' => $e->getMessage()], $status);
        } else {
            return (new SymfonyDisplayer(config('app.debug')))->createResponse($e);
        }
    }
}
