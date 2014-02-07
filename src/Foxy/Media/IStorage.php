<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Media;


interface IStorage
{

	/**
	 * Saves file to media directory
	 *
	 * @param \Nette\Http\FileUpload $file
	 * @param string $dest
	 * @return string
	 */
	public function saveFile(\Nette\Http\FileUpload $file, $dest);


	/**
	 * Checks if file exists
	 *
	 * @param string $dest
	 * @return bool
	 */
	public function fileExists($dest);
}
