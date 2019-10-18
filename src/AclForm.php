<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Acl\Acl;
use Pop\Acl\AclRole;

/**
 * ACL Form class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.4.0
 */

class AclForm extends Form
{

    /**
     * Acl object
     * @var Acl
     */
    protected $acl = null;

    /**
     * AclRole role objects
     * @var array
     */
    protected $roles = [];

    /**
     * Acl strict flag
     * @var boolean
     */
    protected $aclStrict = false;

    /**
     * Acl flag to manage how to display/manage fields
     * @var array
     */
    protected $permissions = [
        'display' => 'view', // permission to display a field
        'modify'  => 'edit'  // permission to modify a field
    ];

    /**
     * Set the Acl object
     *
     * @param  Acl     $acl
     * @return AclForm
     */
    public function setAcl(Acl $acl = null)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Set a AclRole object (alias method)
     *
     * @param  AclRole $role
     * @return AclForm
     */
    public function setRole(AclRole $role = null)
    {
        $this->roles[$role->getName()] = $role;
        return $this;
    }

    /**
     * Add a AclRole object
     *
     * @param  AclRole $role
     * @return AclForm
     */
    public function addRole(AclRole $role = null)
    {
        return $this->setRole($role);
    }

    /**
     * Add AclRole objects
     *
     * @param  array $roles
     * @return AclForm
     */
    public function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->setRole($role);
        }

        return $this;
    }

    /**
     * Set the Acl object as strict evaluation
     *
     * @param  boolean $strict
     * @return AclForm
     */
    public function setAclStrict($strict)
    {
        $this->aclStrict = (bool)$strict;
        return $this;
    }

    /**
     * Set the Acl field permissions
     *
     * @param  string $displayPermission
     * @param  string $modifyPermission
     * @return AclForm
     */
    public function setPermissions($displayPermission, $modifyPermission)
    {
        $this->permissions['display'] = $displayPermission;
        $this->permissions['modify']  = $modifyPermission;

        return $this;
    }

    /**
     * Is the Acl object set to strict evaluation
     *
     * @return boolean
     */
    public function isAclStrict()
    {
        return $this->aclStrict;
    }
    /**
     * Get field permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Render the form object
     *
     * @param  int     $depth
     * @param  string  $indent
     * @param  boolean $inner
     * @return mixed
     */
    public function render($depth = 0, $indent = null, $inner = false)
    {
        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->getAllFields() as $field) {
                $fieldName   = $field->getName();
                if ($this->acl->hasResource($fieldName)) {
                    $viewDenied = ($this->aclStrict) ?
                        $this->acl->isDeniedManyStrict($this->roles, $fieldName, $this->permissions['display']) :
                        $this->acl->isDeniedMany($this->roles, $fieldName, $this->permissions['display']);

                    if ($viewDenied) {
                        unset($fieldset[$fieldName]);
                    } else {
                        $modifyDenied = ($this->aclStrict) ?
                            $this->acl->isDeniedManyStrict($this->roles, $fieldName, $this->permissions['modify']) :
                            $this->acl->isDeniedMany($this->roles, $fieldName, $this->permissions['modify']);
                        if ($modifyDenied) {
                            $field->setReadonly(true);
                        }
                    }
                }
            }
        }

        return parent::render($depth, $indent, $inner);
    }

}