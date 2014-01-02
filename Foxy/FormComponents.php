<?php

# @package nette-foxy-forms
#
# Generate nette forms using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>

namespace Foxy;


class FormComponents
{
    # Creates integer component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createInteger(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_IS_INT]) {
            $form[$field]->addRule(
                $form::INTEGER,
                $form->getValidationMessage($field, FOXY_IS_INT)
            );
        }

        if ($validationSettings[FOXY_HTML5_SUPPORT]) {
            $form[$property['fieldName']]->setType('number');
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates big integer component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    # @param array $validationMessages
    public static function createBigInteger(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        self::createInteger($form, $property, $validationSettings);
    }

    # Creates small integer component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createSmallInteger(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        self::createInteger($form, $property, $validationSettings);
    }

    # Creates string component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createString(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_MAX_LENGTH]) {
            $form[$field]->addRule(
                $form::MAX_LENGTH,
                $form->getValidationMessage($field, FOXY_MAX_LENGTH),
                $property['length']
            );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates text component
    #
    # @param \Form\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createText(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addTextarea($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_MAX_LENGTH]) {
            $form[$field]->addRule(
                $form::MAX_LENGTH,
                $form->getValidationMessage($field, FOXY_MAX_LENGTH),
                $property['length']
            );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates decimal component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createDecimal(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_IS_FLOAT]) {
            $form[$field]->addRule(
                $form::FLOAT,
                $form->getValidationMessage($field, FOXY_IS_FLOAT)
            );
        }

        if ($validationSettings[FOXY_HTML5_SUPPORT]) {
            $form[$field]->setType('number');
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates boolean component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createBoolean(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addCheckbox($field, $field);
        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates datetime component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createDatetime(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_HTML5_SUPPORT]) {
            $form[$field]->setType('datetime-local');
        }

        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format('Y-m-d\TH:i:s')
            );
        }
    }

    # Creates date component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    public static function createDate(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($validationSettings[FOXY_HTML5_SUPPORT]) {
            $form[$field]->setType('date');
        }

        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format('Y-m-d')
            );
        }
    }

    # Creates time component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    # @param array $validationMessages
    public static function createTime(Form & $form,
                                         array $property,
                                         array $validationSettings)
    {
        $field = $property['fieldName'];
        $form->addText($field, $field);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format('H:i:s')
            );
        }
    }

    # Creates selectbox component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    # @param array $data
    public static function createSelectBox(Form & $form,
                                         array $property,
                                         array $validationSettings,
                                         array $data)
    {
        $field = $property['fieldName'];
        $form->addSelect($field, $field, $data);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }

    # Creates mutiple selectbox component
    #
    # @param \Foxy\Form & $form
    # @param array $property
    # @param array $validationSettings
    # @param array $data
    public static function createMultipleSelectBox(Form & $form,
                                         array $property,
                                         array $validationSettings,
                                         array $data)
    {
        $field = $property['fieldName'];
        $form->addMultiSelect($field, $field, $data);

        if ($validationSettings[FOXY_NULLABLE] === FALSE) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }
}
