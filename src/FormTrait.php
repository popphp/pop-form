<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Filter\FilterableTrait;
use ArrayIterator;

/**
 * Form trait
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */

trait FormTrait
{

    use FilterableTrait;

    /**
     * Count of values
     *
     * @return int
     */
    abstract public function count(): int;

    /**
     * Get values
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Method to iterate over the form elements
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Set method to set the property to the value of values[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    abstract public function __set(string $name, mixed $value): void;

    /**
     * Get method to return the value of values[$name]
     *
     * @param  string $name
     * @return mixed
     */
    abstract public function __get(string $name): mixed;

    /**
     * Return the isset value of values[$name]
     *
     * @param  string $name
     * @return bool
     */
    abstract public function __isset(string $name): bool;

    /**
     * Unset values[$name]
     *
     * @param  string $name
     * @return void
     */
    abstract public function __unset(string $name);

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }

}
