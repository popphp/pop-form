<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */

class Fieldset extends Child implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Form field elements
     * @var array
     */
    protected $fields = [];

    /**
     * Current field group
     * @var int
     */
    protected $current = 0;

    /**
     * Fieldset legend
     * @var string
     */
    protected $legend = null;

    /**
     * Fieldset container (dl, table, div or p)
     * @var string
     */
    protected $container = 'dl';

    /**
     * Constructor
     *
     * Instantiate the form fieldset object
     *
     * @param  array  $fields
     * @param  string $container
     */
    public function __construct(array $fields = null, $container = 'dl')
    {
        parent::__construct('fieldset');
        $this->setContainer($container);
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
            $fields[$name] = Fields::create($name, $field);
        }

        return new static($fields);
    }

    /**
     * Method to set container
     *
     * @param  string $container
     * @return Fieldset
     */
    public function setContainer($container)
    {
        $this->container = strtolower($container);
        return $this;
    }

    /**
     * Method to get current group index
     *
     * @param  int $i
     * @return Fieldset
     */
    public function setCurrent($i)
    {
        $this->current = (int)$i;
        if (!isset($this->fields[$this->current])) {
            $this->fields[$this->current] = [];
        }
        return $this;
    }

    /**
     * Method to create new group
     *
     * @return Fieldset
     */
    public function createGroup()
    {
        $this->current++;
        $this->fields[$this->current] = [];
        return $this;
    }

    /**
     * Method to add a form field
     *
     * @param  Element\AbstractElement $field
     * @return Fieldset
     */
    public function addField(Element\AbstractElement $field)
    {
        if (!isset($this->fields[$this->current])) {
            $this->fields[$this->current] = [];
        }
        $this->fields[$this->current][$field->getName()] = $field;
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
     * Method to insert a field before another one
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $field
     * @return Fieldset
     */
    public function insertFieldBefore($name, Element\AbstractElement $field)
    {
        foreach ($this->fields as $i => $group) {
            $fields = [];
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    $fields[$field->getName()] = $field;
                    $fields[$key] = $value;
                } else {
                    $fields[$key] = $value;
                }
            }
            $this->fields[$i] = $fields;
        }

        return $this;
    }

    /**
     * Method to insert a field after another one
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $field
     * @return Fieldset
     */
    public function insertFieldAfter($name, Element\AbstractElement $field)
    {
        foreach ($this->fields as $i => $group) {
            $fields = [];
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    $fields[$key] = $value;
                    $fields[$field->getName()] = $field;
                } else {
                    $fields[$key] = $value;
                }
            }
            $this->fields[$i] = $fields;
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
        $count = 0;
        foreach ($this->fields as $group) {
            $count += count($group);
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

        foreach ($this->fields as $group) {
            foreach ($group as $name => $field) {
                $fieldValues[$name] = $field->getValue();
            }
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
     * Method to get container
     *
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Method to get current group index
     *
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Method to determine if the fieldset has a field
     *
     * @param  string $name
     * @return boolean
     */
    public function hasField($name)
    {
        $result = false;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return Element\AbstractElement
     */
    public function getField($name)
    {
        $result = null;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = $fields[$name];
            }
        }
        return $result;
    }

    /**
     * Method to get field element objects in a group
     *
     * @param  int  $i
     * @return array
     */
    public function getFields($i)
    {
        return (isset($this->fields[$i])) ? $this->fields[$i] : null;
    }

    /**
     * Method to get all field element groups
     *
     * @return array
     */
    public function getFieldGroups()
    {
        return $this->fields;
    }

    /**
     * Method to get all field elements
     *
     * @return array
     */
    public function getAllFields()
    {
        $fields = [];
        foreach ($this->fields as $group) {
            foreach ($group as $field) {
                $fields[$field->getName()] = $field;
            }
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
        $result = null;
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $result = $this->fields[$key][$name]->getValue();
            }
        }
        return $result;
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
        foreach ($this->fields as $key => $fields) {
            if (isset($fields[$name])) {
                $this->fields[$key][$name]->setValue($value);
            }
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
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }

        switch ($this->container) {
            case 'table':
                $this->prepareTable();
                break;
            case 'dl':
                $this->prepareDl();
                break;
            default:
                $this->prepareElement($this->container);
        }

        return $this;
    }

    /**
     * Prepare fieldset elements for rendering with a view
     *
     * @return array
     */
    public function prepareForView()
    {
        $fields = [];

        foreach ($this->fields as $groups) {
            foreach ($groups as $field) {
                if (null !== $field->getLabel()) {
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');
                    $label    = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if (null !== $field->getLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $fields[$field->getName() . '_label'] = $label->render();
                }

                if (null !== $field->getHint()) {
                    $hint = new Child('span', $field->getHint());
                    if (null !== $field->getHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $fields[$field->getName() . '_hint'] = $hint->render();
                }

                if ($field->hasErrors()) {
                    $fields[$field->getName() . '_errors'] = $field->getErrors();
                }

                $fields[$field->getName()] = $field->render();
            }
        }

        return $fields;
    }

    /**
     * Prepare table
     *
     * @return void
     */
    protected  function prepareTable()
    {
        foreach ($this->fields as $fields) {
            $table = new Child('table');

            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                $tr = new Child('tr');
                if (null !== $field->getLabel()) {
                    $td = new Child('td');
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');

                    $label = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if (null !== $field->getLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $td->addChild($label);
                    $tr->addChild($td);
                }

                $td = new Child('td');
                if ($field->isErrorPre()) {
                    $td->addChildren($errors);
                }
                $td->addChild($field);

                if (null !== $field->getHint()) {
                    $hint = new Child('span', $field->getHint());
                    if (null !== $field->getHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $td->addChild($hint);
                }

                if (null === $field->getLabel()) {
                    $td->setAttribute('colspan', 2);
                }
                if (!$field->isErrorPre()) {
                    $td->addChildren($errors);
                }
                $tr->addChild($td);
                $table->addChild($tr);
            }

            $this->addChild($table);
        }
    }

    /**
     * Prepare DIV or P
     *
     * @param  string $element
     * @return void
     */
    protected  function prepareElement($element)
    {
        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                $container = new Child($element);
                if (null !== $field->getLabel()) {
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');
                    $label    = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if (null !== $field->getLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $container->addChild($label);
                }

                if ($field->isErrorPre()) {
                    $container->addChildren($errors);
                }
                $container->addChild($field);

                if (null !== $field->getHint()) {
                    $hint = new Child('span', $field->getHint());
                    if (null !== $field->getHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $container->addChild($hint);
                }
                if (!$field->isErrorPre()) {
                    $container->addChildren($errors);
                }
                $this->addChild($container);
            }
        }
    }

    /**
     * Prepare DL
     *
     * @return void
     */
    protected  function prepareDl()
    {
        foreach ($this->fields as $fields) {
            $dl = new Child('dl');

            foreach ($fields as $field) {
                $errors = [];
                if ($field->hasErrors()) {
                    foreach ($field->getErrors() as $error) {
                        $errors[] = (new Child('div', $error))->setAttribute('class', 'error');
                    }
                }

                if (null !== $field->getLabel()) {
                    $dt = new Child('dt');
                    $labelFor = $field->getName() . (($field->getNodeName() == 'fieldset') ? '1' : '');

                    $label = new Child('label', $field->getLabel());
                    $label->setAttribute('for', $labelFor);
                    if (null !== $field->getLabelAttributes()) {
                        $label->setAttributes($field->getLabelAttributes());
                    }
                    if ($field->isRequired()) {
                        if ($label->hasAttribute('class')) {
                            $label->setAttribute('class', $label->getAttribute('class') . ' required');
                        } else {
                            $label->setAttribute('class', 'required');
                        }
                    }
                    $dt->addChild($label);
                    $dl->addChild($dt);
                }

                $dd = new Child('dd');
                if ($field->isErrorPre()) {
                    $dd->addChildren($errors);
                }
                $dd->addChild($field);

                if (null !== $field->getHint()) {
                    $hint = new Child('span', $field->getHint());
                    if (null !== $field->getHintAttributes()) {
                        $hint->setAttributes($field->getHintAttributes());
                    }
                    $dd->addChild($hint);
                }
                if (!$field->isErrorPre()) {
                    $dd->addChildren($errors);
                }
                $dl->addChild($dd);
            }

            $this->addChild($dl);
        }
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
        return $this->hasField($name);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        foreach ($this->fields as $i => $group) {
            foreach ($group as $key => $value) {
                if ($key == $name) {
                    unset($this->fields[$i][$key]);
                }
            }
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
