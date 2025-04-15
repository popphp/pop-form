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
 * Form radio element set class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class RadioSet extends AbstractElement
{

    /**
     * Array of radio input elements
     * @var array
     */
    protected array $radios = [];

    /**
     * Array of checked values
     * @var ?string
     */
    protected ?string $checked = null;

    /**
     * Fieldset legend
     * @var ?string
     */
    protected ?string $legend = null;

    /**
     * Constructor
     *
     * Instantiate the radio input form elements
     *
     * @param  string  $name
     * @param  array   $values
     * @param  ?string $checked
     * @param  ?string $indent
     */
    public function __construct(string $name, array $values, ?string $checked = null, ?string $indent = null)
    {
        parent::__construct('fieldset');

        $this->setName($name);
        $this->setAttribute('class', 'radio-fieldset');

        if ($checked !== null) {
            $this->setValue($checked);
        }

        if ($indent !== null) {
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

            if (is_array($v) && isset($v['value']) && isset($v['attributes'])) {
                $nodeValue = $v['value'];
                $radio->setAttributes($v['attributes']);
            } else {
                $nodeValue = $v;
            }

            // Determine if the current radio element is checked.
            if (($this->checked !== null) && ($k == $this->checked)) {
                $radio->check();
            }

            $span = new Child('span');
            if ($indent !== null) {
                $span->setIndent($indent);
            }
            $span->setAttribute('class', 'radio-span');
            $span->setNodeValue($nodeValue);
            $this->addChildren([$radio, $span]);
            $this->radios[] = $radio;
            $i++;
        }
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return RadioSet
     */
    public function setDisabled(bool $disabled): RadioSet
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
     * @return RadioSet
     */
    public function setReadonly(bool $readonly): RadioSet
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
    public function setRadioAttribute(string $a, string $v): Child
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
    public function setRadioAttributes(array $a): Child
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
    public function setValue(mixed $value = null): RadioSet
    {
        $this->checked = $value;

        if (($this->checked !== null) && ($this->hasChildren())) {
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
    public function resetValue(): RadioSet
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
    public function getValue(): mixed
    {
        return $this->checked;
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'radio';
    }

    /**
     * Set the checked value
     *
     * @param  mixed $checked
     * @return RadioSet
     */
    public function setChecked(mixed $checked): RadioSet
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
     * @return RadioSet
     */
    public function setLegend(string $legend): RadioSet
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
