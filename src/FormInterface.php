<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use ArrayIterator;

/**
 * Form interface class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.0
 */

interface FormInterface
{

    /**
     * Count of values
     *
     * @return int
     */
    public function count(): int;

    /**
     * Get values
     *
     * @param  array $options
     * @return array
     */
    public function toArray(array $options = []): array;

    /**
     * Method to iterate over object
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator;

    /**
     * Set method to set the property to the value of values[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void;

    /**
     * Get method to return the value of values[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed;

    /**
     * Return the isset value of values[$name]
     *
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool;

    /**
     * Unset values[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void;

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool;

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed;

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void;

}
