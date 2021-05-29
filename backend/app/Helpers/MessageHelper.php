<?php


namespace App\Helpers;


use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MessageHelper
{
    /**
     * Throw New Exception
     */
    public static function throwExceptionMessage($message= "")
    {
        throw new \Exception($message,Response::HTTP_BAD_REQUEST );
    }

    /**
     * Throw Exception
     */
    public static function throwException($exception)
    {
        $message = config("messages.400");
        if(!is_string($exception)){
            $message = $exception->getMessage();
            if($exception->getCode() == 0){
                $message = config("messages.400");
            }
        }
        throw new \Exception($message,Response::HTTP_BAD_REQUEST );
    }

    /**
     * Json Response
     */
    public static function jsonResponse($results = null)
    {
        return \response()->json($results);
    }

    /**
     * Unauthorized Message
     */
    public static function unauthorizedMessage()
    {
        return new JsonResponse([
            'key'       => "UN_AUTHORIZED",
            'message'   => config("messages.unauthorized"),
            'timestamp' => now()->toDateTimeString()
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Success Login Message
     */
    public static function successLoginMessage($token)
    {
        return new JsonResponse([
            'message'   => config("messages.login_message"),
            "type"      => "Bearer ",
            "token"     => $token,
            'timestamp' => now()->toDateTimeString()
        ], Response::HTTP_OK);
    }

    /**
     * Error Message
     */
    public static function errorMessage($message = null, $errors = null)
    {
        return new JsonResponse([
            'key'       => "BAD_REQUEST",
            'message'   => empty($message) ? config("messages.400") : $message,
            'errors'    => $errors,
            'timestamp' => now()->toDateTimeString()
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Validation Error Message
     */
    public static function validationErrorMessage($message = null, $errors = null)
    {
        return new JsonResponse([
            'key'       => "VALIDATION_ERROR",
            'message'   => empty($message) ? config("messages.invalid_input") : $message,
            'errors'    => $errors,
            'timestamp' => now()->toDateTimeString()
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Success Message
     */
    public static function successMessage($message = null, $results = null)
    {
        return new JsonResponse([
            'message'   => empty($message) ? config("messages.executed_successfully") : $message,
            'results'   => $results,
            'timestamp' => now()->toDateTimeString()
        ], Response::HTTP_OK);
    }
}