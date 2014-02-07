<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy;


class RenderContext
{
	/**
	 * @var string
	 */
	public $uploadWrapper;

	/**
	 * @var string
	 */
	public $uploadSeparator;

	/**
	 * @var string
	 */
	public $datetimeFormat;

	/**
	 * @var string
	 */
	public $dateFormat;

	/**
	 * @var string
	 */
	public $timeFormat;

	/**
	 * @var \Foxy\Media\Controler
	 */
	public $mediaControler;
}
