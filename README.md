nette-foxy-forms
================

**Z anotaci Doctrine entit generuje formularove komponenty s validacemi, nacitanim a ukladanim samotnych entit vcetne vsech vazeb a podpory file uploadu**

Pozadavky
------------

- Nette 2.0 nebo vyssi
- Doctrine2 neby vyssi
- __toString Magickou methodu v kazde entite
- v konstruktoru kazde entity inicializace "toMany" vazeb pomoci Doctrine\Common\Collections\ArrayCollection()
- [PHP-GD](http://www.php.net/manual/en/image.installation.php) pokud chceme pouzivat resizer media macra

Instalace
------------

Nejjednodussi cesta pro ziskani nette-foxy-forms je pomoci [Composeru](http://getcomposer.org/)

```sh
	$ composer require mrS1lentcz/nette-foxy-forms:@dev-master
```

Konfigurace
------------

- config.neon

Toto nastaveni slouzi ke generovani spravnych url k souborum a ukladani do spravnych destinaci. Je take dobrym zvykem v konfiguraci web serveru pouzivat pro media url vlastni nastaveni, napriklad cachovani, apod. Prvni parametr pro sluzbu mediaStorage je absolutni cesta do adresare, kam chceme soubory ukladat, druhy nepovinny, je pattern nazvu souboru, ktery v zakladu generuje nazvy ve tvaru IMG_%04d.EXT. Pote staci nastavit mediaControleru url do uloziste a v druhem parametru predat mediaStorage.


```yaml
services:
    mediaStorage: Foxy\Media\Storage('/www/my_project/media/')

    mediaControler: Foxy\Media\Controler('/media', @mediaStorage)

    # Priklad pouziti subdomeny
    mediaControler: Foxy\Media\Controler('http://media.mujprojekt.cz/', @mediaStorage)
```

Pokud chceme mit k dispozici i media macro, pak jej musime zaregistrovat do latte, avsak k pouzivani media macra vas nikdo nenuti a tak tato konfigurace neni povinna.

```yaml
    services:
        nette.latte:
            class: Nette\Latte\Engine
            setup:
                - 'Foxy\Macros\Media::install(?->getCompiler())'(@self)
```

- presenter

U starich verzi Nette nez 2.1 je potreba manualne injectovat Media Controler.

```php
class HomepagePresenter extends Nette\Application\UI\Presenter
{
    protected $mediaControler;

    public function injectMediaStorage(\Foxy\Media\Controler $mediaControler)
    {
        if ($this->mediaControler) {
            throw new \Nette\InvalidStateException('Foxy\Media\Controler has already been set');
        }
        $this->mediaStorage = $mediaControler;
    }
}
```

Pouziti
------------

- model
- successUrl

Foxy formy se pouzivaji stejne jako klasicke Nette UI formy v tovarnickach na komponenty. Staci definovat pouze nazev modelu (entity) a successUrl pro presmerovani po uspesnem ulozeni a o vse ostatni se foxy form postara. Jednoduche, ze?

```php
class ProductForm extends Foxy\Forms\Form
{
    protected
        $model = 'Product',
        $successUrl = 'default';
}

class HomepagePresenter extends Nette\Application\UI\Presenter
{
    createComponentProductForm()
    {
        return new ProductForm();
    }
}
```

- successInsert
- successUpdate
- errorInsert
- errorUpdate

Flash messages po zpracovani formulare

```php
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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
    - FOXY_UPLOAD_TYPE - validuje typ uploadovaneho souboru u upload widgetu (typ image)
    - FOXY_EMAIL - validuje email widgety
    - FOXY_VALIDATE_ALL - aplikuje vsechny podporovane validace

```php
class ProductForm extends Foxy\Forms\Form
{
    protected
        $validation = FOXY_MAX_LENGTH;
}
```

- excludeValidations

Deaktivaci jednotlivich pravidel muzeme provest deklaraci excludeValidations

```php
class ProductForm extends Foxy\Forms\Form
{
    protected
        $excludeValidations = array(FOXY_HTML5_SUPPORT);
}
```

- validationMessages
- getErrorMessage($field, $level)

Validace a jejich chybove zpravy jsou velice flexibilni. Muzete jednoduse nastavit uroven validace aktualniho formulare, globalni validacni zpravy, ale i validacni zpravy pro konkretni komponenty.

```php
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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

Pro kazdy field lze specifikovat widget, ktery pretizi nativni typ z doctrine anotace. Widget lze upravovat pomoci Foxy\Annotation\Widget anotaci. Podporovane jsou nasledujici:

    - upload
    - image
    - password
    - email

```php
class ProductEntity
{
    /**
     * @Column(type="string",nullable=true)
     * @Foxy\Annotations\Widget(type="image")
     */
    protected $image;
}
```

- annotationNamespace

Muzeme pouzit i zapis bez namespace, avsak tuto volbu musime nastavit ve formulari prepinacem annotationNamespace.

```php
class ProductForm extends Foxy\Forms\Form
{
    protected $annotationNamespace = FALSE;
}

class ProductEntity
{
    /**
     * @Column(type="string",nullable=true)
     * @Widget(type="image")
     */
    protected $image;
}
```

- uploadTo

Nastaveni globalni cesty pro ukladani s podporou date masek. Pokud potrebujeme nastavit jinou cestu pro konkretni komponentu, lze definovat metodu getUploadTo. Pro vypnuti uploadu pro dany prvek musi tato metoda vracet FALSE.

```php
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
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
class ProductForm extends Foxy\Forms\Form
{
    protected
        $submitButton = 'odeslat';
}
```

```php
class ProductForm extends Foxy\Forms\Form
{
    protected
        # anebo bez odesilaciho tlactika
        $submitButton = NULL;
}
```

- uploadWrapper
- uploadSeparator

Upload/image widgety v zakladu renderuji link na uploadovany dokument. Toto chovani muzeme ovlivnit redeklaraci uploadWrapper a to s hodnotnou NULL, ktera vypne renderovani linku nebo vlastnim wrapperem. Wrapper a separator mohou byt stringy, ktere budou argumenty pro \Nette\Utils\Html, anebo primo instance teto tridy.

```php
class ProductForm extends Foxy\Forms\Form
{
    public
        $uploadWrapper = 'span';

    protected function attached($presenter)
    {
        parent::attached($presenter);
        $this->uploadSeparator = \Nette\Utils\Html::el('br');
    }
}
```

- getUrlParams()

V pripade, ze bychom chteli po ulozeni nebo vytvoreni entity presmerovat na jeji detail, tezko bychom skladali tento dynamicky odkaz pres $successUrl property, proto byla zavedena nepovinna metoda getUrlParams(), ktera kdyz je definovana, musi vracet prazdne ci naplnene pole url parametru.

```php
class ProductForm extends Foxy\Forms\Form
{
    protected
        $successUrl = 'detail';

    protected function getUrlParams()
    {
        return array('id' => $this->instance->id);
    }
}
```

- flashMessage($status = 'success')

Budeme-li chtit jednoduse vyvolat flash message po zpracovani formulare, kde jsme pretizili saveModel metodu, muzeme pouzit invokeFlashMessage

```php
class ProductForm extends Foxy\Forms\Form
{
    public function saveModel($form, $commit = TRUE)
    {
        parent::saveModel($form, $commit = FALSE);

        # ...

        try {
            $this->em->flush();
            $this->flashMessage();
        } catch(\Exception $e) {
            $this->flashMessage('error');
        }

        $this->presenter->redirect($this->successUrl);
}
```

Pokud jsme si zaregistrovali media macro, muzeme jej v latte pouzit ke zkompletovani absolutni url uploadovaneho dokumentu dle konfigurace.

```html

	<img src="{media $entity->image}"  />

```

Pres media macro muzeme takze jednoduse menit rozmery obrazku. Lze pouzit zapis s pojmenovanymi parametry, anebo anonymnimi, avsak oba tyto zpusoby nelze kombinovat.

```html

	<!-- Obrazek o rozmeru 100x200, ktery bude zmensen a orezan  -->
	<img src="{media $entity->image, 100, 200, TRUE}"  />

	<!-- Obrazek o rozmeru 150x300, ktery bude zmensen v pomeru sran -->
	<img src="{media $entity->image, 150, 300, FALSE}"  />

	<!-- Obrazek o rozmeru nx25, ktery bude zmensen v pomeru sran -->
	<img src="{media $entity->image, height=>25}"  />

	<!-- Obrazek o rozmeru nx100, ktery bude zmensen a orezan (defaultne) -->
	<img src="{media $entity->image, NULL, 100}"  />

	<!-- Obrazek o rozmeru 50x100, ktery bude zmensen bez pomeru stran -->
	<img src="{media $entity->image, 50, 100, Nette\Image::STRETCH}"  />

```

TODO
----

- help_text
- LoginForm
- ChangePasswordForm
- ForggotenPasswordForm
- PasswordRecoveryForm
- CreateForm
- UpdateForm
