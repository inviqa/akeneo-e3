<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command\Compare;

final class ConsoleTableFormatter
{
    const COLUMN_SEPARATOR = ' | ';

    /**
     * @var int[]|array
     */
    private array $columnWidths;

    /**
     * @param int[]|array $columnWidths
     */
    public function __construct(array $columnWidths)
    {
        $this->columnWidths = $columnWidths;
    }

    /**
     * @return array|string[]
     */
    public function format(array $data): array
    {
        return array_map([$this, 'formatLine'], $data);
    }

    private function formatLine(array $line): string
    {
        $columnWidths = $this->columnWidths;

        array_walk($line, function(&$item, $key) use ($columnWidths) {
            $item = str_pad(substr($item, 0, $columnWidths[$key]), $columnWidths[$key]);
        });

        return implode(self::COLUMN_SEPARATOR, $line);
    }
}
