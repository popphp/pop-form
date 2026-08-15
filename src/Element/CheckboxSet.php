<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */

class CheckboxSet extends AbstractInputSet
{

    /**
     * Array of checked values
     * @var array
     */
    protected array $checked = [];

    /**
     * Constructor
     *
     * Instantiate a fieldset of checkbox input form elements
     *
     * @param  string            $name
     * @param  array             $values
     * @param  string|array|null $checked
     * @param  ?string           $indent
     * @param  ?string           $container
     */
    public function __construct(string $name, array $values, string|array|null $checked = null, ?string $indent = null, ?string $container = null)
    {
        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'checkbox-fieldset');

        if ($checked !== null) {
            $this->setValue($checked);
        }

        if ($indent !== null) {
            $this->setIndent($indent);
        }

        if ($container !== null) {
            $this->setContainer($container);
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

            if (is_array($v) && isset($v['value']) && isset($v['attributes'])) {
                $nodeValue = $v['value'];
                $checkbox->setAttributes($v['attributes']);
            } else {
                $nodeValue = $v;
            }

            // Determine if the current checkbox element is checked.
            if (in_array($k, $this->checked)) {
                $checkbox->check();
            }

            $this->appendInputWithSpan($checkbox, $nodeValue, 'checkbox-span', $indent, 'checkbox-fieldset-container');
            $i++;
        }
    }

    /**
     * Set an attribute for the input checkbox elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setCheckboxAttribute(string $a, string $v): Child
    {
        return $this->setInputAttribute($a, $v);
    }

    /**
     * Set an attribute or attributes for the input checkbox elements
     *
     * @param  array $a
     * @return Child
     */
    public function setCheckboxAttributes(array $a): Child
    {
        return $this->setInputAttributes($a);
    }

    /**
     * Set the checked value of the checkbox form elements
     *
     * @param  mixed $value
     * @return CheckboxSet
     */
    public function setValue(mixed $value = null): CheckboxSet
    {
        $this->checked = (!is_array($value)) ? [$value] : $value;

        if ((count($this->checked) > 0) && ($this->hasChildren())) {
            $childNodes = $this->getFieldsetChildNodes();
            foreach ($childNodes as $child) {
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
    public function resetValue(): CheckboxSet
    {
        $childNodes    = $this->getFieldsetChildNodes();
        $this->checked = [];

        foreach ($childNodes as $child) {
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
    public function getValue(): mixed
    {
        return $this->checked;
    }

    /**
     * Set the checked value
     *
     * @param  mixed $checked
     * @return CheckboxSet
     */
    public function setChecked(mixed $checked): CheckboxSet
    {
        return $this->setValue($checked);
    }

    /**
     * Get the checked value
     *
     * @return mixed
     */
    public function getChecked(): mixed
    {
        return $this->getValue();
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'checkbox';
    }

}
