<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException as AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Session;
use Throwable;
use Response;
use ReflectionException;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof \Illuminate\Database\QueryException){
            $errorCode = $exception->errorInfo[1];          
            switch ($errorCode) {
                case 1062://code dublicate entry 
                    return back()->with('message', 'Email Already in Use.');
                    break;
                case 1364://handel any auther error
                    return back()->with('error', $exception->getMessage());                       
                    break; 
                default :     
                    return response()->make(view('errors.500'), 500);
            }
        }
        else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
        {
            return response()->make(view('errors.404'), 404);
        }
        else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\AuthenticationException)
        {
            return response()->make(view('errors.419'), 419);
        }
        else if ($exception instanceof ReflectionException) {
            return response([
                'Status' => 0,
                'Error'=>'Controller function that does not exist'
            ]); 
        }
        else if ($exception instanceof ModelNotFoundException) {
            return response([
                'Status' => 0,
                'Error'=>'Model instance not found'
            ]); 
        }
        /*else {
            return response()->make(view('errors.500'), 500);
        }*/
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception) 
    {
        if ($request->expectsJson()) {
            return back()->with('message', 'You are unauthenticated'); 
        }
    
        return redirect()->guest('login');
    }
}
