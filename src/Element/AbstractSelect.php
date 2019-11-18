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
namespace Pop\Form\Element;

/**
 * Abstract select element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.5.0
 */

abstract class AbstractSelect extends AbstractElement
{

    /**
     * Constant for months, short
     * @var string
     */
    const MONTHS_SHORT = 'MONTHS_SHORT';

    /**
     * Constant for days of the month
     * @var string
     */
    const DAYS_OF_MONTH = 'DAYS_OF_MONTH';

    /**
     * Constant for 12 hours
     * @var string
     */
    const HOURS_12 = 'HOURS_12';

    /**
     * Constant for 24 hours
     * @var string
     */
    const HOURS_24 = 'HOURS_24';

    /**
     * Constant for 60 minutes (0-59)
     * @var string
     */
    const MINUTES = 'MINUTES';

    /**
     * Constant for minutes in increments of 5
     * @var string
     */
    const MINUTES_5 = 'MINUTES_5';

    /**
     * Constant for minutes in increments of 10
     * @var string
     */
    const MINUTES_10 = 'MINUTES_10';

    /**
     * Constant for minutes in increments of 15
     * @var string
     */
    const MINUTES_15 = 'MINUTES_15';

    /**
     * Selected value(s)
     * @var mixed
     */
    protected $selected = null;

    /**
     * Set whether the form element is required
     *
     * @param  boolean $required
     * @return Select
     */
    public function setRequired($required)
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }
        return parent::setRequired($required);
    }

    /**
     * Set whether the form element is disabled
     *
     * @param  boolean $disabled
     * @return Select
     */
    public function setDisabled($disabled)
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
     * @param  boolean $readonly
     * @return Select
     */
    public function setReadonly($readonly)
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
            foreach ($this->childNodes as $childNode) {
                if ($childNode->getAttribute('selected') != 'selected') {
                    $childNode->setAttribute('disabled', 'disabled');
                } else {
                    $childNode->setAttribute('readonly', 'readonly');
                }
            }
        } else {
            $this->removeAttribute('readonly');
            foreach ($this->childNodes as $childNode) {
                $childNode->removeAttribute('disabled');
                $childNode->removeAttribute('readonly');
            }
        }

        return parent::setReadonly($readonly);
    }

    /**
     * Get form element object type
     *
     * @return string
     */
    public function getType()
    {
        return 'select';
    }

    /**
     * Get select form element selected value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->selected;
    }

    /**
     * Get select form element selected value (alias)
     *
     * @return mixed
     */
    public function getSelected()
    {
        return $this->getValue();
    }

    /**
     * Get select options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        foreach ($this->childNodes as $child) {
            if ($child instanceof Select\Option) {
                $options[] = $child;
            } else if ($child instanceof Select\Optgroup) {
                foreach ($child->getChildren() as $c) {
                    if ($c instanceof Select\Option) {
                        $options[] = $c;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Get select options as array
     *
     * @return array
     */
    public function getOptionsAsArray()
    {
        $options      = $this->getOptions();
        $optionsArray = [];

        foreach ($options as $option) {
            $optionsArray[$option->getValue()] = $option->getNodeValue();
        }

        return $optionsArray;
    }

    /**
     * Validate the form element object
     *
     * @return boolean
     */
    public function validate()
    {
        $value = $this->getValue();

        // Check if the element is required
        if (($this->required) && empty($value)) {
            $this->errors[] = 'This field is required.';
        }

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
                    $result = call_user_func_array($validator, [$value]);
                    if (null !== $result) {
                        if (!in_array($result, $this->errors)) {
                            $this->errors[] = $result;
                        }
                    }
                }
            }
        }

        return (count($this->errors) == 0);
    }

    /**
     * Set the select element as multiple
     *
     * @param  string|array $values
     * @param  string       $xmlFile
     * @return array
     */
    public static function parseValues($values, $xmlFile = null)
    {
        $parsedValues = null;

        // If the values are an array of values already
        if (is_array($values)) {
            $parsedValues = $values;
        // Else, if the value is a string
        } else if (is_string($values)) {
            // If the value flag is YEAR-based, calculate the year range for the select drop-down menu.
            if (strpos($values, 'YEAR') !== false) {
                $years = [];
                $yearAry = explode('_', $values);
                // YEAR_1111_2222 (from year 1111 to 2222)
                if (isset($yearAry[1]) && isset($yearAry[2])) {
                    if ($yearAry[1] < $yearAry[2]) {
                        for ($i = $yearAry[1]; $i <= $yearAry[2]; $i++) {
                            $years[$i] = $i;
                        }
                    } else {
                        for ($i = $yearAry[1]; $i >= $yearAry[2]; $i--) {
                            $years[$i] = $i;
                        }
                    }
                // YEAR_1111
                // If 1111 is less than today's year, then 1111 to present year,
                // else from present year to 1111
                } else if (isset($yearAry[1])) {
                    $year = date('Y');
                    if ($year < $yearAry[1]) {
                        for ($i = $year; $i <= $yearAry[1]; $i++) {
                            $years[$i] = $i;
                        }
                    } else {
                        for ($i = $year; $i >= $yearAry[1]; $i--) {
                            $years[$i] = $i;
                        }
                    }
                // YEAR, from present year to 10+ years
                } else {
                    $year = date('Y');
                    for ($i = $year; $i <= ($year + 10); $i++) {
                        $years[$i] = $i;
                    }
                }
                $parsedValues = $years;
            } else {
                // Else, if the value flag is one of the pre-defined , set the value of the select drop-down menu to it.
                switch ($values) {
                    // Hours, 12-hour values.
                    // Months, numeric short values.
                    case Select::HOURS_12:
                    case Select::MONTHS_SHORT:
                        $parsedValues = [
                            '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06',
                            '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12'
                        ];
                        break;
                    // Days of Month, numeric short values.
                    case Select::DAYS_OF_MONTH:
                        $parsedValues = [
                            '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05',
                            '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11',
                            '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17',
                            '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23',
                            '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29',
                            '30' => '30', '31' => '31'
                        ];
                        break;
                    // Military hours, 24-hour values.
                    case Select::HOURS_24:
                        $parsedValues = [
                            '00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05',
                            '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12',
                            '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19',
                            '20' => '20', '21' => '21', '22' => '22', '23' => '23'
                        ];
                        break;
                    // Minutes, incremental by 1 minute.
                    case Select::MINUTES:
                        $parsedValues = [
                            '00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05',
                            '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12',
                            '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19',
                            '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26',
                            '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31', '32' => '32', '33' => '33',
                            '34' => '34', '35' => '35', '36' => '36', '37' => '37', '38' => '38', '39' => '39', '40' => '40',
                            '41' => '41', '42' => '42', '43' => '43', '44' => '44', '45' => '45', '46' => '46', '47' => '47',
                            '48' => '48', '49' => '49', '50' => '50', '51' => '51', '52' => '52', '53' => '53', '54' => '54',
                            '55' => '55', '56' => '56', '57' => '57', '58' => '58', '59' => '59'
                        ];
                        break;
                    // Minutes, incremental by 5 minutes.
                    case Select::MINUTES_5:
                        $parsedValues = [
                            '00' => '00', '05' => '05', '10' => '10', '15' => '15', '20' => '20', '25' => '25',
                            '30' => '30', '35' => '35', '40' => '40', '45' => '45', '50' => '50', '55' => '55'
                        ];
                        break;
                    // Minutes, incremental by 10 minutes.
                    case Select::MINUTES_10:
                        $parsedValues = [
                            '00' => '00', '10' => '10', '20' => '20', '30' => '30', '40' => '40', '50' => '50'
                        ];
                        break;
                    // Minutes, incremental by 15 minutes.
                    case Select::MINUTES_15:
                        $parsedValues = ['00' => '00', '15' => '15', '30' => '30', '45' => '45'];
                        break;
                    // Else, set the custom array of values passed.
                    default:
                        if (null === $xmlFile) {
                            $xmlFile = __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'options.xml';
                        }
                        $parsedValues = self::parseXml($xmlFile, $values);
                }
            }
        }

        return $parsedValues;
    }

    /**
     * Static method to parse an XML file of options
     *
     * @param  string $xmlFile
     * @param  string $name
     * @return array
     */
    protected static function parseXml($xmlFile, $name)
    {
        $options = [];

        if (file_exists($xmlFile)) {
            $xml = new \SimpleXMLElement($xmlFile, null, true);
            $xmlValues = [];
            foreach ($xml->set as $node) {
                $xmlValues[(string)$node->attributes()->name] = [];
                foreach ($node->opt as $opt) {
                    $xmlValues[(string)$node->attributes()->name][(string)$opt->attributes()->value] = (string)$opt;
                }
            }
            if (array_key_exists($name, $xmlValues)) {
                $options = $xmlValues[$name];
            }
        }

        return $options;
    }

}
