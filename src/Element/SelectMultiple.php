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

/**
 * Form select multiple element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
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
                    $option = new Select\Option((string)$ky, (string)$vl);
                    if ($indent !== null) {
                        $option->setIndent($indent);
                    }
                    $optGroup->addChild($option);
                }
                $this->addChild($optGroup);
            } else {
                $option = new Select\Option((string)$k, (string)$v);
                if ($indent !== null) {
                    $option->setIndent($indent);
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
