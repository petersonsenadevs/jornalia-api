<?php

namespace App\Exceptions;

use Exception;

class PdfGenerateException extends Exception
{
    public function __construct()
    {
        parent::__construct('Error generating PDF', 500);
    }
}
