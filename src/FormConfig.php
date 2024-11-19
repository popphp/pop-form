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
namespace Pop\Form;

use Pop\Utils;
use Pop\Validator\ValidatorInterface;

/**
 * Form config class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.0
 */

class FormConfig extends Utils\ArrayObject
{

    /**
     * Create array object from JSON string
     *
     * @param  string  $jsonString
     * @param  int     $depth
     * @param  int     $options
     * @return FormConfig
     */
    public static function createFromJson(string $jsonString, int $depth = 512, int $options = 0): FormConfig
    {
        $formConfig = parent::createFromJson($jsonString, $depth, $options)->toArray();
        $first      = reset($formConfig);

        if (!isset($first['type'])) {
            foreach ($formConfig as $key => $value) {
                foreach ($value as $ky => $vl) {
                    if (!empty($vl['validators'])) {
                        foreach ($vl['validators'] as $k => $v) {
                            $class = 'Pop\\Validator\\' . $v['type'];
                            $validator = new $class($v['value'], $v['message']);
                            if (!empty($v['input'])) {
                                $validator->setInput($v['input']);
                            }
                            $formConfig[$key][$ky]['validators'][$k] = $validator;
                        }
                    }
                }
            }
        } else {
            foreach ($formConfig as $key => $value) {
                if (!empty($value['validators'])) {
                    foreach ($value['validators'] as $k => $v) {
                        $class = 'Pop\\Validator\\' . $v['type'];
                        $validator = new $class($v['value'], $v['message']);
                        if (!empty($v['input'])) {
                            $validator->setInput($v['input']);
                        }
                        $formConfig[$key]['validators'][$k] = $validator;
                    }
                }
            }
        }


        return new self($formConfig);
    }

    /**
     * JSON serialize the array object
     *
     * @param  int $options
     * @param  int $depth
     * @return string
     */
    public function jsonSerialize(int $options = 0, int $depth = 512): string
    {
        $first = reset($this->data);
        if (!isset($first['type'])) {
            $this->filterFieldsetConfig();
        } else {
            $this->filterConfig();
        }
        return parent::jsonSerialize($options, $depth);
    }

    /**
     * Filter config validators
     *
     * @return FormConfig
     */
    public function filterConfig(): FormConfig
    {
        foreach ($this->data as $key => $value) {
            if (!empty($value['validator']) || !empty($value['validators'])) {
                $validators = (!empty($value['validator'])) ? $value['validator'] : $value['validators'];
                if (!is_array($validators)) {
                    $validators = [$validators];
                }
                foreach ($validators as $k => $validator) {
                    if ($validator instanceof ValidatorInterface) {
                        $validators[$k] = [
                            'type'    => str_replace('Pop\\Validator\\', '', get_class($validator)),
                            'input'   => $validator->getInput(),
                            'value'   => $validator->getValue(),
                            'message' => $validator->getMessage()
                        ];
                    } else {
                        unset($validators[$k]);
                    }
                }
                $this->data[$key]['validators'] = array_values($validators);
                if (isset($this->data[$key]['validator'])) {
                    unset($this->data[$key]['validator']);
                }
            }
        }

        return $this;
    }

    /**
     * Filter fieldset config validators
     *
     * @return FormConfig
     */
    public function filterFieldsetConfig(): FormConfig
    {
        foreach ($this->data as $key => $value) {
            foreach ($value as $ky => $vl) {
                if (!empty($vl['validator']) || !empty($vl['validators'])) {
                    $validators = (!empty($vl['validator'])) ? $vl['validator'] : $vl['validators'];
                    if (!is_array($validators)) {
                        $validators = [$validators];
                    }
                    foreach ($validators as $k => $validator) {
                        if ($validator instanceof ValidatorInterface) {
                            $validators[$k] = [
                                'type'    => str_replace('Pop\\Validator\\', '', get_class($validator)),
                                'input'   => $validator->getInput(),
                                'value'   => $validator->getValue(),
                                'message' => $validator->getMessage()
                            ];
                        } else {
                            unset($validators[$k]);
                        }
                    }
                    $this->data[$key][$ky]['validators'] = array_values($validators);
                    if (isset($this->data[$key][$k]['validator'])) {
                        unset($this->data[$key][$k]['validator']);
                    }
                }
            }
        }

        return $this;
    }

}
