<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace FoxyTests\Forms;

use Doctrine\ORM\Tools\SchemaTool;
use Nette;
use Nette\Application\UI;
use Nette\PhpGenerator as Code;
use Tester;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class ATestCase extends Tester\TestCase
{

	/**
	 * @var \Nette\DI\Container|\SystemContainer
	 */
	protected $serviceLocator;

	/**
	 * @return Kdyby\Doctrine\EntityManager
	 */
	protected function createMemoryManager()
	{
		$rootDir = __DIR__ . '/../../';

		$config = new Nette\Configurator();
		$container = $config->setTempDirectory(TEMP_DIR)
			->addConfig(__DIR__ . '/../nette-reset.neon')
			->addParameters(array(
				'appDir' => $rootDir,
				'wwwDir' => $rootDir,
			))
			->createContainer();
		/** @var Nette\DI\Container $container */

		$em = $container->getByType('Doctrine\ORM\EntityManager');

		/** @var Doctrine\ORM\EntityManager $em */

		$schemaTool = new SchemaTool($em);
		$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

		$this->serviceLocator = $container;

		return $em;
	}



	/**
	 * @param string $className
	 * @param array $props
	 * @return object
	 */
	protected function newInstance($className, $props = array())
	{
		$instance = new $className();
		foreach($props as $property => $value){
			$rp = new \ReflectionProperty($className, $property);
			$rp->setAccessible(TRUE);
			$rp->setValue($instance, $value);
		}

		return $instance;
	}



	/**
	 * @param UI\Form $form
	 * @param array $data
	 * @return PresenterMock
	 */
	protected function attachToPresenter(UI\Form $form, $data = array())
	{
		$presenter = new PresenterMock();
		$this->serviceLocator->callMethod(array($presenter, 'injectPrimary'));

		if (!empty($data)) {
			$request = new Nette\Application\Request('fake', 'POST', array('do' => 'save-model'), array('do' => 'save-model') + $data);

		} else {
			$request = new Nette\Application\Request('fake', 'POST', array());
		}

		$presenter->run($request);
		$presenter['form'] = $form;

		return $presenter;
	}


	protected function getInitData($entity)
	{
		return array(
			'model' => $entity,
			'instance' => new $entity()
		);
	}
}



class PresenterMock extends UI\Presenter
{

	protected function startup()
	{
		$this->terminate();
	}

}
