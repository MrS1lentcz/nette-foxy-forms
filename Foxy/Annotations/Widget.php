<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY","ANNOTATION"})
 */
class Widget implements \Doctrine\ORM\Mapping\Annotation
{

	/**
	 * @var string
	 */
	public $type;
}
