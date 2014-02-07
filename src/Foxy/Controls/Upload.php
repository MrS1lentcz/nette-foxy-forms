<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Controls;


class Upload extends \Nette\Forms\Controls\UploadControl
{
    /**
     * @var string
     */
    protected $infoValue;


    /**
     * Set value for generating link
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value)
    {
        $this->infoValue = $value;
        return $this;
    }


    /**
     * Get wrappered control
     *
     * @return \Nette\Utils\Html
     */
    public function getControl()
    {
        $context = $this->form->getRenderContext();

        if (! $context->uploadWrapper) {
            return parent::getControl();
        }

        $wrapper = $context->uploadWrapper;
        if (! $context->uploadWrapper instanceof \Nette\Utils\Html) {
            $wrapper = \Nette\Utils\Html::el($context->uploadWrapper);
        }

        $wrapper->add(parent::getControl());
        if ($context->uploadSeparator) {
            if ($context->uploadSeparator instanceof \Nette\Utils\Html) {
                $wrapper->add($context->uploadSeparator);
            } else {
                $wrapper->add(
                    \Nette\Utils\Html::el($context->uploadSeparator)
                );
            }
        }
        $wrapper->add($this->getLink());

        return $wrapper;
    }


    /**
     * Get info link
     *
     * @return \Nette\Utils\Html
     */
    public function getLink()
    {
        $context = $this->form->getRenderContext();
        return \Nette\Utils\Html::el('a')
            ->setText($this->infoValue)
            ->setHref($context->mediaControler->getUrl($this->infoValue));
    }
}
