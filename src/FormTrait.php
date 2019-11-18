<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Filter\FilterableTrait;

/**
 * Form trait
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

trait FormTrait
{

    use FilterableTrait;

    /**
     * Count of values
     *
     * @return int
     */
    abstract public function count();

    /**
     * Get values
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Method to iterate over the form elements
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Set method to set the property to the value of values[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    abstract public function __set($name, $value);

    /**
     * Get method to return the value of values[$name]
     *
     * @param  string $name
     * @return mixed
     */
    abstract public function __get($name);

    /**
     * Return the isset value of values[$name]
     *
     * @param  string $name
     * @return boolean
     */
    abstract public function __isset($name);

    /**
     * Unset values[$name]
     *
     * @param  string $name
     * @return void
     */
    abstract public function __unset($name);

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
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
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

}