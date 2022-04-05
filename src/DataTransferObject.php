<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\ServiceProvider;

/**
 * DataTransferObject Classe que serve para transferência de dados entre o Service e o Controller
 *
 * @author Luis Gustavo Santarosa Pinto <bolota_xd@hotmail.com>
 *
 */
class DataTransferObject extends ServiceProvider
{
    private bool    $success;
    private array   $include;
    private bool    $index;
    private string  $message;
    private $data;
    private int     $internalCode;
    private int     $httpCode;

    public function __construct()
    {
        $this->success      = true;
        $this->include      = [];
        $this->index        = false;
        $this->message      = "";
        $this->data         = [];
        $this->internalCode = 2000;
        $this->httpCode     = 201;
    }

    /**
     * setSuccess
     *
     * @param  bool $value
     * @return void
     */
    public function setSuccess($value)
    {
        $this->success = $value;

        return $this;
    }

    /**
     * setInclude function
     *
     * @param array|null $value
     * @return DataTransferObject
     */
    public function setInclude(array $value): DataTransferObject
    {
        $this->include = $value;

        return $this;
    }

    /**
     * setIndex
     *
     * @param  bool $value
     * @return void
     */
    public function setIndex($value)
    {
        $this->index = $value;

        return $this;
    }

    /**
     * message
     *
     * @param  string $value
     * @return void
     */
    public function setMessage($value)
    {
        $this->message = $value;

        return $this;
    }

    /**
     * data
     *
     * @param  mixed $value
     * @return void
     */
    public function setData($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * success
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * include
     *
     * @return array
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * include
     *
     * @return bool
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * data
     *
     * @return $value
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * internalCode
     *
     * @return mixed
     */
    public function getInternalCode()
    {
        return $this->internalCode;
    }
    /**
     * internalCode
     *
     * @param  mixed $code
     * @return void
     */
    public function setInternalCode($code)
    {
        $this->internalCode = $code;

        return $this;
    }

    /**
     * HttpCode
     *
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
    /**
     * HttpCode
     *
     * @param  mixed $code
     * @return void
     */
    public function setHttpCode($code)
    {
        $this->httpCode = $code;

        return $this;
    }

    /**
     * successMessage
     *
     * @param  string $message
     * @param  mixed $data
     * @return void
     */
    public function successMessage($message, $data = null, $include = [])
    {
        $this->setMessage($message);
        $this->setData($data);
        $this->setInclude($include);

        return $this;
    }

    /**
     * ErrorMessage function
     *
     * @param string $message
     * @param $data
     * @param integer $internalCode
     *
     * @return self
     */
    public function errorMessage(string $message, $data = null, int $internalCode = 9000, $httpCode = 500): self
    {
        $this->setSuccess(false);
        $this->setMessage($message);
        $this->setInternalCode($internalCode);
        $this->setData($data);
        $this->setHttpCode($httpCode);

        return $this;
    }

    /**
     * GetMessageDTO function
     *
     * @return void
     */
    public function getMessageDTO()
    {
        $callback = [
            "success"      => $this->getSuccess(),
            "internalCode" => $this->getInternalCode(),
            "message"      => $this->getMessage(),
        ];

        if (count($this->getInclude()) > 0) {
            $callback = array_merge($callback, ["include" => $this->getInclude()]);
        }

        if (in_array(gettype($this->getData()), ["array"]) && count((array) $this->getData()) > 0) {
            $callback = array_merge($callback, [
                "data" => $this->getData()
            ]);
        }

        if (in_array(gettype($this->getData()), ["object"]) && !is_null($this->getData())) {
            $callback = array_merge($callback, [
                "data" => $this->getData()->toArray()
            ]);
        }

        if (in_array(gettype($this->getData()), ["string", "numeric", "integer"]) && !empty($this->getData())) {
            $callback = array_merge($callback, [
                "data" => $this->getData()
            ]);
        }

        return response()->json($callback, $this->getHttpCode());
    }
}
