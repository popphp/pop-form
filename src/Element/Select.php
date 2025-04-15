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
 * Form select element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class Select extends AbstractSelect
{

    /**
     * Constructor
     *
     * Instantiate the select form element object
     *
     * @param  string       $name
     * @param  string|array $values
     * @param  mixed        $selected
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
            'name' => $name,
            'id'   => $name
        ]);

        if ($selected !== null) {
            $this->setValue($selected);
        }
        if ($indent !== null) {
            $this->setIndent($indent);
        }

        $this->setValues($values);
    }

    /**
     * Set the options values of the select form element
     *
     * @param  string|array $values
     * @param  ?string      $xmlFile
     * @param  ?string      $indent
     * @return Select
     */
    public function setValues(string|array $values, ?string $xmlFile = null, ?string $indent = null): Select
    {
        $values = self::parseValues($values, $xmlFile);

        // Create the child option elements.
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                if (isset($v['value']) && isset($v['attributes'])) {
                    $option = new Select\Option($k, $v['value'], ['attributes' => $v['attributes']]);
                    $this->addChild($option);
                } else {
                    $optGroup = new Select\Optgroup();
                    if ($indent !== null) {
                        $optGroup->setIndent($indent);
                    }
                    $optGroup->setAttribute('label', $k);
                    foreach ($v as $ky => $vl) {
                        if (is_array($vl) && isset($vl['value']) && isset($vl['attributes'])) {
                            $option = new Select\Option($ky, $vl['value'], ['attributes' => $vl['attributes']]);
                        } else {
                            $option = new Select\Option($ky, $vl);
                        }
                        if ($indent !== null) {
                            $option->setIndent($indent);
                        }

                        // Determine if the current option element is selected.
                        if (($this->selected !== null) && ($ky == $this->selected)) {
                            $option->select();
                        }
                        $optGroup->addChild($option);
                    }
                    $this->addChild($optGroup);
                }
            } else {
                $option = new Select\Option($k, $v);
                if ($indent !== null) {
                    $option->setIndent($indent);
                }

                // Determine if the current option element is selected.
                if (($this->selected !== null) && ($k == $this->selected)) {
                    $option->select();
                }
                $this->addChild($option);
            }
        }

        return $this;
    }

    /**
     * Set the selected value of the select form element
     *
     * @param  mixed $value
     * @return Select
     */
    public function setValue(mixed $value = null): Select
    {
        $this->selected = $value;

        if ($this->hasChildren()) {
            foreach ($this->childNodes as $child) {
                if ($child instanceof Select\Option) {
                    if ($child->getValue() == $this->selected) {
                        $child->select();
                    } else {
                        $child->deselect();
                    }
                } else if ($child instanceof Select\Optgroup) {
                    $options = $child->getOptions();
                    foreach ($options as $option) {
                        if ($option->getValue() == $this->selected) {
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
     * @return Select
     */
    public function resetValue(): Select
    {
        $this->selected = null;

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
