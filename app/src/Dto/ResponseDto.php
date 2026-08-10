<?php

declare(strict_types=1);

namespace App\Dto;

class ResponseDto
{
    public function __construct(
        private bool $success = false,
        private ?string $message = 'Error message',
        private array $data = [],
        private array $trace = [],
        private array $previousTrace = [],
    ) {
    }

    public function toArray($showTrace = false): array
    {
        $result = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ($showTrace) {
            $result['trace'] = $this->trace;
            $result['previousTrace'] = $this->previousTrace;
        }

        return $result;
    }
}
