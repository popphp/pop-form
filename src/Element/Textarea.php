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
namespace Pop\Form\Element;

/**
 * Form textarea element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */

class Textarea extends AbstractElement
{

    /**
     * Constructor
     *
     * Instantiate the textarea form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, ?string $indent = null)
    {
        parent::__construct('textarea', $value);

        $this->setAttributes(['name' => $name, 'id' => $name]);
        $this->setName($name);
        if ($indent !== null) {
            $this->setIndent($indent);
        }
    }

    /**
     * Set whether the form element is required
     *
     * @param  bool    $required
     * @param  ?string $requiredMessage
     * @return Textarea
     */
    public function setRequired(bool $required, ?string $requiredMessage = 'This field is required.'): Textarea
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
     * @return Textarea
     */
    public function setDisabled(bool $disabled): Textarea
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
     * @return Textarea
     */
    public function setReadonly(bool $readonly): Textarea
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
        } else {
            $this->removeAttribute('readonly');
        }
        return parent::setReadonly($readonly);
    }

    /**
     * Set the value of the form textarea element object
     *
     * @param  mixed $value
     * @return Textarea
     */
    public function setValue(mixed $value = null): Textarea
    {
        $this->setNodeValue($value);
        return $this;
    }

    /**
     * Reset the value of the form element
     *
     * @return Textarea
     */
    public function resetValue(): Textarea
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
        return 'textarea';
    }

    /**
     * Get the value of the form textarea element object
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
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = $this->getRequiredMessage();
        }

        $this->validateValue($value, $formValues);

        return (count($this->errors) == 0);
    }

}
