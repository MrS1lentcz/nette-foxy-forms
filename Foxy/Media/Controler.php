<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>

namespace Foxy\Media;


class Controler {

	# @string
	protected $mediaUrl;

	# @IStorage
	protected $storage;


	# Construct Controler
	#
	# @param string $mediaUrl
	# @param string $mediaDir
	public function __construct($mediaUrl, IStorage $storage)
	{
		$this->mediaUrl = $mediaUrl;
		$this->storage = $storage;
	}

	# Returns completed url to file
	#
	# @param string $filepath
	# @return string
	public function getUrl($filepath)
	{
		return $this->mediaUrl . '/' . $filepath;
	}

	# Saves file to media directory
	#
	# @param \Nette\Http\FileUpload $file
	# @param string $dest
	# @param bool $unlink
	# @return \Nette\Http\FileUpload
	public function saveFile(\Nette\Http\FileUpload $file, & $dest)
	{
		$dest = $this->storage->saveFile($file, $dest);
	}
}
