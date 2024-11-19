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
namespace Pop\Form\Element\Input;

use Pop\Dom\Child;

/**
 * Form text element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.0
 */

class Datalist extends Text
{

    /**
     * Datalist object.
     * @var ?Child
     */
    protected ?Child $datalist = null;

    /**
     * Constructor
     *
     * Instantiate the datalist text input form element
     *
     * @param  string  $name
     * @param  array  $values
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, array $values, ?string $value = null, ?string $indent = null)
    {
        parent::__construct($name, $value);
        if ($indent !== null) {
            $this->setIndent($indent);
        }
        $this->setAttribute('list', $name . '_datalist');

        if ($values !== null) {
            $this->datalist = new Child('datalist');
            if ($indent !== null) {
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
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string
    {
        return parent::render($depth, $indent, $inner) . $this->datalist->render($depth, $indent, $inner);
    }

    /**
     * Print the datalist element
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
