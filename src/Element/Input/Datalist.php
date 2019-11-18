<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element\Input;

use Pop\Dom\Child;

/**
 * Form text element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

class Datalist extends Text
{

    /**
     * Datalist object.
     * @var Child
     */
    protected $datalist = null;

    /**
     * Constructor
     *
     * Instantiate the datalist text input form element
     *
     * @param  string $name
     * @param  array  $values
     * @param  string $value
     * @param  string $indent
     */
    public function __construct($name, array $values, $value = null, $indent = null)
    {
        parent::__construct($name, $value);
        if (null !== $indent) {
            $this->setIndent($indent);
        }
        $this->setAttribute('list', $name . '_datalist');

        if (null !== $values) {
            $this->datalist = new Child('datalist');
            if (null !== $indent) {
                $this->datalist->setIndent($indent);
            }
            $this->datalist->setAttribute('id', $name . '_datalist');
            foreach ($values as $key => $val) {
                $this->datalist->addChild((new Child('option', $val))->setAttribute('value', $key));
            }
        }
    }

    /**
     * Render the datalist element
     *
     * @param  int     $depth
     * @param  string  $indent
     * @param  boolean $inner
     * @return mixed
     */
    public function render($depth = 0, $indent = null, $inner = false)
    {
        return parent::render($depth, $indent, $inner) . $this->datalist->render($depth, $indent, $inner);
    }

    /**
     * Print the datalist element
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
