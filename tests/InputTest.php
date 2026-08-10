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

    public function testFileAllowedTypesRejectsDisallowedExtension()
    {
        $_FILES['my_file'] = [
            'name' => 'malware.exe',
            'size' => 1000
        ];
        $input = new Input\File('my_file');
        $input->setAllowedTypes(['jpg', '.png']);
        $this->assertEquals(['jpg', 'png'], $input->getAllowedTypes());
        $this->assertTrue($input->hasAllowedTypes());
        $this->assertFalse($input->validate());
        $this->assertContains('The file type must be one of the following: jpg, png.', $input->getErrors());
    }

    public function testFileAllowedTypesAcceptsMatchingExtensionCaseInsensitive()
    {
        $_FILES['my_file'] = [
            'name' => 'photo.JPG',
            'size' => 1000
        ];
        $input = new Input\File('my_file');
        $input->setAllowedTypes(['jpg', 'png']);
        $this->assertTrue($input->validate());
    }

    public function testFileMaxSizeRejectsOversizedFile()
    {
        $_FILES['my_file'] = [
            'name' => 'photo.jpg',
            'size' => 6000000
        ];
        $input = new Input\File('my_file');
        $input->setMaxSize(5000000);
        $this->assertEquals(5000000, $input->getMaxSize());
        $this->assertTrue($input->hasMaxSize());
        $this->assertFalse($input->validate());
        $this->assertContains('The file size must be less than or equal to 5 MB.', $input->getErrors());
    }

    public function testFileMaxSizeAcceptsFileWithinLimit()
    {
        $_FILES['my_file'] = [
            'name' => 'photo.jpg',
            'size' => 1000
        ];
        $input = new Input\File('my_file');
        $input->setMaxSize(5000000);
        $this->assertTrue($input->validate());
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

    public function testValueIsNullByDefault()
    {
        $input = new Input\Text('my_text');

        $this->assertNull($input->getValue());
    }

    public function testCanSetValueToNull()
    {
        $input = new Input\Text('my_text');

        $input->setValue('foo');
        $input->setValue(null);

        $this->assertNull($input->getValue());
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

    public function testCsrf()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $input = new Input\Csrf('my_csrf');
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
    }

    public function testCsrfRejectsMismatchedToken()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        new Input\Csrf('csrf_token');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['csrf_token' => 'not-the-real-token'];
        $input = new Input\Csrf('csrf_token');
        $input->setValue($_POST['csrf_token']);

        $this->assertFalse($input->validate());
        $this->assertContains('The security token does not match.', $input->getErrors());
    }

    public function testCsrfValidatesMatchingToken()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $original    = new Input\Csrf('csrf_token');
        $realValue   = $original->getValue();

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['csrf_token' => $realValue];
        $input = new Input\Csrf('csrf_token');
        $input->setValue($_POST['csrf_token']);

        $this->assertTrue($input->validate());
        $this->assertFalse($input->hasErrors());
    }

    public function testCsrfTokensAreNamespacedPerFieldName()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $formA = new Input\Csrf('form_a_token');
        $formB = new Input\Csrf('form_b_token');

        $this->assertNotEquals($formA->getValue(), $formB->getValue());

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['form_b_token' => $formA->getValue()];
        $input = new Input\Csrf('form_b_token');
        $input->setValue($_POST['form_b_token']);

        $this->assertFalse($input->validate());
    }

    public function testCsrfRegeneratesExpiredToken()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        $original = new Input\Csrf('expiring_token', null, 60);
        $originalValue = $original->getValue();

        // Force the stored token to look like it was created long enough ago to have expired,
        // without relying on an actual sleep() in the test.
        $_SESSION['pop_csrf']['expiring_token']['start'] = time() - 3600;

        $renewed = new Input\Csrf('expiring_token', null, 60);
        $this->assertNotEquals($originalValue, $renewed->getValue());
    }

    public function testCsrfHandlesNonGetPostRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $input = new Input\Csrf('put_token');
        $this->assertInstanceOf('Pop\Form\Element\Input\Csrf', $input);
    }

    public function testCsrfThrowsExceptionWithoutRequestMethod()
    {
        $original = $_SERVER['REQUEST_METHOD'] ?? null;
        unset($_SERVER['REQUEST_METHOD']);

        try {
            $this->expectException('Pop\Form\Element\Input\Exception');
            new Input\Csrf('no_request_method_token');
        } finally {
            if ($original !== null) {
                $_SERVER['REQUEST_METHOD'] = $original;
            }
        }
    }
}
