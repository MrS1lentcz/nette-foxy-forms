<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */

namespace Foxy\Forms;


define('FOXY_NO_VALIDATION', 0);
define('FOXY_NULLABLE', 2);
define('FOXY_IS_INT', 4);
define('FOXY_IS_FLOAT', 8);
define('FOXY_MAX_LENGTH', 16);
define('FOXY_HTML5_SUPPORT', 32);
define('FOXY_UNIQUE', 64);
define('FOXY_UPLOAD_TYPE', 128);
define('FOXY_EMAIL', 256);
define('FOXY_VALIDATE_ALL', 510);
define('FOXY_ONE_TO_ONE', 1);
define('FOXY_MANY_TO_ONE', 2);
define('FOXY_ONE_TO_MANY', 4);
define('FOXY_MANY_TO_MANY', 8);


abstract class Form extends \Nette\Application\UI\Form
{
    /**
     * @var \Nette\Caching/Cache
     */
    private $cache;

    /**
     * @var \Foxy|RenderContext
     */
    private $renderContext;

    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var bool
     */
    private static $isInited = FALSE;

    /**
     * @var Doctrine\Common\Annotations\SimpleAnnotationReader
     */
    private static $simpleReader = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Foxy\Media\Controler
     */
    protected $mediaControler;

    /**
     * @var \Nette\Caching\IStorage
     */
    protected $cachingStorage;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var array | NULL
     */
    protected $fields;

    /**
     * @var array | NULL
     */
    protected $exclude;

    /**
     * @var array | NULL
     */
    protected $fieldsets;

    /**
     * @var int
     */
    protected $validation = FOXY_VALIDATE_ALL;

    /**
     * @var array | NULL
     */
    protected $excludeValidations;

    /**
     * @var array | NULL
     */
    protected $readOnly;

    /**
     * @var object
     */
    protected $instance;

    /**
     * @var string
     */
    protected $successUrl = 'default';

    /**
     * @var string
     */
    protected $uploadTo = 'images/%Y-%m-%d/';

    /**
     * @var string | NULL
     */
    protected $submitButton = 'send';

    /**
     * @var string
     */
    protected $successInsert = 'Model was created successfully';

    /**
     * @var string
     */
    protected $successUpdate= 'Model was edited successfully';

    /**
     * @var string
     */
    protected $errorInsert = 'Model was not created';

    /**
     * @var string
     */
    protected $errorUpdate = 'Model was not updated';

    /**
     * @var string | Nette\Utils\Html | NULL
     */
    protected $uploadWrapper = 'div';

    /**
     * @var string | Nette\Utils\Html | NULL
     */
    protected $uploadSeparator = 'br';

    /**
     * @var bool
     */
    protected static $annotationNamespace = TRUE;

    /**
     * @var bool
     */
    protected $enableCaching = FALSE;

    /**
     * @var bool
     */
    protected $enableTranslator = FALSE;

	/**
	 * @var string
	 */
	protected $datetimeFormat = 'Y-m-d H:i:s';

	/**
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d H:i:s';

	/**
	 * @var string
	 */
	protected $timeFormat = 'H:i:s';

    /**
     * @var array
     */
    protected $validationMessages = array(
        FOXY_NULLABLE   => 'Item is required',
        FOXY_IS_INT     => 'Has to be an integer',
        FOXY_IS_FLOAT   => 'has to be a float',
        FOXY_MAX_LENGTH => 'Text is too long',
        FOXY_UNIQUE     => 'Entered value is already used',
        FOXY_UPLOAD_TYPE=> 'Thubnail must be JPEG, PNG or GIF',
        FOXY_EMAIL      => 'Email is not valid',
    );

    /**
     * @var array
     */
    protected $componentsCallbackMap = array(
        'integer'           => 'Foxy\ControlsFactory::createInteger',
        'bigint'            => 'Foxy\ControlsFactory::createBigInteger',
        'smallint'          => 'Foxy\ControlsFactory::createSmallInteger',
        'string'            => 'Foxy\ControlsFactory::createString',
        'text'              => 'Foxy\ControlsFactory::createText',
        'decimal'           => 'Foxy\ControlsFactory::createDecimal',
        'float'             => 'Foxy\ControlsFactory::createDecimal',
        'boolean'           => 'Foxy\ControlsFactory::createBoolean',
        'datetime'          => 'Foxy\ControlsFactory::createDatetime',
        'date'              => 'Foxy\ControlsFactory::createDate',
        'time'              => 'Foxy\ControlsFactory::createTime',
        'blob'              => 'Foxy\ControlsFactory::createUpload',
        FOXY_ONE_TO_ONE     => 'Foxy\ControlsFactory::createSelectBox',
        FOXY_MANY_TO_ONE    => 'Foxy\ControlsFactory::createSelectBox',
        FOXY_ONE_TO_MANY    => 'Foxy\ControlsFactory::createMultipleSelectBox',
        FOXY_MANY_TO_MANY   => 'Foxy\ControlsFactory::createMultipleSelectBox',
        # Additional widgets
        'upload'            => 'Foxy\ControlsFactory::createUpload',
        'image'             => 'Foxy\ControlsFactory::createImage',
        'password'          => 'Foxy\ControlsFactory::createPassword',
        'email'             => 'Foxy\ControlsFactory::createEmail',
    );


    /**
     * Construct Foxy\Forms\Form
     *
     */
    public function __construct()
    {
        parent::__construct();

        if (self::$isInited == FALSE) {
            \Doctrine\Common\Annotations\AnnotationRegistry
                ::registerFile(__DIR__ . '/../Annotations/Widget.php');

            self::$simpleReader
                = new \Doctrine\Common\Annotations\SimpleAnnotationReader();

            if (! self::$annotationNamespace) {
                self::$simpleReader->addNamespace('Foxy\Annotations');
            }
            self::$isInited = TRUE;
        }

        $this->instance = new $this->model;

        if (is_array($this->excludeValidations)) {
            foreach($this->excludeValidations as $ex) {
                $this->validation ^= $ex;
            }
        }

        $this->onSuccess[] = array($this, 'saveModel');
    }


    /**
     * Creates form components after attached to presenter
     *
     * @param object
     */
    protected function attached($presenter)
    {
        parent::attached($presenter);

        if ($presenter instanceof \Nette\Application\UI\Presenter) {

			$context = $this->presenter->getContext();
			$this->em = $context->getByType('Doctrine\ORM\EntityManager');
			$this->mediaControler = $context->getByType('Foxy\Media\Controler');
			$this->cachingStorage = $context->getByType('Nette\Caching\IStorage');

			if ($this->enableTranslator) {
				$this->setTranslator($context->getByType('Nette\Localization\ITranslator'));
			}

            $this->cache = new \Nette\Caching\Cache(
                $this->cachingStorage,
                'nette-foxy-forms'
            );

            $this->properties = $this->cache->load(get_class($this).'_properties');
            if ($this->properties === NULL) {
                $this->properties = $this->getCompletedProperties();
            }

            foreach($this->properties as $property) {
                $this->createFieldComponent($property);
            }

            if ($this->enableCaching) {
                $this->cache->save(get_class($this).'_properties', $this->properties);
            }

            if ($this->submitButton) {
                $this->addSubmit($this->submitButton, $this->submitButton);
            }
        }
    }


    /**
     * Returns entity's identifier name
     *
     * @param string $entity
     * @param int $idNum
     * @return string
     */
    protected function getIdentifier($entity, $idNum = 0)
    {
		$identfiers = $this->em->getClassMetadata($entity)->getIdentifier();
        return $identfiers[$idNum];
    }


    /**
     * Get related data for select box
     *
     * @param mixed $entity
     * @param string $fieldName
     * @return array
     */
    protected function getSelectData($entity, $fieldName)
    {
        $data = NULL;
        if (method_exists($this, 'getFkValues')) {
            $data = $this->getFkValues(
                $fieldName,
                $this->em->getRepository($entity)
            );
        }
        if (is_null($data)) {
            $data = $this->em->getRepository($entity)->findAll();
        }

        $rc = $this->em->getClassMetadata($entity)->getReflectionClass();
        $rp = $rc->getProperty($this->getIdentifier($entity));
        $rp->setAccessible(TRUE);

        $result = array();
        foreach($data as $r) {
            $result[$rp->getValue($r)] = (string) $r;
        }
        return $result;
    }


    /**
     * Return available fields
     *
     * @return array
     */
    public function getFields()
    {
        if (is_null($this->fields)) {
            $this->fields = array();

            $meta = $this->em->getClassMetadata($this->model);
            foreach($meta->getReflectionClass()->getProperties() as $prop) {
                $this->fields[] = $prop->getName();
            }
        }

        if (is_array($this->exclude)) {
            return array_diff($this->fields, $this->exclude);
        }
        return $this->fields;
    }


    /**
     * Check if validation is allowed for choosen level
     *
     * @return boolean
     */
    public function canValidate($level)
    {
        return $this->validation & $level;
    }


    /**
     * Create component for property
     *
     * @param array $property
     * @return boolean
     */
    protected function createFieldComponent($property)
    {
        $params = array(
            &$this,
            $property,
        );

        $fieldName = $property['fieldName'];

        # Add group (make fieldset)
        if (isset($property['newGroup']) && $property['newGroup']) {
            $this->addGroup($property['newGroup']);
        }

        # Custom creating component for field
        if (method_exists($this, 'setFieldComponent')) {
            $this->setFieldComponent($fieldName);

            if (isset($this[$fieldName])) {
                return TRUE;
            }
        }

		# Set label
		if (! isset($property['label'])) {
			$property['label'] = $property['fieldName'];
		}

        # Relation from second side is ignored
        if (($property['type'] == FOXY_ONE_TO_ONE
                ||$property['type'] == FOXY_ONE_TO_MANY
            )
            && (! isset($property['joinColumns'])
                || count($property['joinColumns']) == 0
            )) {
            unset($this->properties[$fieldName]);
            return FALSE;
        }

        $relations = array(
            FOXY_ONE_TO_ONE,
            FOXY_MANY_TO_ONE,
            FOXY_ONE_TO_MANY,
            FOXY_MANY_TO_MANY
        );

        # Relations have data for select-box as 5nd parameter
        if (in_array($property['type'], $relations)) {
            $params[] = $this->getSelectData(
                $property['targetEntity'],
                $fieldName
            );
        }

        # Create identifier as hidden field
        if (isset($property['identifier'])) {
            $this->addHidden($fieldName);
            return TRUE;
        }

        call_user_func_array(
            $this->componentsCallbackMap[$property['type']],
            $params
        );

        # If readOnly
        if (is_array($this->readOnly)
            && in_array($property['fieldName'], $this->readOnly)) {
            $this->removeComponent($this[$fieldName]);
            $this[$fieldName] = new \Foxy\Controls\Disabled(
                $this,
                $property
            );
        }
        return TRUE;
    }


    /**
     * Customize property metadata
     *
     * @param string $field
     * @param array & $properties
     */
    private function customizeMetadata($field, array & $properties)
    {
        $metadata       = $this->em->getClassMetadata($this->model);
        $fields         = $metadata->getFieldNames();
        $assocMappings  = $metadata->getAssociationMappings();

        $rp = $metadata->getReflectionClass()->getProperty($field);
        $rp->setAccessible(TRUE);

        # Field
        $key = array_search($field, $fields);
        if ($key !== FALSE) {
            $properties[$field] += $metadata->getFieldMapping($field);
            unset($fields[$key]);

        # Relation
        } elseif (array_key_exists($field, $assocMappings)) {
            $properties[$field] += $assocMappings[$field];
            $properties[$field]['nullable'] = TRUE;
            $properties[$field]['unique'] = FALSE;

            if (isset($properties[$field]['joinColumns'])) {
                foreach($properties[$field]['joinColumns'] as $col) {
                    if (! isset($col['nullable']) || $col['nullable'] == FALSE) {
                        $properties[$field]['nullable'] = FALSE;
                    }
                    if (isset($col['unique']) && $col['unique']) {
                        $properties[$field]['unique'] = TRUE;
                    }
                }
            }
        } else {
            unset($properties[$field]);
            return;
        }

        # Checks for custom widget
        foreach(self::$simpleReader->getPropertyAnnotations($rp) as $a) {
            if ($a instanceof \Foxy\Annotations\Widget) {
				$widgetArray = (array) $a;
				foreach($widgetArray as $key => $val) {
					if (is_null($val)) {
						unset($widgetArray[$key]);
					}
				}

				$properties[$field] = array_merge(
					$properties[$field],
					$widgetArray
 				);
            }
        }

        $defaultValue = $rp->getValue($this->instance);
        if (isset($assocMappings[$field]) &&
            in_array($properties[$field]['type'], array(FOXY_MANY_TO_ONE, FOXY_ONE_TO_ONE))  &&
            $defaultValue != null){
            $targetEntity = $assocMappings[$field]['targetEntity'];
            $identifier = $this->getIdentifier($targetEntity);
            $metadata = $this->em->getClassMetadata($targetEntity);
            $rp = $metadata->getReflectionClass()->getProperty($identifier);
            $rp->setAccessible(TRUE);
            $defaultValue = $rp->getValue($defaultValue);
        }
        $properties[$field]['defaultValue'] = $defaultValue;
    }


    /**
	 * Get completed properties
	 *
	 * @return array
	 */
    protected function getCompletedProperties()
    {
        $result         = array();
        $properties     = array();
        $identifier     = $this->getIdentifier($this->model);

        # Fieldsets
        if ($this->fieldsets) {
            foreach($this->fieldsets as $group => $groupFields) {
                $activeGroup = NULL;

                foreach($groupFields as $field) {
					$properties[$field] = array();

                    # Detect new group
                    if ($activeGroup != $group) {
                        $activeGroup = $group;
                        $properties[$field]['newGroup'] = $group;
                    }

                    $this->customizeMetadata($field, $properties);
                }
            }
        # Fields
        } else {
            foreach($this->getFields() as $field) {
				$properties[$field] = array();
                $this->customizeMetadata($field, $properties);
            }
        }

        if (array_key_exists($identifier, $properties)) {
            $properties[$identifier]['identifier'] = TRUE;
        }

        return $properties;
    }


    /**
	 * Get cleaned property value for form component
	 *
	 * @param array $property
	 * @param bool $asLabel
	 * @return mixed
	 */
    public function getFormValue($property, $asLabel = FALSE)
    {
        # Password value cannot be loaded
        if ($property['type'] == 'password') {
            return NULL;
        }

        $value = NULL;
        $getter = ucfirst($property['fieldName']);
        if (method_exists($this->instance, $getter)) {
            $value = $this->instance->$getter();
        } else {
            $rc = $this->em->getClassMetadata($this->model)->getReflectionClass();
            $rp = $rc->getProperty($property['fieldName']);
            $rp->setAccessible(TRUE);
            $value = $rp->getValue($this->instance);
        }

        if ($value instanceof \Doctrine\Common\Collections\ArrayCollection
            || $value instanceof \Doctrine\ORM\PersistentCollection) {

            $data = array();
            $meta = $this->em->getClassMetadata($property['targetEntity']);

            # as label
            if ($asLabel) {
                $data = '';
                foreach($value as $entity) {
                    $data .= (string) $entity . ', ';
                }
                $data = rtrim($data, ',');
            # to form
            } else {
                foreach($value as $entity) {
                    $rp = $meta->getReflectionClass()->getProperty(
                        $this->getIdentifier(get_class($entity))
                    );
                    $rp->setAccessible(TRUE);
                    $val = $rp->getValue($entity);

                    if (is_object($val)) {
                        $valmeta = $this->em->getClassMetadata(get_class($val));
                        $valrp = $valmeta->getReflectionClass()->getProperty(
                            $this->getIdentifier(get_class($val))
                        );
                        $valrp->setAccessible(TRUE);
                        $val = $valrp->getValue($val);
                    }
                    $data[] = $val;
                }
            }

            return $data;
        }

        if( $value instanceof \Datetime) {
            if ($property['type'] == 'datetime') {
                return $value->format($this->datetimeFormat);
            }
            if ($property['type'] == 'date') {
                return $value->format($this->dateFormat);
            }
            if ($property['type'] == 'time') {
                return $value->format($this->timeFormat);
            }
        }

        if (is_object($value)) {
            # as label
            if ($asLabel) {
                return (string) $value;
            }

            # to form
            $meta = $this->em->getClassMetadata($property['targetEntity']);
            $rp = $meta->getReflectionClass()->getProperty(
                $this->getIdentifier($property['targetEntity'])
            );
            $rp->setAccessible(TRUE);
            return $rp->getValue($value);
        }
        return $value;
    }


    /**
	 * Set POST value to property
	 *
	 * @param string $field
	 * @param bool $asLabel
	 * @return mixed
	 */
    public function setPropertyValue($field, $value)
    {
        if (! array_key_exists($field, $this->properties)) {
            return;
        }

        $property = $this->properties[$field];
        $setter = 'set'.ucfirst($field);
        $getter = 'get'.ucfirst($field);
        $inverseProp = NULL;
        $inverseGetter = NULL;
        $meta = $this->em->getClassMetadata($this->model);
        $rp = $meta->getReflectionClass()->getProperty($property['fieldName']);
        $rp->setAccessible(TRUE);

        # Rewrite $value on One to n
        if ($property['type'] == FOXY_ONE_TO_ONE
            || $property['type'] == FOXY_MANY_TO_ONE
        ) {
            if (! is_null($value)) {
                $value = $this->em->find($property['targetEntity'], $value);
            }
        }

        elseif ($property['type'] == FOXY_MANY_TO_MANY) {
            $arrayCol = NULL;
            if (method_exists($this->instance, $getter)) {
                $arrayCol = $this->instance->$getter();
            } else {
                $arrayCol = $rp->getValue($this->instance);
            }
            $arrayCol->clear();

            $inverseProp = isset($property['inversedBy'])
                ? $property['inversedBy']
                    : $property['mappedBy'];
            $inverseGetter = 'get'.ucfirst($inverseProp);

            # Add entities to relation
            foreach($value as $id) {
                if (is_null($id)) {
                    continue;
                }
                $entity = $this->em->find($property['targetEntity'], $id);
                $arrayCol->add($entity);

                # Add inverse entities on many to many
                if (method_exists($entity,$inverseGetter)) {
                    $entity->$inverseGetter()->add($this->instance);
                    $this->em->persist($entity);
                } else {
					$rpr = new \ReflectionProperty(get_class($entity), $inverseProp);
                    $rpr->setAccessible(TRUE);
                    $rpr->getValue($entity)->add($this->instance);
                    $this->em->persist($entity);
                }
                $this->em->persist($this->instance);
            }

            $value = $arrayCol;
        }

		# Prevent exception: Invalid text representation: 7 ERROR: invalid input syntax for type double precision: ""
		elseif (in_array($property['type'], array('float','decimal'))
				&& is_string($value)
				&& strlen($value) == 0) {
			$value = NULL;
		}

        elseif (in_array($property['type'], array('datetime','date','time'))) {
            $value = new \Datetime($value);
        }

        if (method_exists($this->instance,$setter)) {
            $this->instance->$setter($value);
        } else {
            $rp->setValue($this->instance, $value);
        }
    }


    /**
	 * Set entity instance and load values to form
	 *
	 * @param object $entity
	 * @param array|NULL $excluded
	 * @return self
	 */
    public function setInstance($entity)
    {
        $this->instance = $entity;

        $args = func_get_args();
        $excluded = array();
        if (isset($args[1]) && is_array($args[1])) {
            $excluded = $args[1];
        }

        foreach($this->properties as $property) {
            if (in_array($property['fieldName'], $excluded)) {
                continue;
            }
            $asLabel = FALSE;

            if ($this[$property['fieldName']] instanceof \Foxy\Controls\Disabled) {
                $asLabel = TRUE;
            }
            $this[$property['fieldName']]->setDefaultValue(
                $this->getFormValue($property, $asLabel)
            );

			# TODO validace pro upload components
        }
        return $this;
    }


    /**
	 * Check is unique value is already unique
	 *
	 * @param array $field
	 * @param mixed $newValue
	 * @return bool
	 */
    private function uniqueCheck($field, $newValue)
    {
        if (! array_key_exists($field, $this->properties)) {
            return TRUE;
        }

        $property = $this->properties[$field];
        $entity = NULL;

        if ($property['unique']) {
            if (in_array($property['type'], array(FOXY_ONE_TO_ONE, FOXY_MANY_TO_ONE))
                && isset($property['joinColumns']))
            {
                $entity = $this->em->getRepository($this->model)->findOneBy(
                    array($property['targetEntity'] => $newValue)
                );
            } else {
                if (in_array($property['type'], array('datetime', 'date', 'time'))) {
                    $newValue = new \Datetime($newValue);
                }

                $entity = $this->em->getRepository($this->model)->findOneBy(
                    array($field => $newValue)
                );
            }
            if ($entity) {
                if ($entity === $this->instance) {
                    return TRUE;
                }
                return FALSE;
            }
        }
        return TRUE;
    }


    /**
	 * Returns validation message for level
	 *
	 * @param array $field
	 * @param int $level
	 * @return string
	 */
    public function getValidationMessage($field, $level)
    {
        $msg = NULL;
        if (method_exists($this,'getErrorMessage')) {
            $msg = $this->getErrorMessage($field, $level);
        }

        if (is_null($msg)) {
            return $this->validationMessages[$level];
        }

        return $msg;
    }


    /**
	 * Save model
	 *
	 * @param Foxy\Forms\Form $form
	 * @param $commit
	 */
    public function saveModel($form, $commit = TRUE)
    {
        $values = $form->getValues();
        $this->status = 'Insert';

        # Is update
        $identifier = $this->getIdentifier($this->model);
        if (isset($this[$identifier])
            && isset($values[$identifier])
            && $values[$identifier]) {

            if (! is_null($values[$identifier])) {
                $this->instance = $this->em->find($this->model, $values[$identifier]);
            }
            $this->status = 'Update';
        } elseif(\Doctrine\ORM\UnitOfWork::STATE_MANAGED
                    === $this->em->getUnitOfWork()->getEntityState($this->instance)) {
            $this->status = 'Update';
        }


        foreach($values as $name => $val) {

			# If not custom value
			if (! isset($this->properties[$name])) {
				continue;
			}

            # Prevent rewriting password of NULL value
            if ($this->properties[$name]['type'] == 'password'
                && strlen($val) == 0) {
                continue;
            }

            # Upload file and rewrite $val value
            if ($val instanceof \Nette\Http\FileUpload) {
                if ($val->getSize() == 0) {
                    continue;
                }

                $dest = NULL;
                if (method_exists($this, 'getUploadTo')) {
                    $dest = $this->getUploadTo($name);
                }
                if ($dest === FALSE
					|| (
						isset($this->properties[$name]['customUpload'])
						&& $this->properties[$name]['customUpload'] == TRUE
					)
				) {
                    continue; # no upload automatically
                }
                if (is_null($dest)) {
                    $dest = $this->uploadTo;
                }

                $val = $this->mediaControler->saveFile($val, $dest);
            }

            # Unique check
            if ($this->canValidate(FOXY_UNIQUE) && ! $this->uniqueCheck($name, $val)) {
                $this->addError(
                    $this->getValidationMessage($name,FOXY_UNIQUE)
                );
                return;
            }

            $this->setPropertyValue($name, $val);
        }

        $this->em->persist($this->instance);

        if ($commit) {
            try {
                $this->em->flush();
                $this->flashMessage();
            } catch (\Exception $e) {
                $this->flashMessage('error');
            }

            $urlParams = array();
            if (method_exists($this, 'getUrlParams')) {
                $urlParams = $this->getUrlParams();
            }

            if ($this->successUrl) {
                $this->presenter->redirect($this->successUrl, $urlParams);
            }
        }
    }


    /**
	 * Evokes flash message
	 *
	 * @param string $status
	 */
    public function flashMessage($status = 'success')
    {
        $status = $status . $this->status;
        $this->presenter->flashMessage($this->{$status}, $status);
    }


	/**
	 * Get context for rendering
	 *
	 * @return \Foxy\RenderContext
	 */
	public function getRenderContext()
	{
		if (is_null($this->renderContext)) {
			$this->renderContext = new \Foxy\RenderContext();
			$this->renderContext->uploadWrapper = $this->uploadWrapper;
			$this->renderContext->uploadSeparator = $this->uploadSeparator;
			$this->renderContext->datetimeFormat = $this->datetimeFormat;
			$this->renderContext->dateFormat = $this->dateFormat;
			$this->renderContext->timeFormat = $this->timeFormat;
			$this->renderContext->mediaControler = $this->mediaControler;
		}
		return $this->renderContext;
	}
}
