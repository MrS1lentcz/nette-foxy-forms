nette-foxy-forms
================

**Z anotaci Doctrine entit generuje formularove komponenty s validacemi, nacitanim a ukladanim samotnych entit vcetne vsech vazeb a podpory file uploadu**

Pozadavky
------------

- Nette 2.0 nebo vyssi
- Doctrine2 neby vyssi
- __toString Magickou methodu v kazde entite
- v konstruktoru kazde entity inicializace "toMany" vazeb pomoci Doctrine\Common\Collections\ArrayCollection()

Instalace
------------

- config.neon

Prvni argument je media url, druhy media directory. Toto nastaveni slouzi ke generovani spravnych url k souborum a ukladani do spravnych destinaci. Je take dobrym zvykem v konfiguraci web serveru pouzivat pro media url vlastni nastaveni, napriklad cachovani, apod.

```yaml
services:
    mediaStorage: Foxy\MediaStorage('/media', '/www/my_project/media')

    # Priklad pouziti subdomeny
    mediaStorage: Foxy\MediaStorage('http://media.mujprojekt.cz/', '/www/mujproject/media')
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

Staci definovat pouze nazev modelu (entity) a successUrl pro presmerovani po uspesnem ulozeni a o vse ostatni se foxy form postara. Jednoduche, ze?

```php
class ProductForm extends Foxy\Form
{
    protected
        $model = 'Product',
        $successUrl = 'default';
}
```

- successInsert
- successUpdate
- errorInsert
- errorUpdate

Flash messages po zpracovani formulare

```php
class ProductForm extends Foxy\Form
{
    protected
        $successInsert = 'Produkt byl uspesne vytvoren',
        $successUpdate = 'Produkt byl uspesne upraven',
        $errorInsert = 'Produkt nebyl vytvoren',
        $errorUpdate = 'Produkt nebyl upraven';
}
```

- fields
- exclude


Jestli si prejete vyjmout jednu nebo vice komponent z formulare, muzete jejich vycet zapsat do $exclude nebo definovat svuj vlastni seznam komponent do $fields.

```php
class ProductForm extends Foxy\Form
{
    protected
        $model = 'Product',
        $successUrl = 'default',

        $exclude = array('name'),

        # nebo
        $fields = array('id', 'price', 'category');
}
```

- fieldsets

Pro vygenerovani fieldsetu musite definovat dvojrozmerne pole do $fieldsets, kde prvni uroven je vzdy nazev fieldsetu (skupiny) a druha vycet komponent v nem. Pokud je $fieldsets nenulovy, vzdy je nadrazeny $fields. Exclude je u fieldsetu ignorovane.


```php
class ProductForm extends Foxy\Form
{
    protected
        $model = 'Product',
        $successUrl = 'default',

        $fieldsets = array(
            'main' => array('id, name'),
            'additional' => array('price', 'category')
        );
}
```

- readOnly

V urcitych situacich potrebujeme mit formularove polozky pouze ke cteni at uz z duvodu nedostatecnych prav nebo kdyz jsou tyto polozky generovany. Toto lze ovlivnit deklaraci readOnly property. Vypis hodnot je proveden defaultne do "span" tagu vcetne serialize objektu (datetime, vazebni entita), anebo do "a" tagu v pripade, ze ma tvar url adresy (pridan blank), mailu (pridan mailto atribut), anebo se jedna o uploadovany soubor, v tom pripade dostaneme kompletni link na uploadovany dokument/obrazek.

```php
class ProductForm extends Foxy\Form
{
    protected
        $model = 'Product',
        $successUrl = 'default',
        $readOnly = array('author');
}
```

- validation
    - FOXY_NO_VALIDATION - generovani komponent bez validaci
    - FOXY_NULLABLE - nastavi komponenty jako povinne, pokud nejsou nullable
    - FOXY_IS_INT and FOXY_IS_FLOAT - kontroluje validitu numerickych vstupu
    - FOXY_MAX_LENGTH - nastavuje maximalni delku pro textove vstupy
    - FOXY_HTML5_SUPPORT - pridava html5 atributy
    - FOXY_UNIQUE - kontroluje unique bunky ve fazi ukladani entity
    - FOXY_VALIDATE_ALL - aplikuje vsechny podporovane validace

```php
class ProductForm extends Foxy\Form
{
    protected
        $validation = FOXY_MAX_LENGTH;
}
```

- excludedValidations

Deaktivaci jednotlivich pravidel muzeme provest deklaraci excludedValidations

```php
class ProductForm extends Foxy\Form
{
    protected
        $excludedValidations = array(FOXY_HTML5_SUPPORT);
}
```

- validationMessages
- getErrorMessage($field, $level)

Validace a jejich chybove zpravy jsou velice flexibilni. Muzete jednoduse nastavit uroven validace aktualniho formulare, globalni validacni zpravy, ale i validacni zpravy pro konkretni komponenty.

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

Jestlize potrebujete pouzit vlastni filtr pro select box data, staci definovat metodu getFkValues s nejakym prepinacem dle nazvu filedu - komponenty, ktera vrati pole entit.

```php
class ProductForm extends Foxy\Form
{
    public function getFkValues($field, $repository)
    {
        if ($field == 'category') {
            return $repository->getProductCategories();
        }
    }
}
```

- setFieldComponent($field)

Pro definici vlastni sady pravidel pro nejake komponenty zase slouzi setFieldComponent metoda.

```php
class ProductForm extends Foxy\Form
{
    public function setFieldComponent($field)
    {
        if ($field == 'name') {
            $this->addTextarea('name', 'Super specialni jmeno');
        }
    }
}
```

- widget

Pro kazdy field lze specifikovat widget, ktery pretizi nativni typ z doctrine anotace. V tuto chvili se to resi pomoci options (customSchemaOptions). Podporovane jsou nasledujici:

    - upload
    - image
    - password
    - email

```php
class ProductEntity
{
    /**
     * @Column(type="string",nullable=true,options={"widget"="image"})
     */
    protected $image;
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
            return 'logs/%y-%m-%d/';
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

- submitButton

Foxy forms automaticky pridava do formulare nakonec submit button s nazvem "send". Pro upraveni tohoto tlacitka slouzi property $submitButton. V pripade nastaveni hodnoty na NULL nebude submit button generovan.

```php
class ProductForm extends Foxy\Form
{
    protected
        $submitButton = 'odeslat';
}
```

```php
class ProductForm extends Foxy\Form
{
    protected
        # anebo bez odesilaciho tlactika
        $submitButton = NULL;
}
```

TODO
----

- media macro
- custom file upload/image component s podporou nahledu
- isNew() pro pohodlnejsi pouziti custom flash message po pretizeni saveModel
- getSuccessUrl() pro redirect na detail ci jinou dynamickou url
- LoginForm
- ChangePasswordForm
- ForggotenPasswordForm
- PasswordRecoveryForm
- CreateForm
- UpdateForm
