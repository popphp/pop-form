<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element;

/**
 * Form element interface
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
interface ElementInterface
{

    /**
     * Set the name of the form element
     *
     * @param  string $name
     * @return ElementInterface
     */
    public function setName(string $name): ElementInterface;

    /**
     * Set the value of the form element
     *
     * @param  mixed $value
     * @return ElementInterface
     */
    public function setValue(mixed $value): ElementInterface;

    /**
     * Reset the value of the form element
     *
     * @return ElementInterface
     */
    public function resetValue(): ElementInterface;

    /**
     * Set the label of the form element
     *
     * @param  string $label
     * @return ElementInterface
     */
    public function setLabel(string $label): ElementInterface;

    /**
     * Set the attributes of the label of the form element
     *
     * @param  array $attribs
     * @return ElementInterface
     */
    public function setLabelAttributes(array $attribs): ElementInterface;

    /**
     * Set whether the form element is required
     *
     * @param  bool $required
     * @return mixed
     */
    public function setRequired(bool $required): mixed;

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return mixed
     */
    public function setDisabled(bool $disabled): mixed;

    /**
     * Set whether the form element is readonly
     *
     * @param  bool $readonly
     * @return mixed
     */
    public function setReadonly(bool $readonly): mixed;

    /**
     * Set error to display before the element
     *
     * @param  bool $pre
     * @return ElementInterface
     */
    public function setErrorPre(bool $pre): ElementInterface;

    /**
     * Determine if error to display before the element
     *
     * @return bool
     */
    public function isErrorPre(): bool;

    /**
     * Set validators
     *
     * @param  array $validators
     * @return ElementInterface
     */
    public function setValidators(array $validators = []): ElementInterface;

    /**
     * Clear errors
     *
     * @return ElementInterface
     */
    public function clearErrors(): ElementInterface;

    /**
     * Get form element name
     *
     * @return ?string
     */
    public function getName(): ?string;

    /**
     * Get form element object type
     *
     * @return ?string
     */
    public function getType(): ?string;

    /**
     * Get form element value
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Get form element label
     *
     * @return ?string
     */
    public function getLabel(): ?string;

    /**
     * Get the attributes of the form element label
     *
     * @return array
     */
    public function getLabelAttributes(): array;

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators(): array;

    /**
     * Get whether the form element is required
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Get whether the form element is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Get whether the form element is readonly
     *
     * @return bool
     */
    public function isReadonly(): bool;

    /**
     * Get whether the form element object is a button
     *
     * @return bool
     */
    public function isButton(): bool;

    /**
     * Get form element errors
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Get if form element has errors
     *
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * Add a validator the form element
     *
     * @param  mixed $validator
     * @return ElementInterface
     */
    public function addValidator(mixed $validator): ElementInterface;

    /**
     * Validate the value
     *
     * @param  mixed $value
     * @param  array $formValues
     * @return void
     */
    public function validateValue(mixed $value, array $formValues = []): void;

    /**
     * Validate the value by callable
     *
     * @param  callable $validator
     * @param  mixed    $value
     * @param  array    $formValues
     * @return void
     */
    public function validateCallable(callable $validator, mixed $value, array $formValues = []): void;

    /**
     * Validate the form element
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool;

}
