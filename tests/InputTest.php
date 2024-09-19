<?php

namespace Pop\Form\Test;

use Pop\Form\Element\Input;
use Pop\Validator;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{

    public function testButton()
    {
        $input = new Input\Button('my_button');
        $this->assertInstanceOf('Pop\Form\Element\Input\Button', $input);
    }

    public function testColor()
    {
        $input = new Input\Color('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Color', $input);
    }

    public function testDate()
    {
        $input = new Input\Date('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Date', $input);
    }

    public function testMonth()
    {
        $input = new Input\Month('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Month', $input);
    }

    public function testSearch()
    {
        $input = new Input\Search('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Search', $input);
    }

    public function testTel()
    {
        $input = new Input\Tel('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Tel', $input);
    }

    public function testTime()
    {
        $input = new Input\Time('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Time', $input);
    }

    public function testWeek()
    {
        $input = new Input\Week('my_field');
        $this->assertInstanceOf('Pop\Form\Element\Input\Week', $input);
    }

    public function testCheckbox()
    {
        $input = new Input\Checkbox('my_checkbox', 'Red');
        $input->setValue('Red');
        $input->setValue('Green');
        $input->resetValue();
        $this->assertInstanceOf('Pop\Form\Element\Input\Checkbox', $input);
        $this->assertFalse($input->isChecked());
    }

    public function testDatalist()
    {
        $input = new Input\Datalist('my_datalist', [
            'foo', 'bar', 'baz'
        ], null, '    ');
        $this->assertInstanceOf('Pop\Form\Element\Input\Datalist', $input);

        ob_start();
        echo $input;
        $result = ob_get_clean();

        $this->assertStringContainsString('<datalist', $result);
        $this->assertStringContainsString('<datalist', $input->render());
    }

    public function testEmail()
    {
        $input = new Input\Email('my_email');
        $this->assertInstanceOf('Pop\Form\Element\Input\Email', $input);
    }

    public function testFile()
    {
        $_FILES['my_file'] = [
            'name' => 'foo.txt',
            'size' => 1000
        ];
        $input = new Input\File('my_file');
        $input->addValidator(new Validator\LessThan(500));
        $input->addValidator(new Validator\NotEqual('foo.txt'));
        $input->addValidator(function($value){
            return 'This is wrong';
        });
        $this->assertInstanceOf('Pop\Form\Element\Input\File', $input);
        $this->assertFalse($input->validate());
        $this->assertEquals(3, count($input->getErrors()));
    }

    public function testFileValidateRequired()
    {
        unset($_FILES['my_file']);
        $input = new Input\File('my_file');
        $input->setRequired(true);
        $this->assertInstanceOf('Pop\Form\Element\Input\File', $input);
        $this->assertFalse($input->validate());
        $this->assertEquals(1, count($input->getErrors()));
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
        $input = new Input\Password('my_password', '123456');
        $this->assertInstanceOf('Pop\Form\Element\Input\Password', $input);
        $this->assertFalse($input->getRenderValue());
        $this->assertStringNotContainsString('123456', $input->render());
    }

    public function testRadio()
    {
        $input = new Input\Radio('my_radio', 'Red');
        $input->setValue('Red');
        $input->setValue('Green');
        $input->resetValue();
        $this->assertInstanceOf('Pop\Form\Element\Input\Radio', $input);
        $this->assertFalse($input->isChecked());
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

    public function testAppend()
    {
        $input = new Input('username');
        $input->setAppend('Append');
        $this->assertEquals('Append', $input->getAppend());
    }

    public function testPrepend()
    {
        $input = new Input('username');
        $input->setPrepend('Prepend');
        $this->assertEquals('Prepend', $input->getPrepend());
    }

    public function testRemoveRequired()
    {
        $input = new Input('my_field');
        $input->setRequired(false);
        $this->assertFalse($input->isRequired());
    }

    public function testDisabled()
    {
        $input = new Input('my_field');
        $input->setDisabled(true);
        $this->assertTrue($input->isDisabled());
    }

    public function testRemoveDisabled()
    {
        $input = new Input('my_field');
        $input->setDisabled(false);
        $this->assertFalse($input->isDisabled());
    }

    public function testReadonly()
    {
        $input = new Input('my_field');
        $input->setReadonly(true);
        $this->assertTrue($input->isReadonly());
    }

    public function testRemoveReadonly()
    {
        $input = new Input('my_field');
        $input->setReadonly(false);
        $this->assertFalse($input->isReadonly());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCaptcha()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $input = new Input\Captcha('my_captcha');
        $input->setLabel('Enter Code');
        $this->assertInstanceOf('Pop\Form\Element\Input\Captcha', $input);
        $this->assertTrue(is_array($input->getToken()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testCsrf()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $input = new Input\Csrf('my_csrf');
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
    }
}
