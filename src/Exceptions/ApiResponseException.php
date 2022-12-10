<?php

namespace GustavoSantarosa\LaravelToolPack\Exceptions;

use Exception;

class ApiResponseException extends Exception
{
    private array $apiResponse = [];

    public function __construct($message = '', $code = 0, $previous = null, array $apiResponse = [])
    {
        $this->apiResponse = $apiResponse;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the value of apiResponse.
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }
}
