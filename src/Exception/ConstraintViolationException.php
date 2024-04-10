<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends HttpException
{

    public function __construct(int $statusCode, ConstraintViolationListInterface $errors, \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $detail = "Constraint violation error. ";
        foreach ($errors as $error) {
            // Affichage ou traitement de chaque erreur
            $detail .= $error->getPropertyPath() . ' : ' . $error->getMessage() . ". ";
        }
        parent::__construct($statusCode, $detail, $previous, $headers, $code);
    }
}