<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Helpers\Helpers as ExceptionHelpers;
use Symfony\Component\Debug\Exception\FlattenException;
use Redirect;
use App\Models\Practice;
use Illuminate\Support\Facades\Auth;

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
    public function render($request, Exception $e)
    {
        $ret_err_code   = 0;
        $chk_env_site   = getenv('APP_ENV');
        // Eror message logged for reference purpose.       
        $exception = FlattenException::create($e);
        $statusCode = $exception->getStatusCode($exception);
        $errMSg = $e->getMessage();

        // Include logged username along with practice name.
        if($errMSg != ""){          
            $practice_name = \App\Models\Practice::getPracticeName();
            $ip = getenv('HTTP_CLIENT_IP')?:
                    getenv('HTTP_X_FORWARDED_FOR')?:
                        getenv('HTTP_X_FORWARDED')?:
                            getenv('HTTP_FORWARDED_FOR')?:
                                getenv('HTTP_FORWARDED')?:
                                    getenv('REMOTE_ADDR');
            $user_name = (Auth::check() && isset(Auth::user()->name) )? Auth::user()->name : "Guest";
            $eMsg = "\n <b>Error Code#: ".$statusCode." | Practice: ".$practice_name." | Accessed By: ".$user_name. " | IP: ".$ip.
                    "\n File: ".$e->getFile()." | #".$e->getLine() .
                    "</b>\n <small>Msg: <b>".$e->getMessage()."</b></small>";
            \Log::error($eMsg);
        }
        return parent::render($request, $e);
        
        
    }
}
