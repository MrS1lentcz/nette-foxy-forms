<?php

class UserForm extends Foxy\Form
{
	protected $model = 'User';
	protected $successUrl = 'default';
	protected $readOnly = array('createdDatetime');
}
