<?php

# @package nette-foxy-forms
#
# Generate nette form component using Doctrine entity annotations
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
define('FOXY_VALIDATE_ALL', 126);


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
    protected $instance;

    # @string
    # Redirect destination after save model action
    protected $successUrl = '';

    # @array
    protected $validationMessages = array(
        FOXY_NULLABLE   => 'Item is required',
        FOXY_IS_INT     => 'Has to be an integer',
        FOXY_IS_FLOAT   => 'has to be a float',
        FOXY_MAX_LENGTH => 'Text is too long',
        FOXY_UNIQUE     => 'Entered value is already used',
    );

    # @array
    protected $componentsCallbackMap = array(
        'integer'   => 'Foxy\FormComponents::createInteger',
        'bigint'    => 'Foxy\FormComponents::createBigInteger',
        'smallint'  => 'Foxy\FormComponents::createSmallInteger',
        'string'    => 'Foxy\FormComponents::createString',
        'text'      => 'Foxy\FormComponents::createText',
        'decimal'   => 'Foxy\FormComponents::createDecimal',
        'boolean'   => 'Foxy\FormComponents::createBoolean',
        'datetime'  => 'Foxy\FormComponents::createDatetime',
        'date'      => 'Foxy\FormComponents::createDate',
        'time'      => 'Foxy\FormComponents::createTime',
        2           => 'Foxy\FormComponents::createSelectBox',             # One to many
        4           => 'Foxy\FormComponents::createMultipleSelectBox',     # Many to one
        1           => 'Foxy\FormComponents::createMultipleSelectBox',     # Many to many
    );

    # @string
    protected $formNameSuffix = 'Form';


    # Construct Foxy\Form
    #
    # @param \Doctrine\ORM\EntityManager $em
    # @param Nette\ComponentModel\IContainer $parent
    # @param string $name
    public function __construct(\Doctrine\ORM\EntityManager $em,
                                \Nette\ComponentModel\IContainer $parent = NULL,
                                $name = NULL)
    {
        parent::__construct();
        $this->em = $em;

        # Instance of new model for getting form's default values
        $this->instance = new $this->model;
        $this->onSuccess[] = array($this, 'saveModel');

        foreach($this->getCompletedProperties() as $property) {
            $this->createFieldComponent($property);
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

    # Appends component to form
    #
    # @param \Nette\Application\UI\Form $form
    # @param array $property
    protected function createFieldComponent($property)
    {
        $validationSettings = array(
            FOXY_NULLABLE => TRUE,
            FOXY_IS_INT => FALSE,
            FOXY_IS_FLOAT => FALSE,
            FOXY_MAX_LENGTH => NULL,
            FOXY_HTML5_SUPPORT => FALSE,
        );

        if ($this->validation & FOXY_NULLABLE
            && isset($property['nullable'])
            && $property['nullable'] === FALSE) {
            $validationSettings[FOXY_NULLABLE] = FALSE;
        }

        if ($this->validation & FOXY_IS_INT) {
            $validationSettings[FOXY_IS_INT] = TRUE;
        }

        if ($this->validation & FOXY_IS_FLOAT) {
            $validationSettings[FOXY_IS_FLOAT] = TRUE;
        }

        if ($this->validation & FOXY_HTML5_SUPPORT) {
            $validationSettings[FOXY_HTML5_SUPPORT] = TRUE;
        }

        if ($this->validation & FOXY_MAX_LENGTH
            && in_array($property['type'], array('string','text'))
            ) {
            $validationSettings[FOXY_MAX_LENGTH] = $property['length'];
        }

        if ($this->validation & FOXY_HTML5_SUPPORT) {
            $validationSettings[FOXY_HTML5_SUPPORT] = TRUE;
        }

        $params = array(
            &$this,
            $property,
            $validationSettings
        );

        # Relations have data for select-box as 5nd parameter
        if (in_array($property['type'], array(1,2,4))) {
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
        if (method_exists($this, 'getFieldComponent')) {
            $this->getFieldComponent($property['fieldName']);

            if (isset($this[$property['fieldName']])) {
                return;
            }
        }

        call_user_func_array(
            $this->componentsCallbackMap[$property['type']],
            $params
        );
    }

    # Get validation level for component builder
    #
    # @return int
    public function getValidationLevel()
    {
        return $this->validation;
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
            unset($fields[$key]);
        # Relation
        } elseif (array_key_exists($field, $assocMappings)) {
            $properties[$field] = $assocMappings[$field];
        } else {
            return;
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

        return $properties;
    }

    # Set entity instance and load values to form
    #
    # @param object $entity
    # @return self
    public function setInstance($entity)
    {
        $this->instance = $entity;
        foreach($this->getCompletedProperties() as $property) {
            $this[$property['fieldName']]->setValue(
                $property['defaultValue']
            );
        }
        return $this;
    }

    # Check is unique value is already unique
    #
    # @param array $property
    # @param mixed $newValue
    # @return bool
    private function uniqueCheck($property, $newValue)
    {
        $entity = FALSE;
        if (isset($property['unique'])) {
            if($property['unique']) {
                $entity = $this->em->getRepository($this->model)->findOneBy(
                    array($property['fieldName'] => $newValue)
                );
            }
        }
        elseif(isset($property['joinColumns']['unique'])) {
            if($property['joinColumns']['unique']) {
                $entity = $this->em->getRepository($property['targetEntity'])->findOneBy(
                    array($property['joinColumns']['name'] => $newValue)
                );
            }
        }
        if ($entity) {
            return FALSE;
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
        return $this->validationMessages[$level];
    }

    # Save model
    #
    # @param Foxy\Form $form
    # @param $commit
    public function saveModel($form, $commit = TRUE)
    {
        $values = $form->getValues();

        # Is update
        $identifier = $this->getIdentifier($this->model);
        if (isset($this[$identifier])
            && isset($values[$identifier])
            && $values[$identifier]) {

            $this->instance = $this->em->find($this->model, $values[$identifier]);
        }

        $metadata       = $this->em->getClassMetadata($this->model);
        $fields         = $metadata->getFieldNames();
        $assocMappings  = $metadata->getAssociationMappings();

        foreach($values as $name => $val) {
            $setter = 'set'.ucfirst($name);
            $getter = 'get'.ucfirst($name);

            # Field
            $key = array_search($name, $fields);
            if ($key !== FALSE
                || (array_key_exists($name, $assocMappings)
                    && $assocMappings[$name]['type'] == 2)
                ) {
                # Unique check
                if ($this->instance->$getter() != $val) {
                    if (! $this->uniqueCheck($metadata->getFieldMapping($name), $val)) {
                        $this->addError(
                            $this->getValidationMessage($name,FOXY_UNIQUE)
                        );
                        return;
                    }
                }
                $this->instance->$setter($val);
                continue;
            }

            # Relations 1 and 4
            if (array_key_exists($name, $assocMappings)) {
                $field = $assocMappings[$name];

                foreach($this->instance->$getter() as $rel) {
                    $this->instance->$getter()->removeElement($rel);

                    # Many to many
                    if ($field['type'] === 1) {
                        # TODO vykosit z druhe strany a napersistovat
                    }
                }
                foreach($val as $id) {
                    $entity = $this->em->find($field['targetEntity'], $id);
                    $this->instance->$getter()->add($entity);
                    $this->em->persist($entity);
                }
            }
        }

        $this->em->persist($this->instance);

        if ($commit) {
            $this->em->flush();
        }

        if ($this->successUrl) {
            $this->presenter($this->successUrl);
        }
    }
}
