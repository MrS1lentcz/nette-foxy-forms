<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Media;


class Controler {

	/**
	 * @var string
	 */
	protected $mediaUrl;

	/**
	 * @var IStorage
	 */
	protected $storage;


	/**
	 * Construct Controler
	 *
	 * @param string $mediaUrl
	 * @param IStorage $storage
	 */
	public function __construct($mediaUrl, IStorage $storage)
	{
		$this->mediaUrl = $mediaUrl;
		$this->storage = $storage;
	}


	/**
	 * Returns completed url
	 *
	 * @param string $filepath
	 * @return string
	 */
	public function getUrl($filepath)
	{
		return $this->mediaUrl . '/' . $filepath;
	}


	/**
	 * Saves file to media directory
	 *
	 * @param \Nette\Http\FileUpload $file
	 * @param string $dest
	 * @return string
	 */
	public function saveFile(\Nette\Http\FileUpload $file, $dest)
	{
		return $this->storage->saveFile(
			$file,
			strftime($dest)
		);
	}


	/**
	 * Checks if file exists
	 *
	 * @param \Nette\Http\FileUpload $file
	 * @param string $dest
	 * @return bool
	 */
	public function fileExists(\Nette\Http\FileUpload $file, $dest)
	{
		return $this->storage->fileExists($file, $dest);
	}
}
