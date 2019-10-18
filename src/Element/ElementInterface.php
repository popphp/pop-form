<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */
interface ElementInterface
{

    /**
     * Set the name of the form element
     *
     * @param  string $name
     * @return ElementInterface
     */
    public function setName($name);

    /**
     * Set the value of the form element
     *
     * @param  mixed $value
     * @return ElementInterface
     */
    public function setValue($value);

    /**
     * Reset the value of the form element
     *
     * @return ElementInterface
     */
    public function resetValue();

    /**
     * Set the label of the form element
     *
     * @param  mixed $label
     * @return ElementInterface
     */
    public function setLabel($label);

    /**
     * Set the attributes of the label of the form element
     *
     * @param  array $attribs
     * @return ElementInterface
     */
    public function setLabelAttributes(array $attribs);

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return mixed
     */
    public function setRequired($required);

    /**
     * Set whether the form element is disabled
     *
     * @param  boolean $disabled
     * @return mixed
     */
    public function setDisabled($disabled);

    /**
     * Set whether the form element is readonly
     *
     * @param  boolean $readonly
     * @return mixed
     */
    public function setReadonly($readonly);

    /**
     * Set error to display before the element
     *
     * @param  boolean $pre
     * @return ElementInterface
     */
    public function setErrorPre($pre);

    /**
     * Determine if error to display before the element
     *
     * @return boolean
     */
    public function isErrorPre();

    /**
     * Set validators
     *
     * @param  array $validators
     * @return ElementInterface
     */
    public function setValidators(array $validators = []);

    /**
     * Clear errors
     *
     * @return ElementInterface
     */
    public function clearErrors();

    /**
     * Get form element name
     *
     * @return string
     */
    public function getName();

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType();

    /**
     * Get form element value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get form element label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get the attributes of the form element label
     *
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators();

    /**
     * Get whether the form element is required
     *
     * @return boolean
     */
    public function isRequired();

    /**
     * Get whether the form element is disabled
     *
     * @return boolean
     */
    public function isDisabled();

    /**
     * Get whether the form element is readonly
     *
     * @return boolean
     */
    public function isReadonly();

    /**
     * Get whether the form element object is a button
     *
     * @return boolean
     */
    public function isButton();

    /**
     * Get form element errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get if form element has errors
     *
     * @return array
     */
    public function hasErrors();

    /**
     * Add a validator the form element
     *
     * @param  mixed $validator
     * @return ElementInterface
     */
    public function addValidator($validator);

    /**
     * Validate the form element
     *
     * @throws Exception
     * @return boolean
     */
    public function validate();

}
