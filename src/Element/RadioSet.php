<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
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
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */

class RadioSet extends AbstractInputSet
{

    /**
     * Array of checked values
     * @var ?string
     */
    protected ?string $checked = null;

    /**
     * Constructor
     *
     * Instantiate the radio input form elements
     *
     * @param  string  $name
     * @param  array   $values
     * @param  ?string $checked
     * @param  ?string $indent
     * @param  ?string $container
     */
    public function __construct(string $name, array $values, ?string $checked = null, ?string $indent = null, ?string $container = null)
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

        if ($container !== null) {
            $this->setContainer($container);
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

            $this->appendInputWithSpan($radio, $nodeValue, 'radio-span', $indent, 'radio-fieldset-container');
            $i++;
        }
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
        return $this->setInputAttribute($a, $v);
    }

    /**
     * Set an attribute or attributes for the input radio elements
     *
     * @param  array $a
     * @return Child
     */
    public function setRadioAttributes(array $a): Child
    {
        return $this->setInputAttributes($a);
    }

    /**
     * Set the checked value of the radio form elements
     *
     * @param  mixed $value
     * @return RadioSet
     */
    public function setValue(mixed $value = null): RadioSet
    {
        $childNodes    = $this->getFieldsetChildNodes();
        $this->checked = $value;

        if (($this->checked !== null) && ($this->hasChildren())) {
            foreach ($childNodes as $child) {
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
        $childNodes    = $this->getFieldsetChildNodes();
        $this->checked = null;
        foreach ($childNodes as $child) {
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

}
