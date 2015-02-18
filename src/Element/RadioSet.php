<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @package    Pop_Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */

class RadioSet extends AbstractElement
{

    /**
     * Array of radio input elements
     * @var array
     */
    protected $radios = [];

    /**
     * Constructor
     *
     * Instantiate the radio input form elements
     *
     * @param  string $name
     * @param  array  $values
     * @param  string $indent
     * @param  string $marked
     * @return RadioSet
     */
    public function __construct($name, array $values, $indent = null, $marked = null)
    {
        parent::__construct('fieldset', null, null, false, $indent);
        $this->setAttribute('class', 'radio-fieldset');
        $this->setMarked($marked);
        $this->setName($name);

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
            if ($k === $this->marked) {
                $radio->setAttribute('checked', 'checked');
            }

            $span = new Child('span', null, null, false, $indent);
            $span->setAttribute('class', 'radio-span');
            $span->setNodeValue($v);
            $this->addChildren([$radio, $span]);
            $this->radios[] = $radio;
            $i++;
        }

        $this->value = $values;
    }

    /**
     * Set an attribute for the input radio elements
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setAttribute($a, $v)
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
    public function setAttributes(array $a)
    {
        foreach ($this->radios as $radio) {
            $radio->setAttributes($a);
            if (isset($a['tabindex'])) {
                $a['tabindex']++;
            }
        }
        return $this;
    }

}