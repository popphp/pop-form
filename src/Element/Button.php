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
 * Form button element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class Button extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the button form element.
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, ?string $indent = null)
    {
        parent::__construct('button', $value);

        $this->setAttributes(['name' => $name, 'id' => $name]);

        if (strtolower($name) == 'submit') {
            $this->setAttribute('type', 'submit');
        } else if (strtolower($name) == 'reset') {
            $this->setAttribute('type', 'reset');
        } else{
            $this->setAttribute('type', 'button');
        }

        $this->setName($name);

        if ($value !== null) {
            $this->setValue($value);
        }

        if ($indent !== null) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set the value of the form button element object
     *
     * @param  mixed $value
     * @return Button
     */
    public function setValue(mixed $value = null): Button
    {
        $this->setNodeValue($value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Button
     */
    public function resetValue(): Button
    {
        $this->setNodeValue('');
        return $this;
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'button';
    }

    /**
     * Get the value of the form button element object
     *
     * @return ?string
     */
    public function getValue(): ?string
    {
        return $this->getNodeValue();
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
    {
        return (count($this->errors) == 0);
    }

}
