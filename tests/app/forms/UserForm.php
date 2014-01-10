<?php

class UserForm extends Foxy\Forms\Form
{
	protected $model = 'User';
	protected $successUrl = 'default';
	protected $readOnly = array('createdDatetime');
}
