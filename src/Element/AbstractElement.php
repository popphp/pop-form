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
namespace Pop\Form\Element;

use Pop\Dom\Child;
use Pop\Validator;

/**
 * Abstract form element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */
abstract class AbstractElement extends Child implements ElementInterface
{

    /**
     * Element name
     * @var string
     */
    protected $name = null;

    /**
     * Form element label
     * @var string
     */
    protected $label = null;

    /**
     * Form element hint
     * @var string
     */
    protected $hint = null;

    /**
     * Form element label attributes
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * Form element hint attributes
     * @var array
     */
    protected $hintAttributes = [];

    /**
     * Form element required property
     * @var boolean
     */
    protected $required = false;

    /**
     * Form element disabled property
     * @var boolean
     */
    protected $disabled = false;

    /**
     * Form element readonly property
     * @var boolean
     */
    protected $readonly = false;

    /**
     * Form element validators
     * @var array
     */
    protected $validators = [];

    /**
     * Form element error display position
     * @var boolean
     */
    protected $errorPre = false;

    /**
     * Form element errors
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     * Instantiate the form element object
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     */
    public function __construct($name, $value = null, array $options = [])
    {
        parent::__construct($name, $value, $options);
    }

    /**
     * Set the name of the form element object
     *
     * @param  string $name
     * @return AbstractElement
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the value of the form element
     *
     * @param  mixed $value
     * @return AbstractElement
     */
    abstract public function setValue($value);

    /**
     * Reset the value of the form element
     *
     * @return AbstractElement
     */
    abstract public function resetValue();

    /**
     * Set the label of the form element object
     *
     * @param  string $label
     * @return AbstractElement
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set the hint of the form element object
     *
     * @param  string $hint
     * @return AbstractElement
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
        return $this;
    }

    /**
     * Set an attribute of the label of the form element object
     *
     * @param  string $a
     * @param  string $v
     * @return AbstractElement
     */
    public function setLabelAttribute($a, $v)
    {
        $this->labelAttributes[$a] = $v;
        return $this;
    }

    /**
     * Set the attributes of the label of the form element object
     *
     * @param  array $attribs
     * @return AbstractElement
     */
    public function setLabelAttributes(array $attribs)
    {
        foreach ($attribs as $a => $v) {
            $this->setLabelAttribute($a, $v);
        }
        return $this;
    }

    /**
     * Set an attribute of the hint of the form element object
     *
     * @param  string $a
     * @param  string $v
     * @return AbstractElement
     */
    public function setHintAttribute($a, $v)
    {
        $this->hintAttributes[$a] = $v;
        return $this;
    }

    /**
     * Set the attributes of the hint of the form element object
     *
     * @param  array $attribs
     * @return AbstractElement
     */
    public function setHintAttributes(array $attribs)
    {
        foreach ($attribs as $a => $v) {
            $this->setHintAttribute($a, $v);
        }
        return $this;
    }

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return mixed
     */
    public function setRequired($required)
    {
        $this->required = (boolean)$required;
        return $this;
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  boolean $disabled
     * @return mixed
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (boolean)$disabled;
        return $this;
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  boolean $readonly
     * @return mixed
     */
    public function setReadonly($readonly)
    {
        $this->readonly = (boolean)$readonly;
        return $this;
    }

    /**
     * Set error pre-display
     *
     * @param  boolean $pre
     * @return AbstractElement
     */
    public function setErrorPre($pre)
    {
        $this->errorPre = (boolean)$pre;
        return $this;
    }

    /**
     * Determine if error to display before the element
     *
     * @return boolean
     */
    public function isErrorPre()
    {
        return $this->errorPre;
    }

    /**
     * Set validators
     *
     * @param  array $validators
     * @return AbstractElement
     */
    public function setValidators(array $validators = [])
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * Clear errors
     *
     * @return AbstractElement
     */
    public function clearErrors()
    {
        $this->errors = [];
        return $this;
    }

    /**
     * Get form element object name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Get form element value
     *
     * @return mixed
     */
    abstract public function getValue();

    /**
     * Get form element object label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get form element object hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Get the attributes of the form element object label
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Get the attributes of the form element object hint
     *
     * @return array
     */
    public function getHintAttributes()
    {
        return $this->hintAttributes;
    }

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Get whether the form element object is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Get whether the form element object is disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Get whether the form element object is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * Get whether the form element object is a button
     *
     * @return boolean
     */
    public function isButton()
    {
        return (($this instanceof Button) || ($this instanceof Input\Button) ||
            ($this instanceof Input\Submit) || ($this instanceof Input\Reset));
    }

    /**
     * Get form element object errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get if form element object has errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->errors) > 0);
    }

    /**
     * Add a validator the form element
     *
     * @param  mixed $validator
     * @throws Exception
     * @return AbstractElement
     */
    public function addValidator($validator)
    {
        if (!($validator instanceof \Pop\Validator\AbstractValidator) && !is_callable($validator)) {
            throw new Exception('Error: The validator must be an instance of Pop\Validator\AbstractValidator or a callable object.');
        }
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Add multiple validators the form element
     *
     * @param  array $validators
     * @return AbstractElement
     */
    public function addValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
        return $this;
    }

    /**
     * Validate the form element object
     *
     * @return boolean
     */
    abstract public function validate();

    /**
     * Print form element
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
