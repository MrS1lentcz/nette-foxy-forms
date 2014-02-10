<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */

namespace FoxyTests\Forms;

use Nette;
use Nette\Application\UI;
use Tester;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/models/cms.php';


class ValidationsTest extends ATestCase
{

	/** @var \Doctrine\ORM\EntityManager */
	protected $em;


	protected function setUp()
	{
		$this->em = $this->createMemoryManager();
	}


	public function testRequired()
	{
		$data = $this->getInitData('FoxyTests\Forms\CmsAddress');
		$data['fields'] = array('country');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array($form->getValidationMessage('country', FOXY_NULLABLE)),
			$form['country']->getErrors()
		);
	}


	public function testMaxLength()
	{
		$data = $this->getInitData('FoxyTests\Forms\CmsGroup');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		$model = $data['instance'];
		$model->name = \Nette\Utils\Strings::random($length=51);
		$form->setInstance($model);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array($form->getValidationMessage('country', FOXY_MAX_LENGTH)),
			$form['name']->getErrors()
		);
	}


	public function testInteger()
	{
		$data = $this->getInitData('FoxyTests\Forms\BonusPoint');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		$model = $data['instance'];
		$model->value = 'f123';
		$form->setInstance($model);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array($form->getValidationMessage('value', FOXY_IS_INT)),
			$form['value']->getErrors()
		);
	}


	public function testInteger2()
	{
		$data = $this->getInitData('FoxyTests\Forms\BonusPoint');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		$model = $data['instance'];
		$model->value = 12.3;
		$form->setInstance($model);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array($form->getValidationMessage('value', FOXY_IS_INT)),
			$form['value']->getErrors()
		);
	}


	public function testFloat()
	{
		$data = $this->getInitData('FoxyTests\Forms\BonusPoint');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		# can pass NULL
		$model = $data['instance'];
		$model->percent = '';
		$form->setInstance($model);
		Tester\Assert::true($form->isValid());
	}


	public function oneToManyRequired()
	{
		$data = $this->getInitData('FoxyTests\Forms\CmsComment');
		$form = $this->newInstance('FoxyTests\Forms\Form', $data);
		$this->attachToPresenter($form);

		$model = $data['instance'];
		$model->topic = \Nette\Utils\Strings::random($length=51);
		$model->text = \Nette\Utils\Strings::random($length=51);
		$form->setInstance($model);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array($form->getValidationMessage('article', FOXY_NULLABLE)),
			$form['article']->getErrors()
		);
	}


	public function uploadImage()
	{

	}


	public function uploadFile()
	{

	}
}

\run(new ValidationsTest());
