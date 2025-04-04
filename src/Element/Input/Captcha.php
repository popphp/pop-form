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
namespace Pop\Form\Element\Input;

/**
 * Form CAPTCHA element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.2
 */

class Captcha extends Text
{

    /**
     * Current token data
     * @var array
     */
    protected array $token = [];

    /**
     * Constructor
     *
     * Instantiate the captcha input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  ?string $captcha
     * @param  ?string $answer
     * @param  int     $expire
     * @param  ?string $indent
     */
    public function __construct(
        string $name, ?string $value = null, ?string $captcha = null, ?string $answer = null, int $expire = 300, ?string $indent = null
    )
    {
        // Start a session.
        if (session_id() == '') {
            session_start();
        }

        // If token does not exist, create one
        if (!isset($_SESSION['pop_captcha']) || (isset($_GET['captcha']) && ((int)$_GET['captcha'] == 1))) {
            $this->createNewToken($captcha, $answer, $expire);
        // Else, retrieve existing token
        } else {
            $this->token = unserialize($_SESSION['pop_captcha']);

            // Check to see if the token has expired
            if ($this->token['expire'] > 0) {
                if (($this->token['expire'] + $this->token['start']) < time()) {
                    $this->createNewToken($captcha, $value, $expire);
                }
            }
        }

        parent::__construct($name, strtoupper((string)$value), $indent);
        $this->setRequired(true);
        $this->setValidator();
    }

    /**
     * Set the token of the CAPTCHA form element
     *
     * @param  ?string $captcha
     * @param  ?string $answer
     * @param  int     $expire
     * @return Captcha
     */
    public function createNewToken(?string $captcha = null, ?string $answer = null, int $expire = 300): Captcha
    {
        if (($captcha === null) || ($answer === null)) {
            $captcha = $this->generateEquation();
            $answer  = $this->evaluateEquation($captcha);
        }

        $this->token = [
            'captcha' => $captcha,
            'answer'  => $answer,
            'expire'  => (int)$expire,
            'start'   => time()
        ];
        $_SESSION['pop_captcha'] = serialize($this->token);
        return $this;
    }

    /**
     * Get token
     *
     * @return array
     */
    public function getToken(): array
    {
        return $this->token;
    }

    /**
     * Set the label of the captcha form element
     *
     * @param  string $label
     * @return Captcha
     */
    public function setLabel(string $label): Captcha
    {
        parent::setLabel($label);

        if (isset($this->token['captcha'])) {
            if ((!str_contains($this->token['captcha'], '<img')) &&
                ((str_contains($this->token['captcha'], ' + ')) ||
                 (str_contains($this->token['captcha'], ' - ')) ||
                 (str_contains($this->token['captcha'], ' * ')) ||
                 (str_contains($this->token['captcha'], ' / ')))) {
                $this->label = $this->label . '(' .
                    str_replace([' * ', ' / '], [' &#215; ', ' &#247; '], $this->token['captcha'] .')');
            } else {
                $this->label = $this->label . $this->token['captcha'];
            }
        }

        return $this;
    }

    /**
     * Set the validator
     *
     * @throws Exception
     * @return void
     */
    protected function setValidator(): void
    {
        $this->validators = [];

        // Get query data
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new Exception('Error: The server request method is not set.');
        }

        $queryData = [];
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $queryData = $_GET;
                break;

            case 'POST':
                $queryData = $_POST;
                break;

            default:
                $input = fopen('php://input', 'r');
                $qData = null;
                while ($data = fread($input, 1024)) {
                    $qData .= $data;
                }

                parse_str($qData, $queryData);
        }

        // If there is query data, set validator to check against the token value
        if (count($queryData) > 0) {
            if (isset($queryData[$this->name])) {
                $this->addValidator(function($value){
                    $token = $this->getToken();
                    if (isset($token['answer']) && (strtoupper($token['answer']) == strtoupper($value))) {
                        return null;
                    } else {
                        return 'The answer is incorrect.';
                    }
                });
            }
        }
    }

    /**
     * Randomly generate a simple, basic equation
     *
     * @return string
     */
    protected function generateEquation(): string
    {
        $ops = [' + ', ' - ', ' * ', ' / '];
        $equation = null;

        $rand1 = rand(1, 10);
        $rand2 = rand(1, 10);
        $op    = $ops[rand(0, 3)];

        // If the operator is division, keep the equation very simple, with no remainder
        if ($op == ' / ') {
            $mod = ($rand2 > $rand1) ? $rand2 % $rand1 : $rand1 % $rand2;
            while ($mod != 0) {
                $rand1 = rand(1, 10);
                $rand2 = rand(1, 10);
                $mod   = ($rand2 > $rand1) ? $rand2 % $rand1 : $rand1 % $rand2;
            }
        }

        $equation = ($rand2 > $rand1) ? $rand2 . $op . $rand1 : $rand1 . $op . $rand2;

        return $equation;
    }

    /**
     * Evaluate equation
     *
     * @param  string $equation
     * @return int
     */
    protected function evaluateEquation(string $equation): int
    {
        return eval("return ($equation);");
    }

}
