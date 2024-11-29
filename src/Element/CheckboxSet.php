<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
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
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.1
 */

class CheckboxSet extends AbstractElement
{

    /**
     * Array of checkbox input elements
     * @var array
     */
    protected array $checkboxes = [];

    /**
     * Array of checked values
     * @var array
     */
    protected array $checked = [];

    /**
     * Fieldset legend
     * @var ?string
     */
    protected ?string $legend = null;

    /**
     * Constructor
     *
     * Instantiate a fieldset of checkbox input form elements
     *
     * @param  string            $name
     * @param  array             $values
     * @param  string|array|null $checked
     * @param  ?string           $indent
     */
    public function __construct(string $name, array $values, string|array|null $checked = null, ?string $indent = null)
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

            // Determine if the current radio element is checked.
            if (in_array($k, $this->checked)) {
                $checkbox->check();
            }

            $span = new Child('span');
            if ($indent !== null) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'checkbox-span');
            $span->setNodeValue($nodeValue);
            $this->addChildren([$checkbox, $span]);
            $this->checkboxes[] = $checkbox;
            $i++;
        }
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return CheckboxSet
     */
    public function setDisabled(bool $disabled): CheckboxSet
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
     * @param  bool $readonly
     * @return CheckboxSet
     */
    public function setReadonly(bool $readonly): CheckboxSet
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
    public function setCheckboxAttribute(string $a, string $v): Child
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
    public function setCheckboxAttributes(array $a): Child
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
     * @param  mixed $value
     * @return CheckboxSet
     */
    public function setValue(mixed $value): CheckboxSet
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
    public function resetValue(): CheckboxSet
    {
        $this->checked = [];
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
     * Method to set fieldset legend
     *
     * @param  string $legend
     * @return CheckboxSet
     */
    public function setLegend(string $legend): CheckboxSet
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Method to get fieldset legend
     *
     * @return ?string
     */
    public function getLegend(): ?string
    {
        return $this->legend;
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

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
    {
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = $this->getRequiredMessage();
        }

        $this->validateValue($value, $formValues);

        return (count($this->errors) == 0);
    }

    /**
     * Render the child and its child nodes
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string
    {
        if (!empty($this->legend)) {
            $this->addChild(new Child('legend', $this->legend));
        }
        return parent::render($depth, $indent, $inner);
    }

}
