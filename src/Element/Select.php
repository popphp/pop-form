<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element;

use Pop\Dom\Child;

/**
 * Form select element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
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
     * @param  string       $selected
     * @param  string       $xmlFile
     * @param  string       $indent
     */
    public function __construct($name, $values, $selected = null, $xmlFile = null, $indent = null)
    {
        parent::__construct('select');
        $this->setName($name);
        $this->setAttributes([
            'name' => $name,
            'id'   => $name
        ]);

        if (null !== $selected) {
            $this->setValue($selected);
        }
        if (null !== $indent) {
            $this->setIndent($indent);
        }

        $values = self::parseValues($values, $xmlFile);

        // Create the child option elements.
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                $optGroup = new Child('optgroup');
                if (null !== $indent) {
                    $optGroup->setIndent($indent);
                }
                $optGroup->setAttribute('label', $k);
                foreach ($v as $ky => $vl) {
                    $option = new Child('option');
                    if (null !== $indent) {
                        $option->setIndent($indent);
                    }

                    $option->setAttribute('value', $ky);

                    // Determine if the current option element is selected.
                    if ((null !== $this->selected) && ($ky == $this->selected)) {
                        $option->setAttribute('selected', 'selected');
                    }
                    $option->setNodeValue($vl);
                    $optGroup->addChild($option);
                }
                $this->addChild($optGroup);
            } else {
                $option = new Child('option');
                if (null !== $indent) {
                    $option->setIndent($indent);
                }

                $option->setAttribute('value', $k);

                // Determine if the current option element is selected.
                if ((null !== $this->selected) && ($k == $this->selected)) {
                    $option->setAttribute('selected', 'selected');
                }
                $option->setNodeValue($v);
                $this->addChild($option);
            }
        }
    }

    /**
     * Set the selected value of the select form element
     *
     * @param  mixed $value
     * @return Select
     */
    public function setValue($value)
    {
        $this->selected = $value;

        if ($this->hasChildren()) {
            foreach ($this->childNodes as $child) {
                if (($child instanceof Child) && ($child->nodeName == 'option')) {
                    if ($child->getAttribute('value') == $this->selected) {
                        $child->setAttribute('selected', 'selected');
                    } else {
                        $child->removeAttribute('selected');
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
    public function resetValue()
    {
        $this->selected = null;
        return $this;
    }

    /**
     * Get select form element selected value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->selected;
    }

}
