<?php

class CategoryForm extends Foxy\Forms\Form
{
	protected $model = 'Category';
	protected $successUrl = 'default';
	public $uploadWrapper = 'div';
	protected $enableCaching = FALSE;

	protected function attached($presenter)
	{
		parent::attached($presenter);
		$this->uploadSeparator = \Nette\Utils\Html::el('br');
	}
}
