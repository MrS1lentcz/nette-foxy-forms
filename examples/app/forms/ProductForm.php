<?php

class ProductForm extends Foxy\Forms\Form
{
	protected $model = 'Product';
	protected $successUrl = 'default';
	protected $readOnly = array('author');
}
