<?php
declare(strict_types=1);
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

/**
 * Validator evaluation trait
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait ValidatorEvaluationTrait
{

    /**
     * Evaluate a validator (a Pop\Validator\ValidatorInterface instance or a callable)
     * against a value and return any resulting error messages
     *
     * @param  mixed $validator
     * @param  mixed $value
     * @param  array $formValues
     * @return array
     */
    protected function evaluateValidator(mixed $validator, mixed $value, array $formValues = []): array
    {
        $messages = [];

        if ($validator instanceof \Pop\Validator\ValidatorInterface) {
            if (!$validator->evaluate($value)) {
                $messages[] = $validator->getMessage();
            }
        } else if (is_callable($validator)) {
            $result = call_user_func_array($validator, [$value, $formValues]);
            if ($result instanceof \Pop\Validator\ValidatorInterface) {
                if (!$result->evaluate($value)) {
                    $messages[] = $result->getMessage();
                }
            } else if (is_array($result)) {
                foreach ($result as $val) {
                    if ($val instanceof \Pop\Validator\ValidatorInterface) {
                        if (!$val->evaluate($value)) {
                            $messages[] = $val->getMessage();
                        }
                    }
                }
            } else if ($result !== null) {
                $messages[] = $result;
            }
        }

        return $messages;
    }

}
