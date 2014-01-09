<?php

class ProductForm extends Foxy\Form
{
	protected $model = 'Product';
	protected $successUrl = 'default';
	protected $readOnly = array('author');
}
