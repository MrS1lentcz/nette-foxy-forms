nette-foxy-forms
================

**Generate nette form components using Doctrine2 entity annotations with validations and loading/saving model**

Requirements
------------

- Nette 2.0 and higher
- Doctrine2 and higher
- __toString Magic methods in every entity, which is used in select box

Installation
------------

TODO - MediaStorage

Using
------------

- model
- successUrl

You can set doctrine entity name as model and success-url only, which it will be redirect page after save model. Now you can enjoy the convenience foxy forms.
```php
class ProductForm extends Foxy\Form
{
	protected
		$model = 'Product',
		$successUrl = 'default';
}
```
- fields
- exclude

If you wish to remove from the form of one or more components, you can use the $exclude, or define your own list of components in the order using the $fields;
```php
class ProductForm extends Foxy\Form
{
	protected
		$model = 'Product',
		$successUrl = 'default',
		$exclude = array('name'),
		# OR (it's the same as $exclude above)
		$fields = array('id', 'price', 'category');
}
```
