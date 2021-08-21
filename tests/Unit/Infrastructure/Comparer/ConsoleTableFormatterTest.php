<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Comparer;

use AkeneoE3\Infrastructure\Comparer\ConsoleTableFormatter;
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

        $formatter = new ConsoleTableFormatter([10, 9, 11, 12]);

        $this->assertEquals([
            '0123456789 | family    | ziggy       | pet         ',
            '0123456789 | name      |             | Ziggy       ',
            '0123456789 | descripti | Ziggy The H | Ziggy The Pe',
        ], $formatter->format($table));
    }
}
