<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

use Throwable;

class Handler extends ExceptionHandler
{
    const DEFAULT_STATUS = 400;

    // TODO: other errors
    protected $errCodes = [
        'Illuminate\Validation\ValidationException' => 111,
        'Illuminate\Auth\AuthenticationException' => 123,
        'default' => 100
    ];

    protected $errStatuses = [
        'Illuminate\Validation\ValidationException' => 422,
        'Illuminate\Auth\AuthenticationException' => 401,
        'default' => self::DEFAULT_STATUS
    ];

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($this->shouldReturnJson($request, $e)) {
            return $this->renderApiError($request, $e);
        }

        return parent::render($request, $e);
    }

    private function renderApiError($request,Throwable $e) {
        $errClass = get_class($e);
        $code = $this->errCodes[$errClass] ?? $this->errCodes['default'];
        $status = $this->errStatuses[$errClass] ?? $this->errStatuses['default'];
        $message = $e->getMessage();

        if ($e instanceof ValidationException) {
            foreach ($e->errors() as $error) {
                $message = $error[0];
            }
        }

        return response()->error([
            'code' => $code,
            'message' => $message
        ], $status);
    }
}
