<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element\Input;

use Pop\Form\Element;

/**
 * Form password element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */

class Password extends Element\Input
{

    /**
     * Flag to allow rendering the value
     * @var bool
     */
    protected bool $renderValue = false;

    /**
     * Constructor
     *
     * Instantiate the password input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     * @param  bool    $renderValue
     */
    public function __construct(string $name, $value = null, $indent = null, bool $renderValue = false)
    {
        parent::__construct($name, 'password', $value, $indent);
        $this->setRenderValue($renderValue);
    }

    /**
     * Set the render value flag
     *
     * @param  bool $renderValue
     * @return Password
     */
    public function setRenderValue(bool $renderValue): Password
    {
        $this->renderValue = $renderValue;
        return $this;
    }

    /**
     * Get the render value flag
     *
     * @return bool
     */
    public function getRenderValue(): bool
    {
        return $this->renderValue;
    }

    /**
     * Render the password element
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string
    {
        if (!$this->renderValue) {
            $this->setAttribute('value', '');
        }
        return parent::render($depth, $indent, $inner);
    }

}
