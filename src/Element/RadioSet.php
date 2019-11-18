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

use Pop\Dom\Child;

/**
 * Form radio element set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class RadioSet extends AbstractElement
{

    /**
     * Array of radio input elements
     * @var array
     */
    protected $radios = [];

    /**
     * Array of checked values
     * @var string
     */
    protected $checked = null;

    /**
     * Fieldset legend
     * @var string
     */
    protected $legend = null;

    /**
     * Constructor
     *
     * Instantiate the radio input form elements
     *
     * @param  string $name
     * @param  array  $values
     * @param  string $checked
     * @param  string $indent
     */
    public function __construct($name, array $values, $checked = null, $indent = null)
    {
        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'radio-fieldset');

        if (null !== $checked) {
            $this->setValue($checked);
        }

        if (null !== $indent) {
            $this->setIndent($indent);
        }

        // Create the radio elements and related span elements.
        $i = null;
        foreach ($values as $k => $v) {
            $radio = new Input\Radio($name, null, $indent);
            $radio->setAttributes([
                'class' => 'radio',
                'id'    => ($name . $i),
                'value' => $k
            ]);

            // Determine if the current radio element is checked.
            if ((null !== $this->checked) && ($k == $this->checked)) {
                $radio->check();
            }

            $span = new Child('span');
            if (null !== $indent) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'radio-span');
            $span->setNodeValue($v);
            $this->addChildren([$radio, $span]);
            $this->radios[] = $radio;
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
     * Set an attribute for the input radio elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setRadioAttribute($a, $v)
    {
        foreach ($this->radios as $radio) {
            $radio->setAttribute($a, $v);
            if ($a == 'tabindex') {
                $v++;
            }

        }
        return $this;
    }

    /**
     * Set an attribute or attributes for the input radio elements
     *
     * @param  array $a
     * @return Child
     */
    public function setRadioAttributes(array $a)
    {
        foreach ($this->radios as $radio) {
            $radio->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }

    /**
     * Set the checked value of the radio form elements
     *
     * @param  mixed $value
     * @return RadioSet
     */
    public function setValue($value)
    {
        $this->checked = $value;

        if ((null !== $this->checked) && ($this->hasChildren())) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Input\Radio) {
                    if ($child->getValue() == $this->checked) {
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
     * @return RadioSet
     */
    public function resetValue()
    {
        $this->checked = null;
        foreach ($this->childNodes as $child) {
            if ($child instanceof Input\Radio) {
                $child->uncheck();
            }
        }
        return $this;
    }

    /**
     * Get radio form element checked value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->checked;
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType()
    {
        return 'radio';
    }

    /**
     * Set the checked value
     *
     * @param  mixed $checked
     * @return RadioSet
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
     * @return RadioSet
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