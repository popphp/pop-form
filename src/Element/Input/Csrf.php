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
 * Form CSRF element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.0
 */

class Csrf extends Hidden
{

    /**
     * Current token data
     * @var array
     */
    protected array $token = [];

    /**
     * Constructor
     *
     * Instantiate the CSRF input form element
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  int     $expire
     * @param  ?string $indent
     */
    public function __construct(string $name, ?string $value = null, int $expire = 300, ?string $indent = null)
    {
        // Start a session.
        if (session_id() == '') {
            session_start();
        }

        // If token does not exist, create one
        if (!isset($_SESSION['pop_csrf'])) {
            $this->createNewToken($value, $expire);
        // Else, retrieve existing token
        } else {
            $this->token = unserialize($_SESSION['pop_csrf']);

            // Check to see if the token has expired
            if ($this->token['expire'] > 0) {
                if (($this->token['expire'] + $this->token['start']) < time()) {
                    $this->createNewToken($value, $expire);
                }
            }
        }

        parent::__construct($name, $this->token['value'], $indent);
        $this->setRequired(true);
        $this->setValidator();
    }

    /**
     * Set the token of the csrf form element
     *
     * @param  ?string $value
     * @param  int     $expire
     * @return Csrf
     */
    public function createNewToken(?string $value = null, int $expire = 300): Csrf
    {
        $this->token = [
            'value'  => sha1(rand(10000, getrandmax()) . $value),
            'expire' => (int)$expire,
            'start'  => time()
        ];
        $_SESSION['pop_csrf'] = serialize($this->token);
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
            $val = (isset($queryData[$this->name])) ? $queryData[$this->name] : '';
            $this->addValidator(new \Pop\Validator\Equal($val, 'The security token does not match.'));
        }
    }

}
