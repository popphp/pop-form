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
namespace Pop\Form;

use Pop\Form\Element\AbstractElement;

/**
 * Form fields config class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Fields
{

    /**
     * Map of field 'type' string => concrete input element class, for the input types
     * that only need the standard (name, value, indent) constructor signature.
     *
     * This is an explicit allowlist rather than resolving
     * `Pop\Form\Element\Input\{Ucfirst($type)}` dynamically via `class_exists()` — that
     * pattern would instantiate *any* class sitting in that namespace from a
     * config-supplied string, including non-element classes (e.g. `Element\Input\Exception`),
     * with no check afterward that the result is actually an `AbstractElement`.
     *
     * @var array<string, class-string>
     */
    private const array INPUT_ELEMENT_TYPES = [
        'color'    => Element\Input\Color::class,
        'date'     => Element\Input\Date::class,
        'email'    => Element\Input\Email::class,
        'file'     => Element\Input\File::class,
        'hidden'   => Element\Input\Hidden::class,
        'month'    => Element\Input\Month::class,
        'password' => Element\Input\Password::class,
        'reset'    => Element\Input\Reset::class,
        'search'   => Element\Input\Search::class,
        'submit'   => Element\Input\Submit::class,
        'tel'      => Element\Input\Tel::class,
        'text'     => Element\Input\Text::class,
        'time'     => Element\Input\Time::class,
        'url'      => Element\Input\Url::class,
        'week'     => Element\Input\Week::class,
    ];

    /**
     * Static factory method to create a field element object from a field config array
     *
     * @param  string $name
     * @param  array  $field
     * @throws Exception|Element\Exception
     * @return AbstractElement
     */
    public static function create(string $name, array $field): AbstractElement
    {
        if (!isset($field['type'])) {
            throw new Exception('Error: The field type was not set.');
        }

        $type         = $field['type'];
        $value        = $field['value'] ?? null;
        $values       = $field['values'] ?? [];
        $label        = $field['label'] ?? null;
        $indent       = $field['indent'] ?? null;
        $checked      = $field['checked'] ?? null;
        $selected     = $field['selected'] ?? null;
        $required     = $field['required'] ?? null;
        $disabled     = $field['disabled'] ?? null;
        $readonly     = $field['readonly'] ?? null;
        $attributes   = $field['attributes'] ?? null;
        $validators   = $field['validators'] ?? null;
        $render       = $field['render'] ?? false;
        $expire       = $field['expire'] ?? 300;
        $min          = $field['min'] ?? false;
        $max          = $field['max'] ?? false;
        $xmlFile      = $field['xml'] ?? null;
        $hint         = $field['hint'] ?? null;
        $hintAttribs  = $field['hint-attributes'] ?? null;
        $labelAttribs = $field['label-attributes'] ?? null;
        $prepend      = $field['prepend'] ?? null;
        $append       = $field['append'] ?? null;
        $legend       = $field['legend'] ?? null;
        $container    = $field['container'] ?? null;
        $errorPre     = (isset($field['error']) && ($field['error'] == 'pre'));
        $allowedTypes = $field['allowed-types'] ?? null;
        $maxSize      = $field['max-size'] ?? null;

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
                $element = new Element\CheckboxSet($name, $values, $checked, $indent, $container);
                break;
            case 'radio':
                $element = new Element\Input\Radio($name, $value, $indent);
                if (($checked === true) || ($value == $checked)) {
                    $element->check();
                }
                break;
            case 'radio-set':
                $element = new Element\RadioSet($name, $values, $checked, $indent, $container);
                break;
            case 'csrf':
                $element = new Element\Input\Csrf($name, $value, $expire, $indent);
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
                $typeKey = strtolower($type);
                if (!isset(self::INPUT_ELEMENT_TYPES[$typeKey])) {
                    throw new Exception('Error: That class for that form element (' . $type . ') does not exist.');
                }
                $class   = self::INPUT_ELEMENT_TYPES[$typeKey];
                $element = new $class($name, $value);
                if ($class === Element\Input\Password::class) {
                    $element->setRenderValue($render);
                }
        }

        // Set the label.
        if ($label !== null) {
            $element->setLabel($label);
        }
        // Set the label attributes.
        if (($labelAttribs !== null) && is_array($labelAttribs)) {
            $element->setLabelAttributes($labelAttribs);
        }
        // Set prepend content.
        if ($prepend !== null) {
            $element->setPrepend($prepend);
        }
        // Set append content.
        if ($append !== null) {
            $element->setAppend($append);
        }
        // Set legend content.
        if ($legend !== null) {
            $element->setLegend($legend);
        }
        // Set the hint.
        if ($hint !== null) {
            $element->setHint($hint);
        }
        // Set the hint attributes.
        if (($hintAttribs !== null) && is_array($hintAttribs)) {
            $element->setHintAttributes($hintAttribs);
        }
        // Set if required.
        if (($required !== null) && ($required)) {
            $element->setRequired($required, ($field['required_message'] ?? 'This field is required.'));
        }
        // Set if disabled.
        if (($disabled !== null) && ($disabled)) {
            $element->setDisabled($disabled);
        }
        // Set if readonly.
        if (($readonly !== null) && ($readonly)) {
            $element->setReadonly($readonly);
        }

        $element->setErrorPre($errorPre);

        // Set file-specific options.
        if ($element instanceof Element\Input\File) {
            if (is_array($allowedTypes)) {
                $element->setAllowedTypes($allowedTypes);
            }
            if ($maxSize !== null) {
                $element->setMaxSize((int)$maxSize);
            }
        }

        // Set any attributes.
        if ($attributes !== null) {
            if ($element instanceof Element\CheckboxSet) {
                $element->setCheckboxAttributes($attributes);
            } else if ($element instanceof Element\RadioSet) {
                $element->setRadioAttributes($attributes);
            } else {
                $element->setAttributes($attributes);
            }
        }
        // Set any validators.
        if ($validators !== null) {
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
     * @param  array  $tableInfo
     * @param  ?array $attribs
     * @param  ?array $config
     * @param  mixed  $omit
     * @throws Exception
     * @return array
     */
    public static function getConfigFromTable(
        array $tableInfo, ?array $attribs = null, ?array $config = null, mixed $omit = null
    ): array
    {
        $fields = [];

        if (!isset($tableInfo['tableName']) || !isset($tableInfo['columns'])) {
            throw new Exception('Error: The table info parameter is not in the correct format');
        }

        if ($omit !== null) {
            if (!is_array($omit)) {
                $omit = [$omit];
            }
        } else {
            $omit = [];
        }

        if ($config === null) {
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
                    if ($validators !== null) {
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

                if ($attribs !== null) {
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
