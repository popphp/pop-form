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
 * Form fieldset class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class Fieldset extends Child implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Form field elements
     * @var array
     */
    protected $fields = [];

    /**
     * Fieldset legend
     * @var string
     */
    protected $legend = null;

    /**
     * Constructor
     *
     * Instantiate the form fieldset object
     *
     * @param  array  $fields
     */
    public function __construct(array $fields = null)
    {
        parent::__construct('fieldset');
        if (null !== $fields) {
            $this->addFields($fields);
        }
    }

    /**
     * Method to create form fieldset object and fields from config
     *
     * @param  array  $config
     * @return Fieldset
     */
    public static function createFromConfig(array $config)
    {
        $fields = [];

        foreach ($config as $name => $field) {
            $fields[$name] = Field::create($name, $field);
        }

        return new self($fields);
    }

    /**
     * Method to add a form field
     *
     * @param  Element\AbstractElement $field
     * @return Fieldset
     */
    public function addField(Element\AbstractElement $field)
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * Method to add form fields
     *
     * @param  array $fields
     * @return Fieldset
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
        return $this;
    }

    /**
     * Method to get the count of elements in the form fieldset
     *
     * @return int
     */
    public function count()
    {
        return count($this->fields);
    }

    /**
     * Method to get the field values as an array
     *
     * @return array
     */
    public function toArray()
    {
        $fieldValues = [];

        foreach ($this->fields as $name => $field) {
            $fieldValues[$name] = $field->getValue();
        }

        return $fieldValues;
    }

    /**
     * Method to get fieldset legend
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return Element\AbstractElement
     */
    public function getField($name)
    {
        return (isset($this->fields[$name])) ? $this->fields[$name] : null;
    }

    /**
     * Method to get field element objects
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Method to get a field element value
     *
     * @param  string $name
     * @return mixed
     */
    public function getFieldValue($name)
    {
        return (isset($this->fields[$name])) ? $this->fields[$name]->getValue() : null;
    }

    /**
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return Fieldset
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Method to set a field element value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return Fieldset
     */
    public function setFieldValue($name, $value)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name]->setValue($value);
        }
        return $this;
    }

    /**
     * Method to set field element values
     *
     * @param  array $values
     * @return Fieldset
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
     * Prepare fieldset object for rendering
     *
     * @return Fieldset
     */
    public function prepare()
    {
        if (null !== $this->legend) {
            $this->addChild(new Child('legend', $this->legend));
        }

        $dl = new Child('dl');

        foreach ($this->fields as $field) {
            if (null !== $field->getLabel()) {
                $dt = new Child('dt');
                $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');

                $label = new Child('label', $field->getLabel());
                $label->setAttribute('for', $labelFor);
                if (null !== $field->getLabelAttributes()) {
                    $label->setAttributes($field->getLabelAttributes());
                }
                if ($field->isRequired()) {
                    $label->setAttribute('class', 'required');
                }
                $dt->addChild($label);
                $dl->addChild($dt);
            }

            $dd = new Child('dd');
            $dd->addChild($field);

            if (null !== $field->getHint()) {
                $hint = new Child('span', $field->getHint());
                if (null !== $field->getHintAttributes()) {
                    $hint->setAttributes($field->getHintAttributes());
                }
                $dd->addChild($hint);
            }

            $dl->addChild($dd);
        }

        $this->addChild($dl);

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
