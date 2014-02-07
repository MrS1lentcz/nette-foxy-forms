<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy;


class ControlsFactory
{
    /**
     * Creates integer component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createInteger(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_IS_INT)) {
            $form[$field]->addCondition($form::FILLED)
                ->addRule(
                    $form::INTEGER,
                    $form->getValidationMessage($field, FOXY_IS_INT)
                );
        }

        if ($form->canValidate(FOXY_HTML5_SUPPORT)) {
            $form[$property['fieldName']]->setType('number');
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates big integer component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createBigInteger(Forms\Form $form, array $property)
    {
        self::createInteger($form, $property, $validationSettings);
    }


    /**
     * Creates small integer component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createSmallInteger(Forms\Form $form, array $property)
    {
        self::createInteger($form, $property, $validationSettings);
    }


    /**
     * Creates string component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createString(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_MAX_LENGTH) && $property['length']) {
            $form[$field]->addCondition($form::FILLED)
                ->addRule(
                    $form::MAX_LENGTH,
                    $form->getValidationMessage($field, FOXY_MAX_LENGTH),
                    $property['length']
                );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates text component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createText(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addTextarea($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_MAX_LENGTH) && $property['length']) {
            $form[$field]->addCondition($form::FILLED)
                ->addRule(
                    $form::MAX_LENGTH,
                    $form->getValidationMessage($field, FOXY_MAX_LENGTH),
                    $property['length']
                );
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates decimal component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createDecimal(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_IS_FLOAT)) {
            $form[$field]->addCondition($form::FILLED)
                ->addRule(
                    $form::FLOAT,
                    $form->getValidationMessage($field, FOXY_IS_FLOAT)
                );
        }

        if ($form->canValidate(FOXY_HTML5_SUPPORT)) {
            $form[$field]->setType('number');
        }

        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates boolean component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createBoolean(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addCheckbox($field, $label);
        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates datetime component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createDatetime(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_HTML5_SUPPORT)) {
            $form[$field]->setType('datetime-local');
        }

        $context = $form->getRenderContext();
        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format($context->datetimeFormat)
            );
        }
    }


    /**
     * Creates date component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createDate(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        if ($form->canValidate(FOXY_HTML5_SUPPORT)) {
            $form[$field]->setType('date');
        }

        $context = $form->getRenderContext();
        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format($context->dateFormat)
            );
        }
    }


    /**
     * Creates time component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createTime(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addText($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        $context = $form->getRenderContext();
        if ($property['defaultValue'] instanceof \DateTime) {
            $form[$field]->setDefaultValue(
                $property['defaultValue']->format($context->timeFormat)
            );
        }
    }


    /**
     * Creates selectbox component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createSelectBox(Forms\Form $form,
                                         array $property,
                                         array $data)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addSelect($field, $label, $data);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        $form[$field]->setTranslator(NULL);
        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates mutiple selectbox component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createMultipleSelectBox(Forms\Form $form,
                                         array $property,
                                         array $data)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addMultiSelect($field, $label, $data);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }

        $form[$field]->setTranslator(NULL);
    }


    /**
     * Creates upload component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createUpload(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form[$field] = new \Foxy\Controls\Upload($field); # TODO $label

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }
        $form[$field]->setDefaultValue($property['defaultValue']);
    }


    /**
     * Creates upload component with image validation
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createImage(Forms\Form $form, array $property)
    {
        self::createUpload($form, $property);
        $field = $property['fieldName'];

        if ($form->canValidate(FOXY_UPLOAD_TYPE)) {
            $form[$field]->addCondition($form::FILLED)->addRule(
                $form::IMAGE,
                $form->getValidationMessage($field, FOXY_UPLOAD_TYPE)
            );
        }
    }


    /**
     * Creates password component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createPassword(Forms\Form $form, array $property)
    {
        $field = $property['fieldName'];
        $label = array_key_exists('label', $property) ? $property['label']: $field;
        $form->addPassword($field, $label);

        if ($form->canValidate(FOXY_NULLABLE) && ! $property['nullable']) {
            $form[$field]->setRequired(
                $form->getValidationMessage($field, FOXY_NULLABLE)
            );
        }
    }


    /**
     * Creates email component
     *
     * @param \Foxy\Forms\Form $form
     * @param array $property
     */
    public static function createEmail(Forms\Form $form, array $property)
    {
        self::createString($form, $property);
        $field = $property['fieldName'];

        if ($form->canValidate(FOXY_IS_INT)) {
            $form[$field]->addCondition($form::FILLED)
                ->addRule(
                    $form::EMAIL,
                    $form->getValidationMessage($field, FOXY_EMAIL)
                );
        }

        if ($form->canValidate(FOXY_HTML5_SUPPORT)) {
            $form[$field]->setType('email');
        }
    }
}
