<?php

namespace App\Infrastructure\Loader;

class LoaderError
{
    private array $data;

    public function __construct(array $response)
    {
        $this->data = $response;
    }

    public function getIdentifier(): string
    {
        return $this->data['identifier'] ?? '?';
    }

    public function getErrorMessage(): string
    {
        $messages = [$this->data['message'] ?? ''];

        foreach ($this->data['errors'] ?? [] as $error) {
            $messages[] = sprintf(' - property: %s, attribute: %s, %s',
                $error['property'] ?? '?',
                $error['attribute'] ?? '?',
                $error['message'] ?? '?',
            );
        }

        return implode(PHP_EOL, $messages);
    }

}
