<?php	namespace App\Traits;

use Log;
use Response;

trait CommonUtil
{

    // To check an user is logged in or not.
    public function checkIsUser()
    {
        if (\Auth::check()) {
            return true;
        }
        return false;
    }

    // Common success response function. Params, $message want to show, $data array which needs to be return.	
    public function SuccessResponse($message = '', $data = array())
    {
        return Response::json([
            'response' => [
                'error' => FALSE,
                'status' => 10,
                'message' => $message,
                'data' => $data
            ]], 200);
    }

    // Common error response function. Params, $message want to show, $status_code return code.
    public function FailureResponse($message = '', $status_code)
    {
        return Response::json([
            'response' => [
                'error' => TRUE,
                'status' => 11,
                'message' => $message,
                'data' => ''
            ]], $status_code);
    }

    // To handle error responses on try catch block, have to pass the error object,
    public function showErrorResponse($msgFor, $e)
    {
        $respMsg = '';
        if (!empty($e)) {
            $respMsg = $msgFor . " | Error Msg: " . $e->getMessage() . ". | Occured on Line# " . $e->getLine();
            $respMsg .= "Trace |" . $e->getTraceAsString();
            Log::info(' Exception Occurred: ' . $msgFor . ' >>' . $e);
        }
        return $respMsg;
    }

    // Format date with the provided date format. Params: $date, optionaly pass format.
    public function dateformater($date, $date_format = 'Y-m-d')
    {
        if (!empty($date) && $date != '0000-00-00') {
            return date($date_format, strtotime($date));
        } else {
            return "";
        }
    }

    // For add ledding 0 for the proved $id, with the length of $pos; Params: $id, $pos (Optional).
    public function generatePaddedNumber($id, $pos = 5)
    {
        $ident = str_pad($id, $pos, '0', STR_PAD_LEFT);
        return $ident;
    }

    public function numberFormater($number)
    {
            return number_format($number, 2, '.', '');
    }

}