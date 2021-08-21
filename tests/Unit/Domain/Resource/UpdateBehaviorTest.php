<?php

namespace AkeneoE3\Tests\Unit\Domain\Resource;

use AkeneoE3\Domain\Exception\TransformException;
use AkeneoE3\Domain\Resource\UpdateBehavior;
use PHPUnit\Framework\TestCase;

/**
 * Test for Update Behavior rules
 *
 * @see https://api.akeneo.com/documentation/update.html#update-behavior
 *
 * Rules:
 *      Rule 1: If the value is an object, it will be merged with the old
 *     value.
 *      Rule 2: If the value is not an object, it will replace the old value.
 *      Rule 3: For non-scalar values (objects and arrays) data types must
 *     match. Rule 4: Any data in non specified properties will be left
 *     untouched.
 *
 * Implementation:
 *      Rule 4: not implemented - not possible by design of the class
 */
class UpdateBehaviorTest extends TestCase
{
    /**
     * @testdox Rule 1: If the value is an object, it will be merged with the
     *     old value (add new element)
     */
    public function test_rule_1_add_new_element()
    {
        $original = [
            'labels' => [
                'en_US' => 'Boots',
                'fr_FR' => 'Bottes',
            ],
        ];

        $patch = [
            'de_DE' => 'Stiefel',
        ];

        $expected = [
            'labels' => [
                'en_US' => 'Boots',
                'fr_FR' => 'Bottes',
                'de_DE' => 'Stiefel',
            ],
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('labels', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 1: If the value is an object, it will be merged with the
     *     old value (update existing element)
     */
    public function test_rule_1_update_existing_element()
    {
        $original = [
            'labels' => [
                'en_US' => 'Boots',
                'fr_FR' => 'Bottes',
            ],
        ];

        $patch = [
            'en_US' => 'Gumboots',
        ];

        $expected = [
            'labels' => [
                'en_US' => 'Gumboots',
                'fr_FR' => 'Bottes',
            ],
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('labels', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 1: If the value is an object, it will be merged with the
     *     old value (set element that did not exist)
     */
    public function test_rule_1_set_element_that_did_not_exist()
    {
        $original = [];

        $patch = [
            'en_US' => 'Boots',
            'fr_FR' => 'Bottes',
        ];

        $expected = [
            'labels' => [
                'en_US' => 'Boots',
                'fr_FR' => 'Bottes',
            ],
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('labels', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 2: If the value is not an object, it will replace the old
     *     value (replace scalar value).
     */
    public function test_rule_2_replace_old_scalar_value()
    {
        $original = [
            'parent' => 'master',
        ];

        $patch = 'clothes';

        $expected = [
            'parent' => 'clothes',
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('parent', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 2: If the value is not an object, it will replace the old
     *     value (replace array value).
     */
    public function test_rule_2_replace_old_array_value()
    {
        $original = [
            'categories' => ['shoes', 'boots'],
        ];

        $patch = ['boots'];

        $expected = [
            'categories' => ['boots'],
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('categories', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 2: If the value is not an object, it will replace the old
     *     value (set value that did not exist).
     */
    public function test_rule_2_set_value_that_did_not_exist()
    {
        $original = [];

        $patch = ['boots'];

        $expected = [
            'categories' => ['boots'],
        ];

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('categories', $patch);

        $this->assertEquals($expected, $original);
    }

    /**
     * @testdox Rule 3: For non-scalar values (objects and arrays) data types
     *     must match.
     */
    public function test_rule_3_throw_an_exception_if_types_mismatch()
    {
        $original = [
            'categories' => ['boots'],
        ];

        $patch = 'boots';

        $this->expectException(TransformException::class);

        $updateBehavior = new UpdateBehavior($original);

        $updateBehavior->patch('categories', $patch);
    }
}
