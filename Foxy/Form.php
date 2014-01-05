<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>


namespace Foxy;


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


abstract class Form extends \Nette\Application\UI\Form {

    # @\Doctrine\ORM\EntityManager
    private $em;

    # @string
    # Model name
    protected $model;

    # @mixed
    # Creates these fields only if is set
    protected $fields;

    # @mixed
    # Remove these fields from form
    protected $exclude;

    # @mixed
    # Form fieldsets
    protected $fieldsets;

    # @int
    # Validation level
    protected $validation = FOXY_VALIDATE_ALL;

    # @object
    # Instance of model
    private $instance;

    # @string
    # Redirect destination after save model action
    protected $successUrl = '';

    # @string
    # Relative upload directory with date masks supporting
    protected $uploadTo = 'images/%Y-%m-%d/';

	# @string
	# Add submit button automatically if is not NULL
	protected $submitButton = 'send';

	# @string
	# Flash message post insert
	protected $successInsert = 'Model was created successfully';

	# @string
	# Flash message post update
	protected $successUpdate= 'Model was edited successfully';

	# @string
	# Flash message post error
	protected $errorMessage = 'Model was not saved';


    # @array
    protected $validationMessages = array(
        FOXY_NULLABLE   => 'Item is required',
        FOXY_IS_INT     => 'Has to be an integer',
        FOXY_IS_FLOAT   => 'has to be a float',
        FOXY_MAX_LENGTH => 'Text is too long',
        FOXY_UNIQUE     => 'Entered value is already used',
		FOXY_UPLOAD_TYPE=> 'Thubnail must be JPEG, PNG or GIF',
		FOXY_EMAIL		=> 'Email is not valid',
    );

    # @array
    protected $componentsCallbackMap = array(
        'integer'           => 'Foxy\FormComponents::createInteger',
        'bigint'            => 'Foxy\FormComponents::createBigInteger',
        'smallint'          => 'Foxy\FormComponents::createSmallInteger',
        'string'            => 'Foxy\FormComponents::createString',
        'text'              => 'Foxy\FormComponents::createText',
        'decimal'           => 'Foxy\FormComponents::createDecimal',
        'boolean'           => 'Foxy\FormComponents::createBoolean',
        'datetime'          => 'Foxy\FormComponents::createDatetime',
        'date'              => 'Foxy\FormComponents::createDate',
        'time'              => 'Foxy\FormComponents::createTime',
        FOXY_ONE_TO_ONE     => 'Foxy\FormComponents::createSelectBox',
        FOXY_MANY_TO_ONE    => 'Foxy\FormComponents::createSelectBox',
        FOXY_ONE_TO_MANY    => 'Foxy\FormComponents::createMultipleSelectBox',
        FOXY_MANY_TO_MANY   => 'Foxy\FormComponents::createMultipleSelectBox',
		# Additional widgets
		'upload'			=> 'Foxy\FormComponents::createUpload',
		'image'				=> 'Foxy\FormComponents::createImage',
		'password'			=> 'Foxy\FormComponents::createPassword',
		'email'				=> 'Foxy\FormComponents::createEmail',
    );

    # Properties with customized metadata
    # @array
    private $properties = array();


    # Construct Foxy\Form
    #
    # @param \Doctrine\ORM\EntityManager $em
    # @param Nette\ComponentModel\IContainer $parent
    # @param string $name
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        parent::__construct();
        $this->em = $em;

        if (is_object($this->model)) {
            $this->instance = $this->model;
        } else {
            $this->instance = new $this->model;
        }

        $this->onSuccess[] = array($this, 'saveModel');
    }

    # Creates form components after attached to presenter
    #
    # @param object
    protected function attached($presenter)
    {
        parent::attached($presenter);

        if ($presenter instanceof \Nette\Application\UI\Presenter) {
            $this->properties = $this->getCompletedProperties();

            foreach($this->properties as $property) {
                $this->createFieldComponent($property);
            }

			if ($this->submitButton) {
				$this->addSubmit($this->submitButton, $this->submitButton);
			}
        }
    }

    # Returns entity's identifier name
    #
    # @param string $entity
    # @return string
    protected function getIdentifier($entity)
    {
        return $this->em->getClassMetadata($entity)->getIdentifier()[0];
    }

    # Get related data for select box
    #
    # @param mixed $entity
    # @param string $fieldName
    # @return array
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

    # Return fields for create
    #
    # @return array
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

    # Check if validation is allowed for choosen level
    #
    # @return boolean
    public function canValidate($level)
    {
        return $this->validation & $level;
    }

    # Appends component to form
    #
    # @param \Nette\Application\UI\Form $form
    # @param array $property
    protected function createFieldComponent($property)
    {
        $params = array(
            &$this,
            $property,
        );

        # Relation from second side is ignored
        if (($property['type'] == FOXY_ONE_TO_ONE
				||$property['type'] == FOXY_ONE_TO_MANY
			)
            && (! isset($property['joinColumns'])
				|| count($property['joinColumns']) == 0
			)) {
			unset($this->properties[$property['fieldName']]);
            return;
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
                $property['fieldName']
            );
        }

        # Add group (make fieldset)
        if (isset($property['newGroup']) && $property['newGroup']) {
            $this->addGroup($property['newGroup']);
        }

        # Create identifier as hidden field
        if (isset($property['identifier'])) {
            $this->addHidden($property['fieldName']);
            return;
        }

        # Custom creating component for field
        if (method_exists($this, 'setFieldComponent')) {
            $this->setFieldComponent($property['fieldName']);

            if (isset($this[$property['fieldName']])) {
                return;
            }
        }

        call_user_func_array(
            $this->componentsCallbackMap[$property['widget']],
            $params
        );
    }

    # Customize property metadata
    #
    # @param string $field
    # @param array & $properties
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
            $properties[$field] = $metadata->getFieldMapping($field);
			$properties[$field]['widget'] = $properties[$field]['type'];
            unset($fields[$key]);

        # Relation
        } elseif (array_key_exists($field, $assocMappings)) {
            $properties[$field] = $assocMappings[$field];
			$properties[$field]['widget'] = $properties[$field]['type'];
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
            return;
        }

		# Checks for custom widget
		if (isset($properties[$field]['options'])
			&& array_key_exists('widget', $properties[$field]['options'])) {
			$properties[$field]['widget'] = $properties[$field]['options']['widget'];
		}

        $properties[$field]['defaultValue'] = $rp->getValue($this->instance);
    }

    # Get completed properties
    #
    # @return array
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
                $this->customizeMetadata($field, $properties);
            }
        }

        if (array_key_exists($identifier, $properties)) {
            $properties[$identifier]['identifier'] = TRUE;
        }

		#if ($this->instance instanceof \Product) {
		#	dump($properties);exit;
		#}
        return $properties;
    }

    # Get cleaned property value for form component
    #
    # @param @array $property
    # @return mixed
    public function getFormValue($property)
    {
		# Password value cannot be loaded
		if ($property['widget'] == 'password') {
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

            foreach($value as $entity) {
                $rp = $meta->getReflectionClass()->getProperty(
                    $this->getIdentifier(get_class($entity))
                );
                $rp->setAccessible(TRUE);
                $data[] = $rp->getValue($entity);
            }
            return $data;
        }

        if( $value instanceof \Datetime) {
            if ($property['type'] == 'datetime') {
                return $value->format('Y-m-d\TH:i:s');
            }
            if ($property['type'] == 'date') {
                return $value->format('Y-m-d');
            }
            if ($property['type'] == 'time') {
                return $value->format('H:i:s');
            }
        }

        if (is_object($value)) {
            $meta = $this->em->getClassMetadata($property['targetEntity']);
            $rp = $meta->getReflectionClass()->getProperty(
                $this->getIdentifier($property['targetEntity'])
            );
            $rp->setAccessible(TRUE);
            return $rp->getValue($value);
        }
        return $value;
    }

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
            $value = $this->em->find($property['targetEntity'], $value);
        }

        if ($property['type'] == FOXY_MANY_TO_MANY) {
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
                $entity = $this->em->find($property['targetEntity'], $id);
                $arrayCol->add($entity);

                # Add inverse entities on many to many
				if (method_exists($entity,$inverseGetter)) {
					$entity->$inverseGetter()->add($this->instance);
					$this->em->persist($entity);
				} else {
					$rpr = $meta->getReflectionClass()->getProperty($inverseProp);
					$rpr->setAccessible(TRUE);
					$rpr->getValue($entity)->add($this->instance);
					$this->em->persist($entity);
				}
				$this->em->persist($this->instance);
            }

			$value = $arrayCol;
        }

		if (in_array($property['type'], array('datetime','date','time'))) {
			$value = new \Datetime($value);
		}

        if (method_exists($this->instance,$setter)) {
            $this->instance->$setter($value);
        } else {
            $rp->setValue($this->instance, $value);
        }
    }

    # Set entity instance and load values to form
    #
    # @param object $entity
    # @return self
    public function setInstance($entity)
    {
        $this->instance = $entity;
        foreach($this->properties as $property) {
            $this[$property['fieldName']]->setDefaultValue(
                $this->getFormValue($property)
            );
        }
        return $this;
    }

    # Check is unique value is already unique
    #
    # @param array $field
    # @param mixed $newValue
    # @return bool
    private function uniqueCheck($field, $newValue)
    {
        if (! array_key_exists($field, $this->properties)) {
            return TRUE;
        }
        $property = $this->properties[$field];

        if ($property['unique']) {
            if (isset($property['joinColumns'])) {
                foreach($property['joinColumns'] as $col) {
                    if ($col['unique']) {
                        $entity = $this->em->getRepository($property['targetEntity'])->findOneBy(
                            array($col['name'] => $newValue)
                        );
                        if ($entity) {
                            return FALSE;
                        }
                    }
                }
            } else {
                $entity = $this->em->getRepository($this->model)->findOneBy(
                    array($property['fieldName'] => $newValue)
                );
                if ($entity) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    # Returns validation message for level
    # Prepared for overload of custom messages
    #
    # @param string $field
    # @param int $level
    # @return string
    public function getValidationMessage($field, $level)
    {
        $msg = NULL;
        if (method_exists($this,'getErrorMessage')) {
            $msg = $this->getMessage($field, $level);
        }

        if (is_null($msg)) {
            return $this->validationMessages[$level];
        }

        return $msg;
    }

    # Save model
    #
    # @param Foxy\Form $form
    # @param $commit
    public function saveModel($form, $commit = TRUE)
    {
        $values = $form->getValues();
        $mediaStorage = $this->presenter->context->getByType('Foxy\MediaStorage');
		$status = 'successInsert';

        # Is update
        $identifier = $this->getIdentifier($this->model);
        if (isset($this[$identifier])
            && isset($values[$identifier])
            && $values[$identifier]) {

            $this->instance = $this->em->find($this->model, $values[$identifier]);
			$status = 'successUpdate';
        }

        foreach($values as $name => $val) {

			# Prevent rewriting password of NULL value
			if ($this->properties[$name]['widget'] == 'password'
				&& strlen($val) == 0) {
				continue;
			}

            # Upload file and rewrite $val value
            if ($val instanceof \Nette\Http\FileUpload && $val->getSize() > 0) {
                $dest = NULL;
                if (method_exists($this, 'getUploadTo')) {
                    $dest = $this->getUploadTo($name);
                }
                if (is_null($dest)) {
                    $dest = $this->uploadTo;
                }
				$uploadedName = NULL;
				if (method_exists($this, 'getUploadedName')) {
					$uploadedName = $this->getUploadedName($name, $val);
				}
                if (is_null($uploadedName)) {
                    $uploadedName = $val->getName();
                }

				$dest .= $uploadedName;
                $mediaStorage->saveFile($val, $dest);
                $val = $dest;
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
				if ($this->{$status}) {
					$this->presenter->flashMessage($this->{$status});
				}
			} catch (\Exception $e) {
				if ($this->errorMessage) {
					$this->presenter->flashMessage($this->errorMessage, 'error');
				}
			}
        }

        if ($this->successUrl) {
            $this->presenter->redirect($this->successUrl);
        }
    }
}
