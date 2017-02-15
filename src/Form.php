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
     * Field groups
     * @var array
     */
    protected $groups = [];

    /**
     * Current field group
     * @var int
     */
    protected $current = 0;

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
        $action = ((null === $action) && isset($_SERVER['REQUEST_URI'])) ?
            $_SERVER['REQUEST_URI'] : '#';

        parent::__construct('form');
        $this->setAttributes([
            'action' => $action,
            'method' => $method
        ]);

        if (null !== $fields) {
            $this->addFields($fields);
        }
    }

    /**
     * Method to create form object and fields from config
     *
     * @param  array  $config
     * @param  string $action
     * @param  string $method
     * @return Form
     */
    public static function createFromConfig(array $config, $action = null, $method = 'post')
    {
        $fields = [];

        foreach ($config as $name => $field) {
            $fields[$name] = Field::create($name, $field);
        }

        return new self($fields, $action, $method);
    }

    /**
     * Method to add a form field
     *
     * @param  Element\AbstractElement $field
     * @return Form
     */
    public function addField(Element\AbstractElement $field)
    {
        if (!isset($this->groups[$this->current])) {
            $this->groups[$this->current] = new FieldGroup();
        }
        $this->groups[$this->current]->addField($field);
        return $this;
    }

    /**
     * Method to add form fields
     *
     * @param  array $fields
     * @return Form
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
        return $this;
    }

    /**
     * Method to add field group
     *
     * @param  FieldGroup $group
     * @return Form
     */
    public function addFieldGroup(FieldGroup $group)
    {
        $this->groups[] = $group;
        $this->current = count($this->groups) - 1;
        return $this;
    }

    /**
     * Method to get current field group
     *
     * @return FieldGroup
     */
    public function getFieldGroup()
    {
        return (isset($this->groups[$this->current])) ? $this->groups[$this->current] : null;
    }

    /**
     * Method to get current field group index
     *
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Method to get current field group index
     *
     * @param  int $i
     * @return Form
     */
    public function setCurrent($i)
    {
        if (!isset($this->groups[(int)$i])) {
            $this->groups[(int)$i] = new FieldGroup();
        }
        $this->current = (int)$i;
        return $this;
    }

    /**
     * Method to get the count of elements in the form
     *
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ($this->groups as $group) {
            $count += $group->count();
        }
        return $count;
    }

    /**
     * Method to get the field values as an array
     *
     * @return array
     */
    public function toArray()
    {
        $fieldValues = [];

        foreach ($this->groups as $group) {
            $fieldValues = array_merge($fieldValues, $group->toArray());
        }

        return $fieldValues;
    }

    /**
     * Get the form action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }
    /**
     * Get the form method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return Element\AbstractElement
     */
    public function getField($name)
    {
        return (isset($this->groups[$this->current])) ? $this->groups[$this->current]->getField($name) : null;
    }

    /**
     * Method to get field element objects
     *
     * @return array
     */
    public function getFields()
    {
        $fields = [];

        foreach ($this->groups as $group) {
            $fields = array_merge($fields, $group->getFields());
        }

        return $fields;
    }

    /**
     * Method to get a field element value
     *
     * @param  string $name
     * @return mixed
     */
    public function getFieldValue($name)
    {
        return (isset($this->groups[$this->current])) ? $this->groups[$this->current]->getFieldValue($name) : null;
    }

    /**
     * Method to set a field element value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return Form
     */
    public function setFieldValue($name, $value)
    {
        if (isset($this->groups[$this->current])) {
            $this->groups[$this->current]->setFieldValue($name, $value);
        }
        return $this;
    }

    /**
     * Method to set field element values
     *
     * @param  array $values
     * @return Form
     */
    public function setFieldValues(array $values)
    {
        foreach ($values as $name => $value) {
            $this->setFieldValue($name, $value);
        }
        return $this;
    }

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
     * Determine whether or not the form object is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $valid  = true;
        $fields = $this->getFields();

        // Check each element for validators, validate them and return the result.
        foreach ($fields as $field) {
            if ($field->validate() == false) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Get form element errors for a field.
     *
     * @param  string $name
     * @return array
     */
    public function getErrors($name)
    {
        $field  = $this->getField($name);
        $errors = (null !== $field) ? $field->getErrors() : [];

        return $errors;
    }

    /**
     * Get all form element errors
     *
     * @return array
     */
    public function getAllErrors()
    {
        $errors = [];
        $fields = $this->getFields();
        foreach ($fields as $name => $field) {
            if ($field->hasErrors()) {
                $errors[str_replace('[]', '', $field->getName())] = $field->getErrors();
            }
        }

        return $errors;
    }

    /**
     * Method to reset and clear any form field values
     *
     * @return Form
     */
    public function reset()
    {
        $fields = $this->getFields();
        foreach ($fields as $field) {
            $field->resetValue();
        }
        return $this;
    }

    /**
     * Method to clear any security tokens
     *
     * @return Form
     */
    public function clearTokens()
    {
        // Start a session.
        if (session_id() == '') {
            session_start();
        }
        if (isset($_SESSION['pop_csrf'])) {
            unset($_SESSION['pop_csrf']);
        }
        if (isset($_SESSION['pop_captcha'])) {
            unset($_SESSION['pop_captcha']);
        }

        return $this;
    }

    /**
     * Set method to set the property to the value of fields[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setFieldValue($name, $value);
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
        return $this->getFieldValue($name);
    }

    /**
     * Return the isset value of fields[$name]
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return (isset($this->groups[$this->current]) && (null !== $this->groups[$this->current][$name]));
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->groups[$this->current])) {
            $this->groups[$this->current][$name] = null;
            unset($this->groups[$this->current][$name]);
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
