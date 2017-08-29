<?php

namespace App\Exceptions;


class InvalidOperationException extends \Exception 
{
    protected $message;
    protected $status;

    public function __construct($message, $status = 422)
    {
        $this->message = $message;

        $this->status = $status;
    }
}