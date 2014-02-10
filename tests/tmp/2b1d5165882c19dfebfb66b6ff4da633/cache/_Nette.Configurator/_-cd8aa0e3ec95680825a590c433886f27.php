<?php //netteCache[01]000253a:2:{s:4:"time";s:21:"0.99368500 1392065147";s:9:"callbacks";a:1:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:83:"/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/nette-reset.neon";i:2;i:1392064821;}}}?><?php
// source: /home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../nette-reset.neon 

/**
 * @property Nette\Application\Application $application
 * @property Nette\Caching\Storages\MemoryStorage $cacheStorage
 * @property Nette\DI\Container $container
 * @property Doctrine\Common\Cache\ArrayCache $doctrineCache
 * @property Doctrine\ORM\Configuration $doctrineConfiguration
 * @property Doctrine\ORM\EntityManager $em
 * @property Nette\Http\Request $httpRequest
 * @property Nette\Http\Response $httpResponse
 * @property Nette\DI\Extensions\NetteAccessor $nette
 * @property Nette\Application\Routers\RouteList $router
 * @property Nette\Http\Session $session
 * @property Nette\Security\User $user
 */
class SystemContainer extends Nette\DI\Container
{

	protected $meta = array(
		'types' => array(
			'nette\\object' => array(
				'nette',
				'nette.cacheJournal',
				'cacheStorage',
				'nette.httpRequestFactory',
				'httpRequest',
				'httpResponse',
				'nette.httpContext',
				'session',
				'nette.userStorage',
				'user',
				'application',
				'nette.presenterFactory',
				'router',
				'nette.mailer',
				'container',
			),
			'nette\\di\\extensions\\netteaccessor' => array('nette'),
			'nette\\caching\\storages\\ijournal' => array('nette.cacheJournal'),
			'nette\\caching\\storages\\filejournal' => array('nette.cacheJournal'),
			'nette\\caching\\istorage' => array('cacheStorage'),
			'nette\\caching\\storages\\memorystorage' => array('cacheStorage'),
			'nette\\http\\requestfactory' => array('nette.httpRequestFactory'),
			'nette\\http\\irequest' => array('httpRequest'),
			'nette\\http\\request' => array('httpRequest'),
			'nette\\http\\iresponse' => array('httpResponse'),
			'nette\\http\\response' => array('httpResponse'),
			'nette\\http\\context' => array('nette.httpContext'),
			'nette\\http\\session' => array('session'),
			'nette\\security\\iuserstorage' => array('nette.userStorage'),
			'nette\\http\\userstorage' => array('nette.userStorage'),
			'nette\\security\\user' => array('user'),
			'nette\\application\\application' => array('application'),
			'nette\\application\\ipresenterfactory' => array('nette.presenterFactory'),
			'nette\\application\\presenterfactory' => array('nette.presenterFactory'),
			'nette\\arraylist' => array('router'),
			'traversable' => array('router'),
			'iteratoraggregate' => array('router'),
			'countable' => array('router'),
			'arrayaccess' => array('router'),
			'nette\\application\\irouter' => array('router'),
			'nette\\application\\routers\\routelist' => array('router'),
			'nette\\mail\\imailer' => array('nette.mailer'),
			'nette\\mail\\sendmailmailer' => array('nette.mailer'),
			'foxy\\media\\istorage' => array('netteFoxyForms.mediaStorage'),
			'foxy\\media\\storage' => array('netteFoxyForms.mediaStorage'),
			'foxy\\media\\controler' => array('netteFoxyForms.mediaControler'),
			'doctrine\\orm\\entitymanagerinterface' => array('em'),
			'doctrine\\common\\persistence\\objectmanager' => array('em'),
			'doctrine\\orm\\entitymanager' => array('em'),
			'doctrine\\dbal\\configuration' => array('doctrineConfiguration'),
			'doctrine\\orm\\configuration' => array('doctrineConfiguration'),
			'doctrine\\common\\cache\\cacheprovider' => array('doctrineCache'),
			'doctrine\\common\\cache\\cache' => array('doctrineCache'),
			'doctrine\\common\\cache\\arraycache' => array('doctrineCache'),
			'nette\\di\\container' => array('container'),
		),
	);


	public function __construct()
	{
		parent::__construct(array(
			'appDir' => '/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../',
			'wwwDir' => '/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../',
			'debugMode' => FALSE,
			'productionMode' => TRUE,
			'environment' => 'production',
			'consoleMode' => FALSE,
			'container' => array(
				'class' => 'SystemContainer',
				'parent' => 'Nette\\DI\\Container',
				'accessors' => TRUE,
			),
			'tempDir' => '/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/../tmp/2b1d5165882c19dfebfb66b6ff4da633',
		));
	}


	/**
	 * @return Nette\Application\Application
	 */
	public function createServiceApplication()
	{
		$service = new Nette\Application\Application($this->getService('nette.presenterFactory'), $this->getService('router'), $this->getService('httpRequest'), $this->getService('httpResponse'));
		$service->catchExceptions = TRUE;
		$service->errorPresenter = 'Nette:Error';
		Nette\Application\Diagnostics\RoutingPanel::initializePanel($service);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\MemoryStorage
	 */
	public function createServiceCacheStorage()
	{
		$service = new Nette\Caching\Storages\MemoryStorage;
		return $service;
	}


	/**
	 * @return Nette\DI\Container
	 */
	public function createServiceContainer()
	{
		return $this;
	}


	/**
	 * @return Doctrine\Common\Cache\ArrayCache
	 */
	public function createServiceDoctrineCache()
	{
		$service = new Doctrine\Common\Cache\ArrayCache;
		return $service;
	}


	/**
	 * @return Doctrine\ORM\Configuration
	 */
	public function createServiceDoctrineConfiguration()
	{
		$service = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(
			'/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../FoxyTests/Forms/models/',
		), TRUE, NULL, $this->getService('doctrineCache'));
		if (!$service instanceof Doctrine\ORM\Configuration) {
			throw new Nette\UnexpectedValueException('Unable to create service \'doctrineConfiguration\', value returned by factory is not Doctrine\\ORM\\Configuration type.');
		}
		$service->setProxyDir('/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../FoxyTests/Forms/models');
		$service->setQueryCacheImpl($this->getService('doctrineCache'));
		$service->setMetadataCacheImpl($this->getService('doctrineCache'));
		return $service;
	}


	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function createServiceEm()
	{
		$service = \Doctrine\ORM\EntityManager::create(array(
			'dbname' => 'tests',
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		), $this->getService('doctrineConfiguration'));
		if (!$service instanceof Doctrine\ORM\EntityManager) {
			throw new Nette\UnexpectedValueException('Unable to create service \'em\', value returned by factory is not Doctrine\\ORM\\EntityManager type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Request
	 */
	public function createServiceHttpRequest()
	{
		$service = $this->getService('nette.httpRequestFactory')->createHttpRequest();
		if (!$service instanceof Nette\Http\Request) {
			throw new Nette\UnexpectedValueException('Unable to create service \'httpRequest\', value returned by factory is not Nette\\Http\\Request type.');
		}
		return $service;
	}


	/**
	 * @return Nette\Http\Response
	 */
	public function createServiceHttpResponse()
	{
		$service = new Nette\Http\Response;
		return $service;
	}


	/**
	 * @return Nette\DI\Extensions\NetteAccessor
	 */
	public function createServiceNette()
	{
		$service = new Nette\DI\Extensions\NetteAccessor($this);
		return $service;
	}


	/**
	 * @return Nette\Forms\Form
	 */
	public function createServiceNette__basicForm()
	{
		$service = new Nette\Forms\Form;
		trigger_error('Service nette.basicForm is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Caching\Cache
	 */
	public function createServiceNette__cache($namespace = NULL)
	{
		$service = new Nette\Caching\Cache($this->getService('cacheStorage'), $namespace);
		trigger_error('Service cache is deprecated.', 16384);
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\FileJournal
	 */
	public function createServiceNette__cacheJournal()
	{
		$service = new Nette\Caching\Storages\FileJournal('/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/../tmp/2b1d5165882c19dfebfb66b6ff4da633');
		return $service;
	}


	/**
	 * @return Nette\Http\Context
	 */
	public function createServiceNette__httpContext()
	{
		$service = new Nette\Http\Context($this->getService('httpRequest'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Nette\Http\RequestFactory
	 */
	public function createServiceNette__httpRequestFactory()
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy(array());
		return $service;
	}


	/**
	 * @return Nette\Latte\Engine
	 */
	public function createServiceNette__latte()
	{
		$service = new Nette\Latte\Engine;
		Foxy\Macros\Media::install($service->getCompiler());
		return $service;
	}


	/**
	 * @return Nette\Mail\Message
	 */
	public function createServiceNette__mail()
	{
		$service = new Nette\Mail\Message;
		trigger_error('Service nette.mail is deprecated.', 16384);
		$service->setMailer($this->getService('nette.mailer'));
		return $service;
	}


	/**
	 * @return Nette\Mail\SendmailMailer
	 */
	public function createServiceNette__mailer()
	{
		$service = new Nette\Mail\SendmailMailer;
		return $service;
	}


	/**
	 * @return Nette\Application\PresenterFactory
	 */
	public function createServiceNette__presenterFactory()
	{
		$service = new Nette\Application\PresenterFactory('/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../', $this);
		return $service;
	}


	/**
	 * @return Nette\Templating\FileTemplate
	 */
	public function createServiceNette__template()
	{
		$service = new Nette\Templating\FileTemplate;
		$service->registerFilter($this->getService('nette.latte'));
		$service->registerHelperLoader('Nette\\Templating\\Helpers::loader');
		return $service;
	}


	/**
	 * @return Nette\Caching\Storages\PhpFileStorage
	 */
	public function createServiceNette__templateCacheStorage()
	{
		$service = new Nette\Caching\Storages\PhpFileStorage('/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/../tmp/2b1d5165882c19dfebfb66b6ff4da633/cache', $this->getService('nette.cacheJournal'));
		return $service;
	}


	/**
	 * @return Nette\Http\UserStorage
	 */
	public function createServiceNette__userStorage()
	{
		$service = new Nette\Http\UserStorage($this->getService('session'));
		return $service;
	}


	/**
	 * @return Foxy\Media\Controler
	 */
	public function createServiceNetteFoxyForms__mediaControler()
	{
		$service = new Foxy\Media\Controler('/media', $this->getService('netteFoxyForms.mediaStorage'));
		return $service;
	}


	/**
	 * @return Foxy\Media\Storage
	 */
	public function createServiceNetteFoxyForms__mediaStorage()
	{
		$service = new Foxy\Media\Storage('/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/FoxyTests/Forms/../../tmp', 'IMG_%04d');
		return $service;
	}


	/**
	 * @return Nette\Application\Routers\RouteList
	 */
	public function createServiceRouter()
	{
		$service = new Nette\Application\Routers\RouteList;
		return $service;
	}


	/**
	 * @return Nette\Http\Session
	 */
	public function createServiceSession()
	{
		$service = new Nette\Http\Session($this->getService('httpRequest'), $this->getService('httpResponse'));
		return $service;
	}


	/**
	 * @return Nette\Security\User
	 */
	public function createServiceUser()
	{
		$service = new Nette\Security\User($this->getService('nette.userStorage'));
		return $service;
	}


	public function initialize()
	{
		date_default_timezone_set('Europe/Prague');
		Nette\Caching\Storages\FileStorage::$useDirectories = TRUE;
		@header('X-Powered-By: Nette Framework');
		@header('Content-Type: text/html; charset=utf-8');
		Nette\Utils\SafeStream::register();
	}

}
