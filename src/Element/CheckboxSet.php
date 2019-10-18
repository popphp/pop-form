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

/**
 * Form checkbox element set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */

class CheckboxSet extends AbstractElement
{

    /**
     * Array of checkbox input elements
     * @var array
     */
    protected $checkboxes = [];

    /**
     * Array of checked values
     * @var array
     */
    protected $checked = [];

    /**
     * Fieldset legend
     * @var string
     */
    protected $legend = null;

    /**
     * Constructor
     *
     * Instantiate a fieldset of checkbox input form elements
     *
     * @param  string       $name
     * @param  array        $values
     * @param  string|array $checked
     * @param  string       $indent
     */
    public function __construct($name, array $values, $checked = null, $indent = null)
    {
        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'checkbox-fieldset');

        if (null !== $checked) {
            $this->setValue($checked);
        }

        if (null !== $indent) {
            $this->setIndent($indent);
        }

        // Create the checkbox elements and related span elements.
        $i = null;
        foreach ($values as $k => $v) {
            $checkbox = new Input\Checkbox($name . '[]', null, $indent);
            $checkbox->setAttributes([
                'class' => 'checkbox',
                'id'    => ($name . $i),
                'value' => $k
            ]);

            // Determine if the current radio element is checked.
            if (in_array($k, $this->checked)) {
                $checkbox->check();
            }

            $span = new Child('span');
            if (null !== $indent) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'checkbox-span');
            $span->setNodeValue($v);
            $this->addChildren([$checkbox, $span]);
            $this->checkboxes[] = $checkbox;
            $i++;
        }
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  boolean $disabled
     * @return Select
     */
    public function setDisabled($disabled)
    {
        if ($disabled) {
            foreach ($this->childNodes as $childNode) {
                $childNode->setAttribute('disabled', 'disabled');
            }
        } else {
            foreach ($this->childNodes as $childNode) {
                $childNode->removeAttribute('disabled');
            }
        }

        return parent::setDisabled($disabled);
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  boolean $readonly
     * @return Select
     */
    public function setReadonly($readonly)
    {
        if ($readonly) {
            foreach ($this->childNodes as $childNode) {
                $childNode->setAttribute('readonly', 'readonly');
                $childNode->setAttribute('onclick', 'return false;');
            }
        } else {
            foreach ($this->childNodes as $childNode) {
                $childNode->removeAttribute('readonly');
                $childNode->removeAttribute('onclick');
            }
        }

        return parent::setReadonly($readonly);
    }

    /**
     * Set an attribute for the input checkbox elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setCheckboxAttribute($a, $v)
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkbox->setAttribute($a, $v);
            if ($a == 'tabindex') {
                $v++;
            }

        }
        return $this;
    }

    /**
     * Set an attribute or attributes for the input checkbox elements
     *
     * @param  array $a
     * @return Child
     */
    public function setCheckboxAttributes(array $a)
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkbox->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }


    /**
     * Set the checked value of the checkbox form elements
     *
     * @param  $value
     * @return CheckboxSet
     */
    public function setValue($value)
    {
        $this->checked = (!is_array($value)) ? [$value] : $value;

        if ((count($this->checked) > 0) && ($this->hasChildren())) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Input\Checkbox) {
                    if (in_array($child->getValue(), $this->checked)) {
                        $child->check();
                    } else {
                        $child->uncheck();
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return CheckboxSet
     */
    public function resetValue()
    {
        $this->checked = null;
        foreach ($this->childNodes as $child) {
            if ($child instanceof Input\Checkbox) {
                $child->uncheck();
            }
        }
        return $this;
    }

    /**
     * Get checkbox form element checked value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->checked;
    }

    /**
     * Set the checked value
     *
     * @param  mixed $checked
     * @return CheckboxSet
     */
    public function setChecked($checked)
    {
        return $this->setValue($checked);
    }

    /**
     * Get the checked value
     *
     * @return string
     */
    public function getChecked()
    {
        return $this->getValue();
    }

    /**
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return CheckboxSet
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
        return $this;
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
     * Get form element object type
     *
     * @return string
     */
    public function getType()
    {
        return 'checkbox';
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
        if (($this->required) && empty($value)) {
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

    /**
     * Render the child and its child nodes
     *
     * @param  int     $depth
     * @param  string  $indent
     * @param  boolean $inner
     * @return string
     */
    public function render($depth = 0, $indent = null, $inner = false)
    {
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }
        return parent::render($depth, $indent, $inner);
    }

}