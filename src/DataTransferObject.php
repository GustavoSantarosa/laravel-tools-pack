<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\ServiceProvider;

/**
 * DataTransferObject Classe que serve para transferência de dados entre o Service e o Controller
 *
 */
class DataTransferObject extends ServiceProvider
{    
    /**
     * success
     *
     * @var bool
     */
    private $success;

    /**
     * include
     *
     * @var array
     */
    private $include;

    /**
     * index
     *
     * @var bool
     */
    private $index;

    /**
     * message
     *
     * @var string
     */
    private $message;
    
    /**
     * data
     *
     * @var mixed
     */
    private $data;

    /**
     * errors
     *
     * @var mixed
     */
    private $errors;

    public function __construct()
    {
        $this->success = false;
        $this->include = [];
        $this->index = false;
        $this->message = "";
        $this->data = null;
        $this->errors = null;
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
    }

    /**
     * setInclude
     *
     * @param  array $value
     * @return void
     */
    public function setInclude($value)
    {
        $this->include = $value;
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
    }

    /**
     * errors
     *
     * @param  mixed $value
     * @return void
     */
    public function setErrors($value)
    {
        $this->errors = $value;
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
     * @return bool
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
     * @return mixed $value
     */
    public function getData()
    {
        return $this->data;
    }        

     /**
     * errors
     *
     * @return mixed $value
     */
    public function getErrors()
    {
        return $this->errors;
    }            

    /**
     * successMessage
     *
     * @param  string $message
     * @param  mixed $data
     * @return void
     */
    public function successMessage($message,$data = null){
        $this->setSuccess(true);
        $this->setMessage($message); 
        $this->setData($data);
    }

    /**
     * errorMessage
     *
     * @param  string $message
     * @param  mixed $data
     * @param  mixed $error
     * @return void
     */
    public function errorMessage($message,$data = null,$errors=null){
        $this->setSuccess(false);
        $this->setMessage($message); 
        $this->setData($data);
        $this->setErrors($errors);
    }
}