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
        $input = new Input\Checkbox('my_checkbox');
        $this->assertInstanceOf('Pop\Form\Element\Input\Checkbox', $input);
    }

    public function testDatalist()
    {
        $input = new Input\Datalist('my_datalist', [
            'foo', 'bar', 'baz'
        ]);
        $this->assertInstanceOf('Pop\Form\Element\Input\Datalist', $input);

        ob_start();
        echo $input;
        $result = ob_get_clean();

        $this->assertContains('<datalist', $result);
        $this->assertContains('<datalist', $input->render());
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
