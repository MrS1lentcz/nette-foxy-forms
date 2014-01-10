<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>


namespace Foxy\Controls;


class Upload extends \Nette\Forms\Controls\UploadControl
{

    # @Foxy\MediaStorage
    protected $mediaStorage;

    # @string
    protected $infoValue;


    # Adds component to form
    #
    # @param mixed $form
    protected function attached($form)
    {
        parent::attached($form);
        if ($form instanceof \Nette\Forms\Form) {
            $this->mediaStorage
                = $form->presenter->context->getByType('Foxy\MediaStorage');
        }
    }


    # Set value for generating link
    #
    # @param mixed $value
    # @return self
    public function setValue($value)
    {
        $this->infoValue = $value;
        return $this;
    }


    # Get wrappered control
    #
    # @return \Nette\Utils\Html
    public function getControl()
    {
        if (! $this->form->uploadWrapper) {
            return parent::getControl();
        }

        $wrapper = $this->form->uploadWrapper;
        if (! $this->form->uploadWrapper instanceof \Nette\Utils\Html) {
            $wrapper = \Nette\Utils\Html::el($this->form->uploadWrapper);
        }

        $wrapper->add(parent::getControl());
        if ($this->form->uploadSeparator) {
            if ($this->form->uploadSeparator instanceof \Nette\Utils\Html) {
                $wrapper->add($this->form->uploadSeparator);
            } else {
                $wrapper->add(
                    \Nette\Utils\Html::el($this->form->uploadSeparator)
                );
            }
        }
        $wrapper->add($this->getLink());

        return $wrapper;
    }


    # Get info link
    #
    # @return \Nette\Utils\Html
    public function getLink()
    {
        return \Nette\Utils\Html::el('a')
            ->setText($this->infoValue)
            ->setHref($this->mediaStorage->getUrl($this->infoValue));
    }
}
