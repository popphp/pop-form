<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */

class Form extends Child implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Field fieldsets
     * @var array
     */
    protected $fieldsets = [];

    /**
     * Current field fieldset
     * @var int
     */
    protected $current = 0;

    /**
     * Filters
     * @var array
     */
    protected $filters = [];

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
        if (null === $action) {
            $action = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '#';
        }

        parent::__construct('form');
        $this->setAction($action);
        $this->setMethod($method);

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
        $form = new static(null, $action, $method);
        $form->addFieldsFromConfig($config);
        return $form;
    }

    /**
     * Method to create form object and fields from config
     *
     * @param  array  $config
     * @param  string $container
     * @param  string $action
     * @param  string $method
     * @return Form
     */
    public static function createFromFieldsetConfig(array $config, $container = null, $action = null, $method = 'post')
    {
        $form = new static(null, $action, $method);
        $form->addFieldsetsFromConfig($config, $container);
        return $form;
    }

    /**
     * Method to create a new fieldset object
     *
     * @param  string  $legend
     * @param  string  $container
     * @return Fieldset
     */
    public function createFieldset($legend = null, $container = null)
    {
        $fieldset = new Fieldset();
        if (null !== $legend) {
            $fieldset->setLegend($legend);
        }
        if (null !== $container) {
            $fieldset->setContainer($container);
        }

        $this->addFieldset($fieldset);

        $id = (null !== $this->getAttribute('id')) ?
            $this->getAttribute('id') . '-fieldset-' . ($this->current + 1) : 'pop-form-fieldset-' . ($this->current + 1);

        $class = (null !== $this->getAttribute('class')) ?
            $this->getAttribute('id') . '-fieldset' : 'pop-form-fieldset';

        $fieldset->setAttribute('id', $id);
        $fieldset->setAttribute('class', $class);

        return $fieldset;
    }

    /**
     * Method to set action
     *
     * @param  string $action
     * @return Form
     */
    public function setAction($action)
    {
        $this->setAttribute('action', $action);
        return $this;
    }

    /**
     * Method to set method
     *
     * @param  string $method
     * @return Form
     */
    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
        return $this;
    }

    /**
     * Method to get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * Method to get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * Method to add fieldset
     *
     * @param  Fieldset $fieldset
     * @return Form
     */
    public function addFieldset(Fieldset $fieldset)
    {
        $this->fieldsets[] = $fieldset;
        $this->current     = count($this->fieldsets) - 1;
        return $this;
    }

    /**
     * Method to remove fieldset
     *
     * @param  int $i
     * @return Form
     */
    public function removeFieldset($i)
    {
        if (isset($this->fieldsets[(int)$i])) {
            unset($this->fieldsets[(int)$i]);
        }
        $this->fieldsets = array_values($this->fieldsets);
        if (!isset($this->fieldsets[$this->current])) {
            $this->current = (count($this->fieldsets) > 0) ? count($this->fieldsets) - 1 : 0;
        }
        return $this;
    }

    /**
     * Method to get current fieldset
     *
     * @return Fieldset
     */
    public function getFieldset()
    {
        return (isset($this->fieldsets[$this->current])) ? $this->fieldsets[$this->current] : null;
    }

    /**
     * Method to get current fieldset index
     *
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Method to get current fieldset index
     *
     * @param  int $i
     * @return Form
     */
    public function setCurrent($i)
    {
        $this->current = (int)$i;
        if (!isset($this->fieldsets[$this->current])) {
            $this->fieldsets[$this->current] = $this->createFieldset();
        }
        return $this;
    }

    /**
     * Method to get the legend of the current fieldset
     *
     * @return string
     */
    public function getLegend()
    {
        return (isset($this->fieldsets[$this->current])) ?
            $this->fieldsets[$this->current]->getLegend() : null;
    }

    /**
     * Method to set the legend of the current fieldset
     *
     * @param  string $legend
     * @return Form
     */
    public function setLegend($legend)
    {
        if (isset($this->fieldsets[$this->current])) {
            $this->fieldsets[$this->current]->setLegend($legend);
        }
        return $this;
    }

    /**
     * Method to add a form field
     *
     * @param  Element\AbstractElement $field
     * @return Form
     */
    public function addField(Element\AbstractElement $field)
    {
        if (count($this->fieldsets) == 0) {
            $this->createFieldset();
        }
        $this->fieldsets[$this->current]->addField($field);
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
     * Method to add a form field from a config
     *
     * @param  string $name
     * @param  array  $field
     * @return Form
     */
    public function addFieldFromConfig($name, $field)
    {
        $this->addField(Fields::create($name, $field));
        return $this;
    }

    /**
     * Method to add form fields from config
     *
     * @param  array $config
     * @return Form
     */
    public function addFieldsFromConfig(array $config)
    {
        $fields = [];

        foreach ($config as $name => $field) {
            $fields[$name] = Fields::create($name, $field);
        }

        $this->addFields($fields);

        return $this;
    }

    /**
     * Method to add form fieldsets from config
     *
     * @param  array  $fieldsets
     * @param  string $container
     * @return Form
     */
    public function addFieldsetsFromConfig(array $fieldsets, $container = null)
    {
        foreach ($fieldsets as $legend => $config) {
            if (!is_numeric($legend)) {
                $this->createFieldset($legend, $container);
            } else {
                $this->createFieldset(null, $container);
            }
            $this->addFieldsFromConfig($config);
        }

        return $this;
    }

    /**
     * Method to insert a field before another one
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $field
     * @return Form
     */
    public function insertFieldBefore($name, Element\AbstractElement $field)
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($name)) {
                $fieldset->insertFieldBefore($name, $field);
                break;
            }
        }
        return $this;
    }

    /**
     * Method to insert a field after another one
     *
     * @param  string                  $name
     * @param  Element\AbstractElement $field
     * @return Form
     */
    public function insertFieldAfter($name, Element\AbstractElement $field)
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($name)) {
                $fieldset->insertFieldAfter($name, $field);
                break;
            }
        }
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
        foreach ($this->fieldsets as $fieldset) {
            $count += $fieldset->count();
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

        foreach ($this->fieldsets as $fieldset) {
            $fieldValues = array_merge($fieldValues, $fieldset->toArray());
        }

        return $fieldValues;
    }

    /**
     * Method to get a field element object
     *
     * @param  string $name
     * @return Element\AbstractElement
     */
    public function getField($name)
    {
        $namedField = null;
        $fields     = $this->getFields();

        foreach ($fields as $field) {
            if ($field->getName() == $name) {
                $namedField = $field;
                break;
            }
        }

        return $namedField;
    }

    /**
     * Method to get field element objects
     *
     * @return array
     */
    public function getFields()
    {
        $fields = [];

        foreach ($this->fieldsets as $fieldset) {
            $fields = array_merge($fields, $fieldset->getFields());
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
        $fieldValues = $this->toArray();
        return (isset($fieldValues[$name])) ? $fieldValues[$name] : null;
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
        foreach ($this->fieldsets as $fieldset) {
            if (isset($fieldset[$name])) {
                $fieldset[$name] = $value;
            }
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

        $this->filterValues();

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
     * Add filter
     *
     * @param  mixed $call
     * @param  mixed $params
     * @param  mixed $excludeByType
     * @param  mixed $excludeByName
     * @return Form
     */
    public function addFilter($call, $params = null, $excludeByType = null, $excludeByName = null)
    {
        if (null !== $params) {
            if (!is_array($params)) {
                $params = [$params];
            }
        } else {
            $params = [];
        }

        if (null !== $excludeByType) {
            if (!is_array($excludeByType)) {
                $excludeByType = [$excludeByType];
            }
        } else {
            $excludeByType = [];
        }

        if (null !== $excludeByName) {
            if (!is_array($excludeByName)) {
                $excludeByName = [$excludeByName];
            }
        } else {
            $excludeByName = [];
        }

        $this->filters[] = [
            'call'          => $call,
            'params'        => $params,
            'excludeByType' => $excludeByType,
            'excludeByName' => $excludeByName
        ];

        return $this;
    }

    /**
     * Clear filters
     *
     * @return Form
     */
    public function clearFilters()
    {
        $this->filters = [];
        return $this;
    }

    /**
     * Filter value with the filters in the form object
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filterValue($value)
    {
        if ($value instanceof Element\AbstractElement) {
            $name      = $value->getName();
            $type      = $value->getType();
            $realValue = $value->getValue();
        } else {
            $type      = null;
            $name      = null;
            $realValue = $value;
        }

        foreach ($this->filters as $filter) {
            if (((null === $type) || (!in_array($type, $filter['excludeByType']))) &&
                ((null === $name) || (!in_array($name, $filter['excludeByName'])))) {
                if (is_array($realValue)) {
                    foreach ($realValue as $k => $v) {
                        $params        = array_merge([$v], $filter['params']);
                        $realValue[$k] = call_user_func_array($filter['call'], $params);
                    }
                } else {
                    $params    = array_merge([$realValue], $filter['params']);
                    $realValue = call_user_func_array($filter['call'], $params);
                }
            }
        }

        if ($value instanceof Element\AbstractElement) {
            $value->setValue($realValue);
        }

        return $realValue;
    }

    /**
     * Filter values with the filters in the form object
     *
     * @param  array $values
     * @return mixed
     */
    public function filterValues(array $values = null)
    {
        if (null === $values) {
            $values = $this->getFields();
        }
        foreach ($values as $key => $value) {
            $values[$key] = $this->filterValue($value);
        }
        return $values;
    }

    /**
     * Determine whether or not the form object is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $result = true;
        $fields = $this->getFields();

        // Check each element for validators, validate them and return the result.
        foreach ($fields as $field) {
            if ($field->validate() == false) {
                $result = false;
            }
        }

        return $result;
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
     * Prepare form object for rendering
     *
     * @return Form
     */
    public function prepare()
    {
        if (null === $this->getAttribute('id')) {
            $this->setAttribute('id', 'pop-form');
        }
        if (null === $this->getAttribute('class')) {
            $this->setAttribute('class', 'pop-form');
        }

        foreach ($this->fieldsets as $fieldset) {
            $fieldset->prepare();
            $this->addChild($fieldset);
        }

        return $this;
    }

    /**
     * Prepare form object for rendering with a view
     *
     * @return array
     */
    public function prepareForView()
    {
        $formData = [];

        foreach ($this->fieldsets as $fieldset) {
            $formData = array_merge($formData, $fieldset->prepareForView());
        }

        return $formData;
    }

    /**
     * Render the form object
     *
     * @param  int     $depth
     * @param  string  $indent
     * @return mixed
     */
    public function render($depth = 0, $indent = null)
    {
        if (!($this->hasChildren())) {
            $this->prepare();
        }

        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->getFields() as $field) {
                if ($field instanceof Element\Input\File) {
                    $this->setAttribute('enctype', 'multipart/form-data');
                    break;
                }
            }
        }

        return parent::render($depth, $indent);
    }

    /**
     * Render and return the form object as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
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
        $fieldValues = $this->toArray();
        return isset($fieldValues[$name]);
    }

    /**
     * Unset fields[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $fieldValues = $this->toArray();
        if (isset($fieldValues[$name])) {
            $this->getField($name)->resetValue();
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
