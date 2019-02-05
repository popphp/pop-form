<?php

namespace Pop\Form\Test;

use Pop\Form;
use Pop\Acl;
use PHPUnit\Framework\TestCase;

class AclFormTest extends TestCase
{

    protected $fields = [
        'username' => [
            'type' => 'text',
            'label'=> 'Username',
            'attributes' => [
                'size' => 40
            ],
            'value' => 'admin'
        ],
        'password' => [
            'type' => 'password',
            'label'=> 'Password',
            'attributes' => [
                'size' => 40
            ]
        ],
        'first_name' => [
            'type'     => 'text',
            'label'    => 'First Name',
            'attributes' => [
                'size' => 40
            ],
            'value' => 'John'
        ],
        'last_name' => [
            'type'     => 'text',
            'label'    => 'Last Name',
            'attributes' => [
                'size' => 40
            ],
            'value' => 'Smith'
        ],
        'submit' => [
            'type' => 'submit',
            'value' => 'Submit'
        ]
    ];

    public function testAclFormConfig()
    {
        $acl      = new Acl\Acl();
        $admin    = new Acl\AclRole('admin');
        $editor   = new Acl\AclRole('editor');
        $username = new Acl\AclResource('username');
        $password = new Acl\AclResource('password');

        $acl->addRoles([$admin, $editor])
            ->addResources([$username, $password]);

        $acl->deny($editor, 'username', 'edit')
            ->deny($editor, 'password', 'view');

        $form = Form\AclForm::createFromConfig($this->fields);
        $form->setAcl($acl);
        $form->setRole($editor);
        $form->addRoles([$admin]);
        $form->setAclStrict(true);

        $form->setPermissions('index', 'modify');

        $permissions = $form->getPermissions();

        $this->assertTrue($form->isAclStrict());
        $this->assertEquals('index', $permissions['display']);
        $this->assertEquals('modify', $permissions['modify']);
    }

    public function testAclFormDeny()
    {
        $acl      = new Acl\Acl();
        $admin    = new Acl\AclRole('admin');
        $editor   = new Acl\AclRole('editor');
        $username = new Acl\AclResource('username');
        $password = new Acl\AclResource('password');

        $acl->addRoles([$admin, $editor])
            ->addResources([$username, $password]);

        $acl->deny($editor, 'username', 'edit')
            ->deny($editor, 'password', 'view');

        $form = Form\AclForm::createFromConfig($this->fields);

        $form->setAcl($acl);
        $form->addRole($editor);

        $formString = (string)$form;

        $this->assertNotContains('name="password"', $formString);
        $this->assertContains('<input type="text" name="username" id="username" value="admin" size="40" readonly="readonly" />', $formString);
    }

    public function testAclFormDenyStrict()
    {
        $acl      = new Acl\Acl();
        $admin    = new Acl\AclRole('admin');
        $editor   = new Acl\AclRole('editor');
        $username = new Acl\AclResource('username');
        $password = new Acl\AclResource('password');

        $acl->addRoles([$admin, $editor])
            ->addResources([$username, $password]);

        $acl->deny($editor, 'username', 'edit')
            ->deny($editor, 'password', 'view');

        $form = Form\AclForm::createFromConfig($this->fields);

        $form->setAcl($acl);
        $form->addRole($editor);
        $form->setAclStrict(true);

        $formString = (string)$form;

        $this->assertNotContains('name="password"', $formString);
        $this->assertContains('<input type="text" name="username" id="username" value="admin" size="40" readonly="readonly" />', $formString);
    }

}