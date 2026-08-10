<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form\Element\Input;

use Pop\Form\Element;

/**
 * Form file element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.6
 */

class File extends Element\Input
{

    /**
     * Allowed file extensions (lowercase, no leading dot). Empty = any extension allowed.
     * @var array
     */
    protected array $allowedTypes = [];

    /**
     * Maximum allowed file size, in bytes. Null = no limit enforced here.
     * @var ?int
     */
    protected ?int $maxSize = null;

    /**
     * Constructor
     *
     * Instantiate the file input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, ?string $indent = null)
    {
        parent::__construct($name, 'file', $value, $indent);
    }

    /**
     * Set the allowed file extensions (case-insensitive; leading dots are stripped)
     *
     * This checks the extension of the client-submitted filename only, not the file's
     * actual content, so it is not a hard guarantee of file type (a client can rename any
     * file). Pair it with real content validation at the storage layer if that matters.
     *
     * @param  array $extensions
     * @return File
     */
    public function setAllowedTypes(array $extensions): File
    {
        $this->allowedTypes = array_map(
            fn($extension) => strtolower(ltrim((string)$extension, '.')), $extensions
        );
        return $this;
    }

    /**
     * Get the allowed file extensions
     *
     * @return array
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    /**
     * Determine if the element has allowed file extensions set
     *
     * @return bool
     */
    public function hasAllowedTypes(): bool
    {
        return !empty($this->allowedTypes);
    }

    /**
     * Set the maximum allowed file size, in bytes
     *
     * @param  int $bytes
     * @return File
     */
    public function setMaxSize(int $bytes): File
    {
        $this->maxSize = $bytes;
        return $this;
    }

    /**
     * Get the maximum allowed file size, in bytes
     *
     * @return ?int
     */
    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    /**
     * Determine if the element has a maximum file size set
     *
     * @return bool
     */
    public function hasMaxSize(): bool
    {
        return ($this->maxSize !== null);
    }

    /**
     * Validate the form element object
     *
     * @param  array $formValues
     * @return bool
     */
    public function validate(array $formValues = []): bool
    {
        if (($_FILES) && (isset($_FILES[$this->name]['name']))) {
            $value = $_FILES[$this->name]['name'];
            $size  = $_FILES[$this->name]['size'];
        } else {
            $value = null;
            $size  = null;
        }

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = $this->getRequiredMessage();
        }

        // Check the file extension against the allowlist, if one is set
        if (!empty($value) && $this->hasAllowedTypes()) {
            $extension = strtolower((string)pathinfo((string)$value, PATHINFO_EXTENSION));
            if (!in_array($extension, $this->allowedTypes, true)) {
                $this->errors[] = 'The file type must be one of the following: ' .
                    implode(', ', $this->allowedTypes) . '.';
            }
        }

        // Check the file size against the max size, if one is set
        if (($size !== null) && $this->hasMaxSize() && ((int)$size > $this->maxSize)) {
            $this->errors[] = 'The file size must be less than or equal to ' .
                \Pop\Utils\File::formatFileSize($this->maxSize) . '.';
        }

        // Check field validators
        if (count($this->validators) > 0) {
            foreach ($this->validators as $validator) {
                if ($validator instanceof \Pop\Validator\ValidatorInterface) {
                    $class =  get_class($validator);
                    if (($size !== null) &&
                        (('Pop\Validator\LessThanEqual' == $class) || ('Pop\Validator\GreaterThanEqual' == $class) ||
                         ('Pop\Validator\LessThan' == $class) || ('Pop\Validator\GreaterThan' == $class))) {
                        if (!$validator->evaluate($size)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    } else {
                        if (!$validator->evaluate($value)) {
                            $this->errors[] = $validator->getMessage();
                        }
                    }
                } else if (is_callable($validator)) {
                    $this->validateCallable($validator, $value, $formValues);
                }
            }
        }

        return (count($this->errors) == 0);
    }

}
