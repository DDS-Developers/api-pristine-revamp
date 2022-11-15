<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        // return parent::render($request, $exception);
        $code = 500;
        $message = $exception->getMessage();
        if (
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException ||
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
        ) {
            return parent::render($request, $exception);
            $message = 'Route Not Found';
        } else if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $code = 422;
            $message = [];
            foreach ($exception->errors() as $ky => $err) {
                $message[$ky] = $err[0];
            }
        } else if (str_contains($message, 'No query results for model')) {
            $code = 404;
            $message = 'Data Not Found';
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $code = $exception->getStatusCode();
        }
        return response()->json([
            'code' => $code,
            'message' => $message,
        ], $code);
    }
}
