<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>


namespace Foxy;


class DisabledComponent extends \Nette\Forms\Controls\BaseControl
{

    # @Foxy\MediaStorage
    protected $mediaStorage;

    # @array
    protected $property;


    # Construct Foxy\DisabledComponent
    #
    # @param Foxy\Form $form
    # @param @array $property
    public function __construct(Form $form, $property)
    {
        $label = ($form->getTranslator())
            ? $form->getTranslator()->translate($property['fieldName'])
                : $property['fieldName'];

        parent::__construct($label);

        $this->mediaStorage = $form->presenter->context->getByType('Foxy\MediaStorage');;
        $this->property = $property;
        $this->setDisabled();

        $this->_controlBuilder(
            $form->getFormValue($property, $asLabel = TRUE)
        );
    }


    # Builds custom control
    #
    # @param mixed $value
    protected function _controlBuilder($value)
    {
        if (in_array($this->property['widget'], array('upload', 'image'))) {
            $this->control = \Nette\Utils\Html::el('a')
                ->setText($value)
                ->setHref($this->mediaStorage->getUrl($value));
        } elseif($this->property['widget'] == 'email') {
            $this->control = \Nette\Utils\Html::el('a')
                ->setText($value)
                ->setHref('mailto:'.$value);
        } elseif (preg_match('|http://|', $value)) {
            $this->control = \Nette\Utils\Html::el('a')
                ->setText($value)
                ->setHref($value)
                ->setTarget('_blank');
        } else {
            $this->control = \Nette\Utils\Html::el('span')->setText((string) $value);
        }
    }


    # Sets control's value
    #
    # @param mixed $value
    # @return self
    public function setValue($value)
    {
        if ($this->parent) {
            $this->_controlBuilder($value);
        }
        return $this;
    }
}
