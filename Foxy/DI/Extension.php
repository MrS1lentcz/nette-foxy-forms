<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */

namespace Foxy\DI;


class Extension extends \Nette\DI\CompilerExtension
{
	protected
		$config = array(
			'media_dir' => 'media/upload/',
			'image_pattern' => 'IMG_%04d',
			'media_url' => '/media',
			'media_storage' => NULL
		);


	/**
	 * Loads configuration
	 */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->config);

		# Foxy\Media\Storage
		if (! isset($config['mediaStorage'])) {
			$builder->addDefinition('mediaStorage')
				->setClass(
					'Foxy\Media\IStorage',
					array(
						'media_dir' => $config['media_dir'],
						'image_pattern' => $config['image_pattern'],
					)
				);
		}

		# Foxy\Media\Controler
        $builder->addDefinition('mediaControler')
            ->setClass(
                'Foxy\Media\Controler',
                array(
                    'media_url' => $config['media_dir'],
                    'media_storage' => '@mediaStorage',
                )
            );
    }


    public static function register(Configurator $config)
    {
        $compiler->addExtension('foxy-forms', new RedisExtension());
    }
}

/*
 *
 *

<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */

namespace Foxy\DI;


class Extension extends \Nette\DI\CompilerExtension
{
	protected
		$defaults = array(
			'media_dir' => 'media/upload/',
			'image_pattern' => 'IMG_%04d',
			'media_url' => '/media',
			'storage' => 'Foxy\Nejaka\Konkretni\Storage'
		);


	/**
	 * Loads configuration
	 */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

		# Foxy\Media\Storage
			$builder->addDefinition('mediaStorage')
				->setClass(
					'Foxy\Media\IStorage',
					array(
						'media_dir' => $config['media_dir'],
						'image_pattern' => $config['image_pattern'],
					)
				)
				->setFactory($config['storage']);

		# Foxy\Media\Controler
        $builder->addDefinition('mediaControler')
            ->setClass(
                'Foxy\Media\Controler',
                array(
                    'media_url' => $config['media_dir'],
                    'media_storage' => '@mediaStorage',
                )
            );
    }


    public static function register(Configurator $config)
    {
        $compiler->addExtension('foxy-forms', new RedisExtension());
    }
}
