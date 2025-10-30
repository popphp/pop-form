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

use Pop\Dom\Child;
use Pop\Validator;

/**
 * Abstract form element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */
abstract class AbstractElement extends Child implements ElementInterface
{

    /**
     * Element name
     * @var ?string
     */
    protected ?string $name = null;

    /**
     * Form element label
     * @var ?string
     */
    protected ?string $label = null;

    /**
     * Form element hint
     * @var ?string
     */
    protected ?string $hint = null;

    /**
     * Form element label attributes
     * @var array
     */
    protected array $labelAttributes = [];

    /**
     * Form element hint attributes
     * @var array
     */
    protected array $hintAttributes = [];

    /**
     * Form element prepend contents
     * @var ?string
     */
    protected ?string $prepend = null;

    /**
     * Form element append contents
     * @var ?string
     */
    protected ?string $append = null;

    /**
     * Form element required property
     * @var bool
     */
    protected bool $required = false;

    /**
     * Form element required message
     * @var ?string
     */
    protected ?string $requiredMessage = null;

    /**
     * Form element disabled property
     * @var bool
     */
    protected bool $disabled = false;

    /**
     * Form element readonly property
     * @var bool
     */
    protected bool $readonly = false;

    /**
     * Form element validators
     * @var array
     */
    protected array $validators = [];

    /**
     * Form element error display position
     * @var bool
     */
    protected bool $errorPre = false;

    /**
     * Form element errors
     * @var array
     */
    protected array $errors = [];

    /**
     * Constructor
     *
     * Instantiate the form element object
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  array   $options
     */
    public function __construct(string $name, ?string $value = null, array $options = [])
    {
        parent::__construct($name, $value, $options);
    }

    /**
     * Set the name of the form element object
     *
     * @param  string $name
     * @return AbstractElement
     */
    public function setName(string $name): AbstractElement
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the value of the form element
     *
     * @param  mixed $value
     * @return AbstractElement
     */
    abstract public function setValue(mixed $value = null): AbstractElement;

    /**
     * Reset the value of the form element
     *
     * @return AbstractElement
     */
    abstract public function resetValue(): AbstractElement;

    /**
     * Set the label of the form element object
     *
     * @param  string $label
     * @return AbstractElement
     */
    public function setLabel(string $label): AbstractElement
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set the hint of the form element object
     *
     * @param  string $hint
     * @return AbstractElement
     */
    public function setHint(string $hint): AbstractElement
    {
        $this->hint = $hint;
        return $this;
    }

    /**
     * Set the prepend contents of the form element object
     *
     * @param  string $prepend
     * @return AbstractElement
     */
    public function setPrepend(string $prepend): AbstractElement
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * Set the append contents of the form element object
     *
     * @param  string $append
     * @return AbstractElement
     */
    public function setAppend(string $append): AbstractElement
    {
        $this->append = $append;
        return $this;
    }

    /**
     * Set an attribute of the label of the form element object
     *
     * @param  string $a
     * @param  string $v
     * @return AbstractElement
     */
    public function setLabelAttribute(string $a, string $v): AbstractElement
    {
        $this->labelAttributes[$a] = $v;
        return $this;
    }

    /**
     * Set the attributes of the label of the form element object
     *
     * @param  array $attribs
     * @return AbstractElement
     */
    public function setLabelAttributes(array $attribs): AbstractElement
    {
        foreach ($attribs as $a => $v) {
            $this->setLabelAttribute($a, $v);
        }
        return $this;
    }

    /**
     * Set an attribute of the hint of the form element object
     *
     * @param  string $a
     * @param  string $v
     * @return AbstractElement
     */
    public function setHintAttribute(string $a, string $v): AbstractElement
    {
        $this->hintAttributes[$a] = $v;
        return $this;
    }

    /**
     * Set the attributes of the hint of the form element object
     *
     * @param  array $attribs
     * @return AbstractElement
     */
    public function setHintAttributes(array $attribs): AbstractElement
    {
        foreach ($attribs as $a => $v) {
            $this->setHintAttribute($a, $v);
        }
        return $this;
    }

    /**
     * Set whether the form element is required
     *
     * @param  bool    $required
     * @param  ?string $requiredMessage
     * @return AbstractElement
     */
    public function setRequired(bool $required, ?string $requiredMessage = 'This field is required.'): AbstractElement
    {
        $this->required = $required;

        if (!empty($requiredMessage)) {
            $this->setRequiredMessage($requiredMessage);
        }

        return $this;
    }

    /**
     * Set the form element is required message
     *
     * @param  string $requiredMessage
     * @return AbstractElement
     */
    public function setRequiredMessage(string $requiredMessage): AbstractElement
    {
        $this->requiredMessage = $requiredMessage;
        return $this;
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  bool $disabled
     * @return AbstractElement
     */
    public function setDisabled(bool $disabled): AbstractElement
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Set whether the form element is readonly
     *
     * @param  bool $readonly
     * @return AbstractElement
     */
    public function setReadonly(bool $readonly): AbstractElement
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set error pre-display
     *
     * @param  bool $pre
     * @return AbstractElement
     */
    public function setErrorPre(bool $pre): AbstractElement
    {
        $this->errorPre = $pre;
        return $this;
    }

    /**
     * Determine if error to display before the element
     *
     * @return bool
     */
    public function isErrorPre(): bool
    {
        return $this->errorPre;
    }

    /**
     * Set validators
     *
     * @param  array $validators
     * @return AbstractElement
     */
    public function setValidators(array $validators = []): AbstractElement
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * Clear errors
     *
     * @return AbstractElement
     */
    public function clearErrors(): AbstractElement
    {
        $this->errors = [];
        return $this;
    }

    /**
     * Get form element object name
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get form element object type
     *
     * @return ?string
     */
    abstract public function getType() : ?string;

    /**
     * Get form element value
     *
     * @return mixed
     */
    abstract public function getValue(): mixed;

    /**
     * Get form element object label
     *
     * @return ?string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Determine if form element has a label
     *
     * @return bool
     */
    public function hasLabel(): bool
    {
        return !empty($this->label);
    }

    /**
     * Get form element object hint
     *
     * @return ?string
     */
    public function getHint(): ?string
    {
        return $this->hint;
    }

    /**
     * Determine if form element has a hint
     *
     * @return bool
     */
    public function hasHint(): bool
    {
        return !empty($this->hint);
    }

    /**
     * Get form element object prepend contents
     *
     * @return ?string
     */
    public function getPrepend(): ?string
    {
        return $this->prepend;
    }

    /**
     * Determine if form element has prepend content
     *
     * @return bool
     */
    public function hasPrepend(): bool
    {
        return !empty($this->prepend);
    }

    /**
     * Get form element object append contents
     *
     * @return ?string
     */
    public function getAppend(): ?string
    {
        return $this->append;
    }

    /**
     * Determine if form element has append content
     *
     * @return bool
     */
    public function hasAppend(): bool
    {
        return !empty($this->append);
    }

    /**
     * Get the attributes of the form element object label
     *
     * @return array
     */
    public function getLabelAttributes(): array
    {
        return $this->labelAttributes;
    }

    /**
     * Determine if form element has label attributes
     *
     * @return bool
     */
    public function hasLabelAttributes(): bool
    {
        return !empty($this->labelAttributes);
    }

    /**
     * Get the attributes of the form element object hint
     *
     * @return array
     */
    public function getHintAttributes(): array
    {
        return $this->hintAttributes;
    }

    /**
     * Determine if form element has hint attributes
     *
     * @return bool
     */
    public function hasHintAttributes(): bool
    {
        return !empty($this->hintAttributes);
    }

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * Determine if form element has validators
     *
     * @return bool
     */
    public function hasValidators(): bool
    {
        return !empty($this->validators);
    }

    /**
     * Get whether the form element object is required
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Does the form element object have a required message
     *
     * @return bool
     */
    public function hasRequiredMessage(): bool
    {
        return !empty($this->requiredMessage);
    }

    /**
     * Get the form element object required message
     *
     * @return ?string
     */
    public function getRequiredMessage(): ?string
    {
        return $this->requiredMessage;
    }

    /**
     * Get whether the form element object is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Get whether the form element object is readonly
     *
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Get whether the form element object is a button
     *
     * @return bool
     */
    public function isButton(): bool
    {
        return (($this instanceof Button) || ($this instanceof Input\Button) ||
            ($this instanceof Input\Submit) || ($this instanceof Input\Reset));
    }

    /**
     * Get form element object errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get if form element object has errors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Add a validator the form element
     *
     * @param  mixed $validator
     * @throws Exception
     * @return AbstractElement
     */
    public function addValidator(mixed $validator): AbstractElement
    {
        if (!($validator instanceof \Pop\Validator\AbstractValidator) && !is_callable($validator)) {
            throw new Exception('Error: The validator must be an instance of Pop\Validator\AbstractValidator or a callable object.');
        }
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Add multiple validators the form element
     *
     * @param  array $validators
     * @throws Exception
     * @return AbstractElement
     */
    public function addValidators(array $validators): AbstractElement
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
        return $this;
    }

    /**
     * Validate the value
     *
     * @param  mixed $value
     * @param  array $formValues
     * @return void
     */
    public function validateValue(mixed $value, array $formValues = []): void
    {
        // Check field validators
        if (count($this->validators) > 0) {
            foreach ($this->validators as $validator) {
                if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                    if (!$validator->evaluate($value)) {
                        if (!in_array($validator->getMessage(), $this->errors)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    }
                } else if (is_callable($validator)) {
                    $this->validateCallable($validator, $value, $formValues);
                }
            }
        }
    }

    /**
     * Validate the value by callable
     *
     * @param  callable $validator
     * @param  mixed    $value
     * @param  array    $formValues
     * @return void
     */
    public function validateCallable(callable $validator, mixed $value, array $formValues = []): void
    {
        $result = call_user_func_array($validator, [$value, $formValues]);
        if ($result instanceof \Pop\Validator\ValidatorInterface) {
            if (!$result->evaluate($value)) {
                $this->errors[] = $result->getMessage();
            }
        } else if (is_array($result)) {
            foreach ($result as $val) {
                if ($val instanceof \Pop\Validator\ValidatorInterface) {
                    if (!$val->evaluate($value)) {
                        $this->errors[] = $val->getMessage();
                    }
                }
            }
        } else if ($result !== null) {
            $this->errors[] = $result;
        }
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    abstract public function validate(array $formValues = []): bool;

    /**
     * Print form element
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
