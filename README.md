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
- fieldsets

If you wish to remove from the form of one or more components, you can use the $exclude, or define your own list of components in the order using the $fields.
For adding fields to fieldsets you have to overload $fieldsets property.
```php
class ProductForm extends Foxy\Form
{
	protected
		$model = 'Product',
		$successUrl = 'default',

		$exclude = array('name'),

		# OR (it's the same as $exclude above)
		$fields = array('id', 'price', 'category');

		# OR use fieldsets
		$fieldsets = array(
			'main' => array('id, name'),
			'additional' => array('price', 'category')
		);
}
```

- validation
	- FOXY_NO_VALIDATION - generate form components only without any validations
	- FOXY_NULLABLE - set components as required if fields are not nullable
	- FOXY_IS_INT and FOXY_IS_FLOAT - checks numeric validity of input
	- FOXY_MAX_LENGTH - max length for string fields
	- FOXY_HTML5_SUPPORT - add html5 attributes
	- FOXY_UNIQUE - unique checks in save model phase
	- FOXY_VALIDATE_ALL - apply all avaliable validations

- validationMessages

- getMessage($field, $level)


Validation and their messages are so flexible. You can customize validation level, global error messages and error messages for specific fields very easily.

```php
class ProductForm extends Foxy\Form
{
    protected $validationMessages = array(
        FOXY_NULLABLE   => 'Item is required',
        FOXY_IS_INT     => 'Has to be an integer',
        FOXY_IS_FLOAT   => 'has to be a float',
        FOXY_MAX_LENGTH => 'Text is too long',
        FOXY_UNIQUE     => 'My custom message for unique error', # My custom unique error message
    );

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        parent::__construct($em);
		$this->validationMessages[FOXY_NULLABLE] = 'Must fill this field, boy!';
	}

    public function getErrorMessage($field, $level)
    {
		if ($field == 'name' && $level == FOXY_NULLABLE) {
			return 'Name is required';
		}
    }
}
```

- getFkValues($field, $repository)

- setFieldComponent($field)

If you need custom filter for select box data or form component builder for specific fields
```php
class ProductForm extends Foxy\Form
{
    public function getFkValues($field, $repository)
    {
		if ($field == 'category') {
			return $repository->getProductCategories();
		}
    }

    public function setFieldComponent($field)
    {
		if ($field == 'name') {
			$this->addTextarea('name', 'Very long name');
		}
    }
}
```
