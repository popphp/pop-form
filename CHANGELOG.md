CHANGELOG
=========

## 4.2.1

* [UPDATED] Updated to a minimum of PHP 7.3 and PHPUnit 9.0. Migrated from TravisCI to GitHub Actions for CI.  

## 3.5.0

* [ADDED] `AclForm` class to enforce ACL-based roles and permissions for which form fields are accessible by certain users.
* [REMOVED] the native filter classes in favor of using the newly refactored `pop-filter` component
* [ADDED] `FormValidator` class to simply perform field value validations without the weight of a full form object.
* [ADDED] `FormConfig` class for more robust support of form configuration
* [ADDED] Better support for conditional validation

## 3.4.0

* [ADDED] A light-weight form validator class for evaluating form values and returning error messages without the full
weight of a full form object and all of the HTML rendering.
* [CHANGED] Checkbox and radio elements can now be rendered as simple, single elements via a field config. `checkbox-set`
and `radio-set` have to be explicitly declared to order have the field sets render as before (Possible BC break.) 

## 3.3.0

* [ADDED] Support for ACL-enforced form objects

## 3.0.3

The `pop-form` v3 is a major version release that breaks backwards-compatibility is some areas. Chief among these changes
are the addition of fieldsets to support more syntactically correct HTML within the form structure.

* [ADDED] Support for HTML fieldsets to group fields together within the form structure
* [ADDED] Support for legends for the fieldsets
* [ADDED] The ability to group fieldsets in columns to assist with styling and positioning of fieldsets within the form structure.
* [REMOVED] The view/template part of the component has been removed in favor of using the `pop-view` compoment.
* [CHANGED/REMOVED] The following methods have been renamed to reflect the new support for HTML fieldsets within the form structure:

| Old Methods                 | New Methods                        |
|-----------------------------|------------------------------------|
| `addElement()`              | `addField()`                       |
| `addElements()`             | `addFields()`                      |
| `removeElement()`           | `removeField()`                    |
| `element()`                 | `getField()`                       |
| `elements()`                | `getFields()`                      |
| `getElement()`              | `getField()`                       |
| `getElements()`             | `getFields()`                      |
| `setFieldConfig()`          | `addFieldFromConfig()`             |
| `addFieldConfig()`          | `addFieldFromConfig()`             |
| `addFieldConfigs()`         | `addFieldsFromConfig()`            |
| `insertElementBefore()`     | `insertFieldBefore()`              |
| `insertElementAfter()`      | `insertFieldAfter()`               |
| `getFieldConfig()`          | **REMOVED**                        |
| `getFieldGroupConfig()`     | **REMOVED**                        |
| `hasFieldGroupConfig()`     | **REMOVED**                        |
| `insertFieldConfigBefore()` | **REMOVED**                        |
| `insertFieldConfigAfter()`  | **REMOVED**                        |
| `insertGroupConfigBefore()` | **REMOVED**                        |
| `insertGroupConfigAfter()`  | **REMOVED**                        |
| `getElementIndex()`         | **REMOVED**                        |

Please reference the documentation for further information on the changes to the component:

- http://docs.popphp.org/en/latest/user_guide/forms.html
- http://api.popphp.org/3.6/namespaces/Pop.Form.html
