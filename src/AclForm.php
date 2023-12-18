<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Form;

use Pop\Acl\Acl;
use Pop\Acl\AclRole;
use ReturnTypeWillChange;

/**
 * ACL Form class
 *
 * @category   Pop
 * @package    Pop\Form
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */

class AclForm extends Form
{

    /**
     * Acl object
     * @var ?Acl
     */
    protected ?Acl $acl = null;

    /**
     * AclRole role objects
     * @var array
     */
    protected array $roles = [];

    /**
     * Acl strict flag
     * @var bool
     */
    protected bool $aclStrict = false;

    /**
     * Acl flag to manage how to display/manage fields
     * @var array
     */
    protected array $permissions = [
        'display' => 'view', // permission to display a field
        'modify'  => 'edit'  // permission to modify a field
    ];

    /**
     * Set the Acl object
     *
     * @param  ?Acl $acl
     * @return AclForm
     */
    public function setAcl(?Acl $acl = null): AclForm
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Set a AclRole object (alias method)
     *
     * @param  ?AclRole $role
     * @return AclForm
     */
    public function setRole(?AclRole $role = null): AclForm
    {
        $this->roles[$role->getName()] = $role;
        return $this;
    }

    /**
     * Add a AclRole object
     *
     * @param  ?AclRole $role
     * @return AclForm
     */
    public function addRole(?AclRole $role = null): AclForm
    {
        return $this->setRole($role);
    }

    /**
     * Add AclRole objects
     *
     * @param  array $roles
     * @return AclForm
     */
    public function addRoles(array $roles): AclForm
    {
        foreach ($roles as $role) {
            $this->setRole($role);
        }

        return $this;
    }

    /**
     * Set the Acl object as strict evaluation
     *
     * @param  bool $strict
     * @return AclForm
     */
    public function setAclStrict(bool $strict): AclForm
    {
        $this->aclStrict = $strict;
        return $this;
    }

    /**
     * Set the Acl field permissions
     *
     * @param  string $displayPermission
     * @param  string $modifyPermission
     * @return AclForm
     */
    public function setPermissions(string $displayPermission, string $modifyPermission): AclForm
    {
        $this->permissions['display'] = $displayPermission;
        $this->permissions['modify']  = $modifyPermission;

        return $this;
    }

    /**
     * Is the Acl object set to strict evaluation
     *
     * @return bool
     */
    public function isAclStrict(): bool
    {
        return $this->aclStrict;
    }
    /**
     * Get field permissions
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Render the form object
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string|null
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string|null
    {
        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->getAllFields() as $field) {
                $fieldName   = $field->getName();
                if ($this->acl->hasResource($fieldName)) {
                    $viewDenied = ($this->aclStrict) ?
                        $this->acl->isDeniedMultiStrict($this->roles, $fieldName, $this->permissions['display']) :
                        $this->acl->isDeniedMulti($this->roles, $fieldName, $this->permissions['display']);

                    if ($viewDenied) {
                        unset($fieldset[$fieldName]);
                    } else {
                        $modifyDenied = ($this->aclStrict) ?
                            $this->acl->isDeniedMultiStrict($this->roles, $fieldName, $this->permissions['modify']) :
                            $this->acl->isDeniedMulti($this->roles, $fieldName, $this->permissions['modify']);
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