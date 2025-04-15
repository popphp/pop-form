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

/**
 * Form select multiple element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class SelectMultiple extends AbstractSelect
{

    /**
     * Constructor
     *
     * Instantiate the select form element object
     *
     * @param  string       $name
     * @param  string|array $values
     * @param  string|array $selected
     * @param  ?string      $xmlFile
     * @param  ?string      $indent
     */
    public function __construct(
        string $name, string|array $values, mixed $selected = null, ?string $xmlFile = null, ?string $indent = null
    )
    {
        parent::__construct('select');

        $this->setName($name);
        $this->setAttributes([
            'name'     => $name . '[]',
            'id'       => $name,
            'multiple' => 'multiple'
        ]);

        if ($indent !== null) {
            $this->setIndent($indent);
        }

        $values = self::parseValues($values, $xmlFile);

        // Create the child option elements.
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                $optGroup = new Select\Optgroup();
                if ($indent !== null) {
                    $optGroup->setIndent($indent);
                }
                $optGroup->setAttribute('label', $k);
                foreach ($v as $ky => $vl) {
                    $option = new Select\Option($ky, $vl);
                    if ($indent !== null) {
                        $option->setIndent($indent);
                    }

                    // Determine if the current option element is selected.
                    if (is_array($this->selected) && in_array($ky, $this->selected, true)) {
                        $option->select();
                    }
                    $optGroup->addChild($option);
                }
                $this->addChild($optGroup);
            } else {
                $option = new Select\Option($k, $v);
                if ($indent !== null) {
                    $option->setIndent($indent);
                }

                // Determine if the current option element is selected.
                if (is_array($this->selected) && in_array($k, $this->selected, true)) {
                    $option->select();
                }
                $this->addChild($option);
            }
        }

        if ($selected !== null) {
            $this->setValue($selected);
        } else {
            $this->selected = [];
        }
    }

    /**
     * Set the selected value of the select form element
     *
     * @param  mixed $value
     * @return SelectMultiple
     */
    public function setValue(mixed $value = null): SelectMultiple
    {
        $this->selected = (!is_array($value)) ? [$value] : $value;

        if ($this->hasChildren()) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Select\Option) {
                    if (in_array($child->getValue(), $this->selected)) {
                        $child->select();
                    } else {
                        $child->deselect();
                    }
                } else if ($child instanceof Select\Optgroup) {
                    $options = $child->getOptions();
                    foreach ($options as $option) {
                        if (in_array($option->getValue(), $this->selected)) {
                            $option->select();
                        } else {
                            $option->deselect();
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return SelectMultiple
     */
    public function resetValue(): SelectMultiple
    {
        $this->selected = [];

        if ($this->hasChildren()) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Select\Option) {
                    $child->deselect();
                } else if ($child instanceof Select\Optgroup) {
                    $options = $child->getOptions();
                    foreach ($options as $option) {
                        $option->deselect();
                    }
                }
            }
        }

        return $this;
    }

}
