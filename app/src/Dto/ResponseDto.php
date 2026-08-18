<?php

declare(strict_types=1);

namespace App\Dto;

class ResponseDto
{
    public const string MESSAGE = 'message';
    public const string SUCCESS = 'success';
    public const string DATA = 'data';

    public function __construct(
        private bool $success = false,
        private ?string $message = 'Error message',
        private array $data = [],
        private array $trace = [],
        private array $previousTrace = [],
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): static
    {
        $this->success = $success;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function toArray($showTrace = false): array
    {
        $result = [
            self::SUCCESS => $this->success,
            self::MESSAGE => $this->message,
            self::DATA => $this->data,
        ];

        if ($showTrace) {
            $result['trace'] = $this->trace;
            $result['previousTrace'] = $this->previousTrace;
        }

        return $result;
    }
}
