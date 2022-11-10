<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
		if ($exception instanceof MethodNotAllowedHttpException) 
        {
            return $request->expectsJson() 
                    ? response()->json([
							'status' => 405,
							'message' => 'Invalid Method type for the requested route',
						], 405 ): response()->view('errors.page-not-found', [], 405);
        }
		elseif ($exception instanceof NotFoundHttpException)
		{
			return response()->view('errors.page-not-found', [], 404);
			//return $request->expectsJson() ? response()->json(['status' => 404,'message' => 'Url is not implemented',], 404 ): response()->view('errors.page-not-found', [], 404);
		}
		return parent::render($request, $exception);
    }
}
