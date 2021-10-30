<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\ServiceProvider;

/**
 * ReturnPrepare Serve para responder requisições no Controller
 * Nota: baseada na Classe DataPrepare (Utilizada na API do ERP)
 *
 */
class ReturnPrepare extends ServiceProvider
{
    public static function getMessageDTO(DataTransferObject $dto, $http_code)
    {
        return self::getMessage($dto->getSuccess(), $dto->getInclude(), $dto->getIndex(), $dto->getMessage(), $http_code, null, $dto->getData(), $dto->getErrors());
    }

    private static function getMessage($success, $include = [], $index = false, $message, $code, $params = [], $data = [], $errors = null)
    {
        $retArr = array(
            "success"   => $success,
            "code"      => $code,
            "message"   => $message,
        );

        if (!is_null($include)) {
            $retArr["include"] = $include;
        }

        if (!is_null($errors)) {
            $retArr["errors"] = $errors;
        }

        $data = gettype($data) != 'array' ? [$data] : $data;

        if ($index) {
            return array_merge($retArr, $data);
        }
        
        $retArr['data'] = $data;

        if (empty($retArr["data"])) {
            unset($retArr["data"]); 
        }

        return $retArr;
    }

    public static function successMessage($message, $code, $params = [], $data = [])
    {
        return self::getMessage(
            true,
            [],
            false,
            $message,
            $code,
            $params,
            $data
        );
    }

    public static function errorMessage($message, $code, $params = [], $data = [])
    {
        return self::getMessage(
            false,
            [],
            false,
            $message,
            $code,
            $params,
            $data
        );
    }
}
