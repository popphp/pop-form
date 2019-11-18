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
namespace Pop\Form\Element;

/**
 * Form input element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Input extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the form input element, defaults to text
     *
     * @param  string $name
     * @param  string $type
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, $type = 'text', $value = null, $indent = null)
    {
        parent::__construct('input');

        $this->setName($name);
        $this->setAttributes([
            'type'  => $type,
            'name'  => $name,
            'id'    => $name,
            'value' => $value
        ]);

        if (null !== $indent) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return Input
     */
    public function setRequired($required)
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }
        return parent::setRequired($required);
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  boolean $disabled
     * @return Input
     */
    public function setDisabled($disabled)
    {
        if ($disabled) {
            $this->setAttribute('disabled', 'disabled');
        } else {
            $this->removeAttribute('disabled');
        }
        return parent::setDisabled($disabled);
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  boolean $readonly
     * @return Input
     */
    public function setReadonly($readonly)
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
        } else {
            $this->removeAttribute('readonly');
        }
        return parent::setReadonly($readonly);
    }

    /**
     * Set the value of the form input element object
     *
     * @param  mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Input
     */
    public function resetValue()
    {
        $this->setAttribute('value', '');
        return $this;
    }

    /**
     * Get the value of the form input element object
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Get the type of the form input element object
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute('type');
    }

    /**
     * Validate the form element object
     *
     * @return boolean
     */
    public function validate()
    {
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value) && !($this->getType() == 'number' && $value === '0')) {
            $this->errors[] = 'This field is required.';
        }

        // Check field validators
        if (count($this->validators) > 0) {
            foreach ($this->validators as $validator) {
                if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                    if (!$validator->evaluate($value)) {
                        if (!in_array($validator->getMessage(), $this->errors)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    }
                } else if (is_callable($validator)) {
                    $result = call_user_func_array($validator, [$value]);
                    if (null !== $result) {
                        if (!in_array($result, $this->errors)) {
                            $this->errors[] = $result;
                        }
                    }
                }
            }
        }

        return (count($this->errors) == 0);
    }

}
