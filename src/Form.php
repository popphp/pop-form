<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Dom\Child;
use Pop\Form\Element;

/**
 * Form class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class Form extends Child implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Form field elements
     * @var array
     */
    protected $fields = [];

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     */
    public function __construct(array $fields = null, $action = null, $method = 'post')
    {
        if ((null === $action) && isset($_SERVER['REQUEST_URI'])) {
            $action = $_SERVER['REQUEST_URI'];
        }

        parent::__construct('form');
        $this->setAttributes([
            'action' => $action,
            'method' => $method
        ]);
    }

    /**
     * Method to get the count of elements in the formt
     *
     * @return int
     */
    public function count()
    {
        return count($this->fields);
    }

    /**
     * Method to iterate over the form elements
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Set method to set the property to the value of fields[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if (!($value instanceof Element\AbstractElement)) {
            throw new Exception('Error: The form field must be an instance of Pop\Form\AbstractElement');
        }
        $this->fields[$name] = $value;
    }

    /**
     * Get method to return the value of fields[$name]
     *
     * @param  string $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name)
    {
        return (isset($this->fields[$name])) ? $this->fields[$name] : null;
    }

    /**
     * Return the isset value of fields[$name]
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name] = null;
            unset($this->fields[$name]);
        }
    }

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
