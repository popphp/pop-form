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
 * Form checkbox element set class
 *
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.2
 */

class CheckboxSet extends AbstractElement
{

    /**
     * Array of checkbox input elements
     * @var array
     */
    protected $checkboxes = [];

    /**
     * Constructor
     *
     * Instantiate the set of checkbox input form elements
     *
     * @param  string       $name
     * @param  array        $values
     * @param  string       $indent
     * @param  string|array $marked
     * @return CheckboxSet
     */
    public function __construct($name, array $values, $indent = null, $marked = null)
    {
        if (null !== $marked) {
            if (!is_array($marked)) {
                $marked = [$marked];
            }
        } else {
            $marked = [];
        }

        parent::__construct('fieldset', null, null, false, $indent);
        $this->attributes['class'] = 'checkbox-fieldset';
        $this->setMarked($marked);
        $this->setName($name . '[]');

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
            if (in_array($k, $this->marked)) {
                $checkbox->setAttribute('checked', 'checked');
            }

            $span = new Child('span', null, null, false, $indent);
            $span->setAttribute('class', 'checkbox-span');
            $span->setNodeValue($v);
            $this->addChildren([$checkbox, $span]);
            $this->checkboxes[] = $checkbox;
            $i++;
        }

        $this->value = $values;
    }

    /**
     * Set an attribute for the input checkbox elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setAttribute($a, $v)
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
    public function setAttributes(array $a)
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkbox->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }

}