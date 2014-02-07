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

    const ANNOTATION_READER_CLASS = '\Doctrine\Common\Annotations\SimpleAnnotationReader';

    /** @var array */
    protected
        $config = array(
            'mediaDir' => 'media/upload/',
            'imagePattern' => 'IMG_%04d',
            'mediaUrl' => '/media',
            'mediaStorage' => NULL,
            'mediaMacro' => TRUE,
            'useNamespace' => TRUE,
        );

    /** @var bool */
    private static $isInited = FALSE;

    /** @var SimpleAnnotationReader */
    private static $annotationReader;


    /**
     * Initialize static parts
     * Public method for back support
     */
    public static function init($useNamespace = TRUE)
    {
        if (self::$isInited == FALSE) {
            \Doctrine\Common\Annotations\AnnotationRegistry
                ::registerFile(__DIR__ . '/../Annotations/Widget.php');

            $readerClass = self::ANNOTATION_READER_CLASS;
            self::$annotationReader = new $readerClass();

            if (! $useNamespace) {
                self::$annotationReader->addNamespace('Foxy\Annotations');
            }
            self::$isInited = TRUE;
        }
    }


    /**
     * @return Doctrine\Common\Annotations\Reader
     */
    public static function getAnnotationReader()
    {
        self::init();
        return self::$annotationReader;
    }


    /**
     * Loads configuration
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->config);

        self::init($config['useNamespace']);

        # Foxy\Media\Storage
        $builder->addDefinition($this->prefix('mediaStorage'))
            ->setClass(
                'Foxy\Media\Storage',
                array(
                    'mediaDir' => $config['mediaDir'],
                    'imagePattern' => $config['imagePattern'],
                )
            );

        # Foxy\Media\Controler
        $builder->addDefinition($this->prefix('mediaControler'))
            ->setClass(
                'Foxy\Media\Controler',
                array(
                    $config['mediaUrl'],
                    $this->prefix('@mediaStorage'),
                )
            );

        # Foxy\Macros\Media
        if ($builder->hasDefinition('nette.latte')) {
            $builder->getDefinition('nette.latte')
                ->addSetup('Foxy\Macros\Media::install(?->getCompiler())', array('@self'));
        }
    }
}
