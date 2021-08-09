<?php

namespace AkeneoEtl\Tests\Unit\Infrastructure\Command\Compare;

use AkeneoEtl\Infrastructure\Command\Compare\ConsoleTableFormatter;
use PHPUnit\Framework\TestCase;

class ConsoleTableFormatterTest extends TestCase
{
    public function test_it_formats_a_table()
    {
        $table = [
            ['0123456789', 'family',      'ziggy',           'pet'          ],
            ['0123456789', 'name',        '',                'Ziggy'        ],
            ['0123456789', 'description', 'Ziggy The Hydra', 'Ziggy The Pet'],
        ];

        $formatter = new ConsoleTableFormatter(12);

        $this->assertEquals([
            '0123456789 | family     | ziggy      | pet       ',
            '0123456789 | name       |            | Ziggy     ',
            '0123456789 | descriptio | Ziggy The  | Ziggy The ',
        ], $formatter->format($table));
    }
}
