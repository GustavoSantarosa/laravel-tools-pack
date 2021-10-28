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
    private static $_forbidenNames = ['code', 'msg', 'data', 'success'];

    public static function getMessageDTO(DataTransferObject $dto, $http_code)
    {
        return self::getMessage($dto->getSuccess(), $dto->getInclude(), $dto->getIndex(), $dto->getMessage(), $http_code, null, $dto->getData(), $dto->getErrors());
    }

    private static function getMessage($success, $include = [], $index = false, $message, $code, $params = [], $data = [], $errors = null)
    {
        $retArr = array(
            "success" => $success,
            "code" => $code,
            "msg" => $message,
            "message" => $message,
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

        if (isset($data[0]) && count($data) > 1) {

            $retArr = array_merge($retArr, [
                "totalindata" => count($data),
            ]);
        }
        
        $retArr['data'] = $data;
        
        if (is_array($params)) {
            foreach ($params as $name => $value) {
                if (!in_array($value, self::$_forbidenNames)) {
                    $retArr[] = $value;
                }
            }
        }

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

    public static function makeMessage($success, $message, $code, $data = null)
    {
        $retArr = array(
            "success" => $success,
            "code" => $code,
            "msg" => $message,
            "message" => $message,
        );

        if (isset($data)) {
            $retArr['data'] = $data;
        }

        return (object) $retArr;
    }
}
