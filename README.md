nette-foxy-forms
================

**Z anotaci Doctrine2 entit generuje formularove komponenty s validacemi, loadovanim a ukladanim samotnych entit vcetne vsech vazeb a podporou file uploadu**

Pozadavky
------------

- Nette 2.0 nebo vyssi
- Doctrine2 neby vyssi
- __toString Magickou methodu pro kazdou entitu, ktera je pouzita v select boxu

Instalace
------------

- config.neon

Prvni argument je media url, druhy media directory

```yaml
services:
	mediaStorage: Foxy\MediaStorage('/media', '/www/my_project/media')
```

- presenter

```php
	protected $mediaStorage;

    public function injectMediaStorage(\Foxy\MediaStorage $mediaStorage)
    {
        if ($this->mediaStorage)
        {
            throw new \Nette\InvalidStateException('Foxy\MediaStorage has already been set');
        }

        $this->mediaStorage = $mediaStorage;
    }
```

Pouziti
------------

- model
- successUrl

Staci definovat pouze nazev modelu (entity) a successUrl pro presmerovani po uspesnem ulozeni a o vse ostatni se foxy postara.

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

Jestli si prejete vyjmout jednu nebo vice komponent z formulare, muzete pouzit $exclude nebo definovat svuj vlastni seznam komponent do $fields.
Pro vygenerovani fieldsetu musite definovat dvojrozmerne pole do $filedsets, kde prvni uroven je vzdy nazev fieldsetu (skupiny) a druha vycet komponent v nem.

```php
class ProductForm extends Foxy\Form
{
	protected
		$model = 'Product',
		$successUrl = 'default',

		$exclude = array('name'),

		# nebo (je to same jako $exlude vyse)
		$fields = array('id', 'price', 'category');

		# nebo uziti fieldsetu
		$fieldsets = array(
			'main' => array('id, name'),
			'additional' => array('price', 'category')
		);
}
```

- validation
	- FOXY_NO_VALIDATION - generovani komponent bez validaci
	- FOXY_NULLABLE - nastavi komponenty jako povinne, pokud nejsou nullable
	- FOXY_IS_INT and FOXY_IS_FLOAT - kontroluje validitu numerickych vstupu
	- FOXY_MAX_LENGTH - nastavuje maximalni delku pro textove vstupy
	- FOXY_HTML5_SUPPORT - pridava html5 atributy
	- FOXY_UNIQUE - kontroluje unique bunky v save fazi
	- FOXY_VALIDATE_ALL - aplikuje vsechny uvedene validace

```php
class ProductForm extends Foxy\Form
{
	protected
		$validation = FOXY_MAX_LENGTH,
}
```

- validationMessages

- getErrorMessage($field, $level)

Validace a jejich chybove zpravy jsou velice flexibilni. Muzete jednoduse nastavit uroven validace, globalni validacni zpravy, ale i validacni zpravy pro konkretni komponenty.

```php
class ProductForm extends Foxy\Form
{
    protected $validationMessages = array(
        FOXY_NULLABLE   => 'Item is required',
        FOXY_IS_INT     => 'Has to be an integer',
        FOXY_IS_FLOAT   => 'has to be a float',
        FOXY_MAX_LENGTH => 'Text is too long',
        FOXY_UNIQUE     => 'Moje zprava pro unique chybu',
    );

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        parent::__construct($em);
		$this->validationMessages[FOXY_NULLABLE] = 'Tohle musis vyplnit!';
	}

    public function getErrorMessage($field, $level)
    {
		if ($field == 'name' && $level == FOXY_NULLABLE) {
			return 'Jmeno je povinna polozka';
		}
    }
}
```

- getFkValues($field, $repository)

- setFieldComponent($field)

Jestlize potrebujete pouzit vlastni filtr pro select box data nebo definovat vlastni sadu pravidel pro nejake komponenty, lze pouzit getFkValues a setFieldComponent metody.

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
			$this->addTextarea('name', 'Super specialni jmeno');
		}
    }
}
```

- uploadTo

Nastaveni globalni cesty pro ukladani s podporou date masek. Pokud potrebujeme nastavit jinou cestu pro konkretni komponentu, lze definovat metodu getUploadTo. Pro vypnuti uploadu pro dany prvek musi tato metoda vracet FALSE.

```php
class ProductForm extends Foxy\Form
{
	protected
		$uploadTo = 'images/%y-%m-%d/';

	protected function getUploadTo($name)
	{
		if ($name == 'logo') {
			return 'loga/%y-%m-%d/';
		}
		if ($name == 'image') {
			return FALSE;
		}
	}
}
```

Pro vlastni zpracovani 'image' komponenty je pak nutne podedit metodu saveModel a parentovi predat priznak $commit s hodnotou FALSE, aby se zabranilo flushi a redirectu po zavolani teto metody.

```php
class ProductForm extends Foxy\Form
{
	public function saveModel($form, $commit = TRUE)
	{
		parent::saveModel($form, $commit = FALSE);

		# ...

		$this->em->flush();
		$this->presenter->redirect('to:hell');
}
```





