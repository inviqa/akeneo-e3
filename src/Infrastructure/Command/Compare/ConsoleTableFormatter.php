<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command\Compare;

final class ConsoleTableFormatter
{
    const COLUMN_SEPARATOR = ' | ';

    private int $columnWidth;

    public function __construct(int $columnWidth)
    {
        $this->columnWidth = $columnWidth;
    }

    public function format(array $data): array
    {
        return array_map([$this, 'formatLine'], $data);
    }

    private function formatLine(array $line): string
    {
        $columnWidth = $this->columnWidth;

        $line = array_map(function($item) use ($columnWidth) {
            return str_pad(substr($item, 0, $columnWidth-2), $columnWidth-2);

        }, $line);

        return implode(self::COLUMN_SEPARATOR, $line);
    }
}
