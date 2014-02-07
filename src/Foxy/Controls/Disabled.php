<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Controls;


class Disabled extends \Nette\Forms\Controls\BaseControl
{

    /**
     * @var string
     */
    protected $infoValue;

    /**
     * @var array
     */
    protected $property;

    /**
     * @var bool
     */
    protected $replaced = FALSE;


    /**
     * Construct Foxy\Controls\Disabled
     *
     * @param Foxy\Forms\Form $form
     * @param @array $property
     */
    public function __construct(\Foxy\Forms\Form $form, $property)
    {
        parent::__construct($property['fieldName']);
        $this->property = $property;
        $this->setDisabled();
    }


    /**
     * Builds custom control
     *
     * @param mixed $value
     */
    public function getControl()
    {
        if ($this->replaced === FALSE)
        {
            parent::getControl();
            $context = $this->form->getRenderContext();
            $value = (string) $this->infoValue;

            if (in_array($this->property['type'], array('upload', 'image'))) {
                $this->control = \Nette\Utils\Html::el('a')
                    ->setText($value)
                    ->setHref($context->mediaControler->getUrl($value));
            } elseif($this->property['type'] == 'email') {
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
            $this->replaced = TRUE;
        }
        return $this->control;
    }


    /**
     * Sets control's value
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value)
    {
        if ($this->parent) {
            $this->infoValue = $value;
        }
        return $this;
    }
}
