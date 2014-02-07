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


class StringValidationsTest extends ORMTestCase
{

	public function testRequired()
	{
		$init = array(
			'model' => 'CmsAddress',
			'validationMessages' => array(FOXY_NULLABLE => 'xxx'),
			'fields' => array('country'),
		);

		$form = $this->newInstance('Foxy\Forms\Form', $init);

		Tester\Assert::false($form->isValid());
		Tester\Assert::same(
			array('xxx'),
			$form['country']->getErrors()
		);
	}


/*
	public function testValidate_toOne()
	{
		$form = new UI\Form;
		$form->addText('topic');
		$userContainer = $form->addContainer('user');
		$userContainer->addText('username');

		$article = new CmsArticle();
		$article->user = new CmsUser();

		$this->mapper->validateContainer($form, $article);

		Tester\Assert::same(array(), $form->getErrors());

		Tester\Assert::same(array(
			'Tato hodnota nesmí být null.'
		), $form['topic']->getErrors());

		Tester\Assert::same(array(
			'Tato hodnota nesmí být prázdná.'
		), $userContainer['username']->getErrors());
	}
*/
}

\run(new StringValidationsTest());
