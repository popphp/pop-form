CHANGELOG
=========

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
