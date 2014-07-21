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

	/**
	 * @var bool
	 */
	public $nullable;

	/**
	 * @var bool
	 */
	public $unique;

	/**
	 * @var int
	 */
	public $length;

	# ------------------------
	# Custom
	# ------------------------

	public $customUpload = FALSE;

	/**
	 * @var string
	 */
	public $label;
}
