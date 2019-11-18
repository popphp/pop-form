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
namespace Pop\Form;

/**
 * Form fields config class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */
class Fields
{

    /**
     * Static factory method to create a field element object from a field config array
     *
     * @param  string $name
     * @param  array  $field
     * @throws Exception
     * @return Element\AbstractElement
     */
    public static function create($name, array $field)
    {
        if (!isset($field['type'])) {
            throw new Exception('Error: The field type was not set.');
        }

        $type         = $field['type'];
        $value        = (isset($field['value']))      ? $field['value']      : null;
        $values       = (isset($field['values']))     ? $field['values']     : [];
        $label        = (isset($field['label']))      ? $field['label']      : null;
        $indent       = (isset($field['indent']))     ? $field['indent']     : null;
        $checked      = (isset($field['checked']))    ? $field['checked']    : null;
        $selected     = (isset($field['selected']))   ? $field['selected']   : null;
        $required     = (isset($field['required']))   ? $field['required']   : null;
        $disabled     = (isset($field['disabled']))   ? $field['disabled']   : null;
        $readonly     = (isset($field['readonly']))   ? $field['readonly']   : null;
        $attributes   = (isset($field['attributes'])) ? $field['attributes'] : null;
        $validators   = (isset($field['validators'])) ? $field['validators'] : null;
        $render       = (isset($field['render']))     ? $field['render']     : false;
        $expire       = (isset($field['expire']))     ? $field['expire']     : 300;
        $captcha      = (isset($field['captcha']))    ? $field['captcha']    : null;
        $answer       = (isset($field['answer']))     ? $field['answer']     : null;
        $min          = (isset($field['min']))        ? $field['min']        : false;
        $max          = (isset($field['max']))        ? $field['max']        : false;
        $xmlFile      = (isset($field['xml']))        ? $field['xml']        : null;
        $hint         = (isset($field['hint']))       ? $field['hint']       : null;
        $hintAttribs  = (isset($field['hint-attributes'])) ? $field['hint-attributes']   : null;
        $labelAttribs = (isset($field['label-attributes'])) ? $field['label-attributes'] : null;

        $errorPre = (isset($field['error']) && ($field['error'] == 'pre'));

        // Initialize the form element.
        switch (strtolower($type)) {
            case 'button':
                $element = new Element\Button($name, $value, $indent);
                break;
            case 'select':
                $element = new Element\Select($name, $values, $selected, $xmlFile, $indent);
                break;
            case 'select-multiple':
                $element = new Element\SelectMultiple($name, $values, $selected, $xmlFile, $indent);
                break;
            case 'textarea':
                $element = new Element\Textarea($name, $value, $indent);
                break;
            case 'checkbox':
                $element = new Element\Input\Checkbox($name, $value, $indent);
                if (($checked === true) || ($value == $checked)) {
                    $element->check();
                }
                break;
            case 'checkbox-set':
                $element = new Element\CheckboxSet($name, $values, $checked, $indent);
                break;
            case 'radio':
                $element = new Element\Input\Radio($name, $value, $indent);
                if (($checked === true) || ($value == $checked)) {
                    $element->check();
                }
                break;
            case 'radio-set':
                $element = new Element\RadioSet($name, $values, $checked, $indent);
                break;
            case 'csrf':
                $element = new Element\Input\Csrf($name, $value, $expire, $indent);
                break;
            case 'captcha':
                $element = new Element\Input\Captcha($name, $value, $captcha, $answer, $expire, $indent);
                break;
            case 'input-button':
                $element = new Element\Input\Button($name, $value);
                break;
            case 'datalist':
                $element = new Element\Input\Datalist($name, $values, $value);
                break;
            case 'datetime':
                $element = new Element\Input\DateTime($name, $value);
                break;
            case 'datetime-local':
                $element = new Element\Input\DateTimeLocal($name, $value);
                break;
            case 'number':
                $element = new Element\Input\Number($name, $min, $max, $value);
                break;
            case 'range':
                $element = new Element\Input\Range($name, $min, $max, $value);
                break;
            default:
                $class = 'Pop\\Form\\Element\\Input\\' . ucfirst(strtolower($type));
                if (!class_exists($class)) {
                    throw new Exception('Error: That class for that form element (' . $type . ') does not exist.');
                }
                $element = new $class($name, $value);
                if ($class == 'Pop\\Form\\Element\\Input\\Password') {
                    $element->setRenderValue($render);
                }
        }

        // Set the label.
        if (null !== $label) {
            $element->setLabel($label);
        }
        // Set the label attributes.
        if ((null !== $labelAttribs) && is_array($labelAttribs)) {
            $element->setLabelAttributes($labelAttribs);
        }
        // Set the hint.
        if (null !== $hint) {
            $element->setHint($hint);
        }
        // Set the hint attributes.
        if ((null !== $hintAttribs) && is_array($hintAttribs)) {
            $element->setHintAttributes($hintAttribs);
        }
        // Set if required.
        if ((null !== $required) && ($required)) {
            $element->setRequired($required);
        }
        // Set if disabled.
        if ((null !== $disabled) && ($disabled)) {
            $element->setDisabled($disabled);
        }
        // Set if readonly.
        if ((null !== $readonly) && ($readonly)) {
            $element->setReadonly($readonly);
        }

        $element->setErrorPre($errorPre);

        // Set any attributes.
        if (null !== $attributes) {
            if ($element instanceof Element\CheckboxSet) {
                $element->setCheckboxAttributes($attributes);
            } else if ($element instanceof Element\RadioSet) {
                $element->setRadioAttributes($attributes);
            } else {
                $element->setAttributes($attributes);
            }
        }
        // Set any validators.
        if (null !== $validators) {
            if (is_array($validators)) {
                $element->addValidators($validators);
            } else {
                $element->addValidator($validators);
            }
        }

        return $element;
    }

    /**
     * Static factory method to get field configs from a database table
     *
     * @param  array $tableInfo
     * @param  array $attribs
     * @param  array $config
     * @param  mixed $omit
     * @throws Exception
     * @return array
     */
    public static function getConfigFromTable(array $tableInfo, array $attribs = null, array $config = null, $omit = null)
    {
        $fields = [];

        if (!isset($tableInfo['tableName']) || !isset($tableInfo['columns'])) {
            throw new Exception('Error: The table info parameter is not in the correct format');
        }

        if (null !== $omit) {
            if (!is_array($omit)) {
                $omit = [$omit];
            }
        } else {
            $omit = [];
        }

        if (null === $config) {
            $config = [];
        }

        foreach ($tableInfo['columns'] as $name => $value) {
            if (!in_array($name, $omit)) {
                $fieldValue = null;
                $fieldLabel = null;
                $attributes = null;

                if (isset($config[$name]) && isset($config[$name]['validators'])) {
                    $validators = (!is_array($config[$name]['validators'])) ?
                        [$config[$name]['validators']] : $config[$name]['validators'];
                } else {
                    $validators = null;
                }

                if (isset($config[$name]) && isset($config[$name]['type'])) {
                    $fieldType = $config[$name]['type'];
                } else if (stripos($name, 'password') !== false) {
                    $fieldType = 'password';
                } else if ((stripos($name, 'email') !== false) || (stripos($name, 'e-mail') !== false) ||
                    (stripos($name, 'e_mail') !== false)) {
                    $fieldType = 'email';
                    if (null !== $validators) {
                        $validators[] = new \Pop\Validator\Email();
                    } else {
                        $validators = [new \Pop\Validator\Email()];
                    }
                } else {
                    $fieldType = (stripos($value['type'], 'text') !== false) ? 'textarea' : 'text';
                }

                $fieldValue = (isset($config[$name]) && isset($config[$name]['value'])) ? $config[$name]['value'] : null;

                if ($fieldType != 'hidden') {
                    $fieldLabel = ucwords(str_replace('_', ' ', $name)) . ':';
                }

                if (null !== $attribs) {
                    if (isset($attribs[$fieldType])) {
                        $attributes =  $attribs[$fieldType];
                    }
                }

                $required = (isset($config[$name]) && isset($config[$name]['required'])) ?
                    (bool)$config[$name]['required'] : !($value['null']);

                $fields[$name] = [
                    'type'       => $fieldType,
                    'label'      => $fieldLabel,
                    'value'      => $fieldValue,
                    'required'   => $required,
                    'attributes' => $attributes,
                    'validators' => $validators
                ];
            }
        }

        return $fields;
    }

}