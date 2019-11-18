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

use Pop\Dom\Child;
use Pop\Form\Element;

/**
 * Form class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Form extends Child implements FormInterface, \ArrayAccess, \Countable, \IteratorAggregate
{

    /**
     * Trait declaration
     */
    use FormTrait;

    /**
     * Field fieldsets
     * @var array
     */
    protected $fieldsets = [];

    /**
     * Form columns
     * @var array
     */
    protected $columns = [];

    /**
     * Current field fieldset
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
        $this->setAttribute('action', str_replace(['?captcha=1', '&captcha=1'], ['', ''], $action));
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
     * Method to set an attribute
     *
     * @param  string $a
     * @param  string $v
     * @return Form
     */
    public function setAttribute($a, $v)
    {
        parent::setAttribute($a, $v);

        if ($a == 'id') {
            foreach ($this->fieldsets as $i => $fieldset) {
                $id = $v . '-fieldset-' . ($i + 1);
                $fieldset->setAttribute('id', $id);
            }

        } else if ($a == 'class') {
            foreach ($this->fieldsets as $i => $fieldset) {
                $class = $v . '-fieldset';
                $fieldset->setAttribute('class', $class);
            }
        }

        return $this;
    }

    /**
     * Method to set attributes
     *
     * @param  array $a
     * @return Form
     */
    public function setAttributes(array $a)
    {
        foreach ($a as $name => $value) {
            $this->setAttribute($name, $value);
        }
        return $this;
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
     * Method to add form column
     *
     * @param  mixed  $fieldsets
     * @param  string $class
     * @return Form
     */
    public function addColumn($fieldsets, $class = null)
    {
        if (!is_array($fieldsets)) {
            $fieldsets = [$fieldsets];
        }

        foreach ($fieldsets as $i => $num) {
            $fieldsets[$i] = (int)$num - 1;
        }

        if (null === $class) {
            $class = 'pop-form-column-' . (count($this->columns) + 1);
        }

        $this->columns[$class] = $fieldsets;
        return $this;
    }

    /**
     * Method to determine if form has a column
     *
     * @param  string $class
     * @return boolean
     */
    public function hasColumn($class)
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        return isset($this->columns[$class]);
    }

    /**
     * Method to get form column
     *
     * @param  string $class
     * @return array
     */
    public function getColumn($class)
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        return (isset($this->columns[$class])) ? $this->columns[$class] : null;
    }

    /**
     * Method to remove form column
     *
     * @param  string $class
     * @return Form
     */
    public function removeColumn($class)
    {
        if (is_numeric($class)) {
            $class = 'pop-form-column-' . $class;
        }

        if (isset($this->columns[$class])) {
            unset($this->columns[$class]);
        }

        return $this;
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
        $i = 1;
        foreach ($config as $name => $field) {
            if (is_numeric($name) && !isset($field[$name]['type'])) {
                $fields = [];
                foreach ($field as $n => $f) {
                    $fields[$n] = Fields::create($n, $f);
                }
                if ($i > 1) {
                    $this->fieldsets[$this->current]->createGroup();
                }
                if (!isset($this->fieldsets[$this->current])) {
                    $this->fieldsets[$this->current] = new Fieldset();
                }
                $this->fieldsets[$this->current]->addFields($fields);
                $i++;
            } else {
                $this->addField(Fields::create($name, $field));
            }
        }
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
            $fields = array_merge($fields, $fieldset->getAllFields());
        }

        return $fields;
    }

    /**
     * Has a field element object
     *
     * @param  string $name
     * @return boolean
     */
    public function hasField($name)
    {
        return (null !== $this->getField($name));
    }

    /**
     * Has fields
     *
     * @return boolean
     */
    public function hasFields()
    {
        return (!empty($this->getFields()));
    }

    /**
     * Method to remove a form field
     *
     * @param  string $field
     * @return Form
     */
    public function removeField($field)
    {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($field)) {
                unset($fieldset[$field]);
            }
        }
        return $this;
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
        $fields = $this->toArray();
        foreach ($fields as $name => $value) {
            if (isset($values[$name]) && !($this->getField($name)->isButton())) {
                $this->setFieldValue($name, $values[$name]);
            } else if (!($this->getField($name)->isButton())) {
                $this->getField($name)->resetValue();
            }
        }

        $this->filter();

        return $this;
    }

    /**
     * Filter value with the filters in the form object
     *
     * @param  mixed $field
     * @return mixed
     */
    public function filterValue($field)
    {
        if ($field instanceof Element\AbstractElement) {
            $name      = $field->getName();
            $type      = $field->getType();
            $realValue = $field->getValue();
        } else {
            $type      = null;
            $name      = null;
            $realValue = $field;
        }

        foreach ($this->filters as $filter) {
            $realValue = $filter->filter($realValue, $name, $type);
        }

        if (($field instanceof Element\AbstractElement) && (null !== $realValue) && ($realValue != '')) {
            $field->setValue($realValue);
        }

        return $realValue;
    }

    /**
     * Filter values with the filters in the form object
     *
     * @param  array $values
     * @return mixed
     */
    public function filter(array $values = null)
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
        if ($_SESSION) {
            if (isset($_SESSION['pop_csrf'])) {
                unset($_SESSION['pop_csrf']);
            }
            if (isset($_SESSION['pop_captcha'])) {
                unset($_SESSION['pop_captcha']);
            }
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

        if (count($this->columns) > 0) {
            foreach ($this->columns as $class => $fieldsets) {
                $column = new Child('div');
                $column->setAttribute('class', $class);
                foreach ($fieldsets as $i) {
                    if (isset($this->fieldsets[$i])) {
                        $fieldset = $this->fieldsets[$i];
                        $fieldset->prepare();
                        $column->addChild($fieldset);
                    }
                }
                $this->addChild($column);
            }
        } else {
            foreach ($this->fieldsets as $fieldset) {
                $fieldset->prepare();
                $this->addChild($fieldset);
            }
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
     * @param  boolean $inner
     * @return mixed
     */
    public function render($depth = 0, $indent = null, $inner = false)
    {
        if (!($this->hasChildren())) {
            $this->prepare();
        }

        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->getAllFields() as $field) {
                if ($field instanceof Element\Input\File) {
                    $this->setAttribute('enctype', 'multipart/form-data');
                    break;
                }
            }
        }

        return parent::render($depth, $indent, $inner);
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

}
