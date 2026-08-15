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
namespace Pop\Form\Element\Input;

/**
 * Form CSRF element class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
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

        $this->setName($name);

        // If a token does not exist for this field name, create one
        if (!isset($_SESSION['pop_csrf'][$name])) {
            $this->createNewToken($value, $expire);
        // Else, retrieve existing token
        } else {
            $this->token = $_SESSION['pop_csrf'][$name];

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
     * Tokens are namespaced in the session by field name, so multiple CSRF-protected
     * forms (or fields) can coexist in the same session without clobbering each other.
     *
     * @param  ?string $value
     * @param  int     $expire
     * @return Csrf
     */
    public function createNewToken(?string $value = null, int $expire = 300): Csrf
    {
        $this->token = [
            'value'  => $value ?? bin2hex(random_bytes(32)),
            'expire' => (int)$expire,
            'start'  => time()
        ];

        if (!isset($_SESSION['pop_csrf']) || !is_array($_SESSION['pop_csrf'])) {
            $_SESSION['pop_csrf'] = [];
        }
        $_SESSION['pop_csrf'][$this->name] = $this->token;

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
                $qData = '';
                while ($data = fread($input, 1024)) {
                    $qData .= $data;
                }

                parse_str($qData, $queryData);
        }

        // If there is query data, set validator to check the submitted value against the
        // real, server-side token value using a timing-safe comparison.
        if (count($queryData) > 0) {
            $expectedValue = $this->token['value'] ?? '';
            $this->addValidator(function ($value) use ($expectedValue) {
                return hash_equals($expectedValue, (string)$value) ? null : 'The security token does not match.';
            });
        }
    }

}
