<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Input;

class InputTest extends \PHPUnit_Framework_TestCase
{

    public function testButton()
    {
        $input = new Input\Button('my_button');
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $input);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptchaNoMethodException()
    {
        $this->setExpectedException('Pop\Form\Element\Input\Exception');
        $input = new Input\Captcha('my_captcha');
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptcha()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $input = new Input\Captcha('my_captcha');
        $input->setToken([
            'captcha' => '5 x 2',
            'value'   => 'test',
            'expire'  => 300,
            'start'   => time()
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Captcha', $input);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptchaPost()
    {
        session_start();
        $token = [
            'captcha' => '5 x 2',
            'value'  => sha1(rand(10000, getrandmax()) . 'test'),
            'expire' => 1,
            'start'  => 1435250400
        ];
        $_SESSION['pop_captcha'] = serialize($token);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = new Input\Captcha('my_captcha');
        $this->assertInstanceOf('Pop\Form\Element\Input\Captcha', $input);
        unset($_SESSION['pop_captcha']);
    }

    public function testCheckbox()
    {
        $input = new Input\Checkbox('my_checkbox');
        $this->assertInstanceOf('Pop\Form\Element\Input\Checkbox', $input);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrfNoMethodException()
    {
        $this->setExpectedException('Pop\Form\Element\Input\Exception');
        $input = new Input\Csrf('my_csrf');
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrf()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $input = new Input\Csrf('my_csrf');
        $input->setToken([
            'value'  => 'test',
            'expire' => 300,
            'start'  => time()
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrfPost()
    {
        session_start();
        $token = [
            'value'  => sha1(rand(10000, getrandmax()) . 'test'),
            'expire' => 1,
            'start'  => 1435250400
        ];
        $_SESSION['pop_csrf'] = serialize($token);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = new Input\Csrf('my_csrf');
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
        unset($_SESSION['pop_csrf']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrfOtherMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $input = new Input\Csrf('my_csrf');
        $input->setToken([
            'value'  => 'test',
            'expire' => 300,
            'start'  => time()
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
    }

    public function testDatalist()
    {
        $input = new Input\Datalist('my_datalist', [
            'foo', 'bar', 'baz'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Datalist', $input);

        ob_start();
        $input->render();
        $result = ob_get_clean();

        $this->assertContains('<datalist', $result);
        $this->assertContains('<datalist', $input->render(true));
    }

    public function testEmail()
    {
        $input = new Input\Email('my_email');
        $this->assertInstanceOf('Pop\Form\Element\Input\Email', $input);
    }

    public function testFile()
    {
        $input = new Input\File('my_file');
        $this->assertInstanceOf('Pop\Form\Element\Input\File', $input);
    }

    public function testHidden()
    {
        $input = new Input\Hidden('my_hidden');
        $this->assertInstanceOf('Pop\Form\Element\Input\Hidden', $input);
    }

    public function testNumber()
    {
        $input = new Input\Number('my_number', 1, 10);
        $this->assertInstanceOf('Pop\Form\Element\Input\Number', $input);
    }

    public function testPassword()
    {
        $input = new Input\Password('my_password');
        $this->assertInstanceOf('Pop\Form\Element\Input\Password', $input);
    }

    public function testRadio()
    {
        $input = new Input\Radio('my_radio');
        $this->assertInstanceOf('Pop\Form\Element\Input\Radio', $input);
    }

    public function testRange()
    {
        $input = new Input\Range('my_range', 1, 10);
        $this->assertInstanceOf('Pop\Form\Element\Input\Range', $input);
    }

    public function testReset()
    {
        $input = new Input\Reset('my_reset');
        $this->assertInstanceOf('Pop\Form\Element\Input\Reset', $input);
    }

    public function testSubmit()
    {
        $input = new Input\Submit('my_submit');
        $this->assertInstanceOf('Pop\Form\Element\Input\Submit', $input);
    }

    public function testText()
    {
        $input = new Input\Text('my_text');
        $this->assertInstanceOf('Pop\Form\Element\Input\Text', $input);
    }

    public function testUrl()
    {
        $input = new Input\Url('my_url');
        $this->assertInstanceOf('Pop\Form\Element\Input\Url', $input);
    }

}
