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
 * Form input element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.1
 */

class Input extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the form input element, defaults to text
     *
     * @param  string  $name
     * @param  string  $type
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, string $type = 'text', ?string $value = null, ?string  $indent = null)
    {
        parent::__construct('input');

        $this->setName($name);
        $this->setAttributes([
            'type'  => $type,
            'name'  => $name,
            'id'    => $name,
            'value' => $value
        ]);

        if ($indent !== null) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set whether the form element is required
     *
     * @param  bool    $required
     * @param  ?string $requiredMessage
     * @return Input
     */
    public function setRequired(bool $required, ?string $requiredMessage = 'This field is required.'): Input
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }
        return parent::setRequired($required, $requiredMessage);
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return Input
     */
    public function setDisabled(bool $disabled): Input
    {
        if ($disabled) {
            $this->setAttribute('disabled', 'disabled');
        } else {
            $this->removeAttribute('disabled');
        }
        return parent::setDisabled($disabled);
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  bool $readonly
     * @return Input
     */
    public function setReadonly(bool $readonly): Input
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
        } else {
            $this->removeAttribute('readonly');
        }
        return parent::setReadonly($readonly);
    }

    /**
     * Set the value of the form input element object
     *
     * @param  mixed $value
     * @return Input
     */
    public function setValue(mixed $value): Input
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Input
     */
    public function resetValue(): Input
    {
        $this->setAttribute('value', '');
        return $this;
    }

    /**
     * Get the value of the form input element object
     *
     * @return ?string
     */
    public function getValue(): ?string
    {
        return $this->getAttribute('value');
    }

    /**
     * Get the type of the form input element object
     *
     * @return ?string
     */
    public function getType(): ?string
    {
        return $this->getAttribute('type');
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
    {
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value) && !($this->getType() == 'number' && $value === '0')) {
            $this->errors[] = $this->getRequiredMessage();
        }

        $this->validateValue($value, $formValues);

        return (count($this->errors) == 0);
    }

}
