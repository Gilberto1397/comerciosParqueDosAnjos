<?php
declare(strict_types=1);
namespace App\Exceptions;

final class ValidationException extends \RuntimeException
{
    public function __construct(private array $errors)
    {
        parent::__construct('Dados inválidos.');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
