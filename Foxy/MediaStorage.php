<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>

namespace Foxy;


class MediaStorage {

	# @string
	protected $mediaUrl;

	# @string
	protected $mediaDir;


	# Construct MediaStorage
	#
	# @param string $mediaUrl
	# @param string $mediaDir
	public function __construct($mediaUrl, $mediaDir)
	{
		$this->mediaUrl = $mediaUrl;
		$this->mediaDir = $mediaDir;
	}

	# Returns completed url to file
	#
	# @param string $filepath
	# @return string
	public function getUrl($filepath)
	{
		return $this->mediaUrl . $filepath;
	}

	# Saves file to media directory
	#
	# @param \Nette\Http\FileUpload & $file
	# @param string $dest
	# @param bool $unlink
	# @return \Nette\Http\FileUpload
	public function saveFile(\Nette\Http\FileUpload & $file, & $dest)
	{
		do {
			$parts = explode('/',$dest);
			$fileName = array_pop($parts);
			$fileParts = explode('.', $fileName);
			$ext = isset($fileParts[1]) ? '.'.$fileParts[1] : '';
			$dest = implode('/', $parts) . '/' . sha1($fileName . microtime()) . $ext;
		} while ($this->fileExists($file, $dest));

		$absDirPath = $this->mediaDir . strftime($dest);
		$file->move($absDirPath);
	}

	# Checks if file exists
	#
	# @param \Nette\Http\FileUpload & $file
	# @param string $dest
	# @return bool
	public function fileExists(\Nette\Http\FileUpload & $file, $dest)
	{
		$absDirPath = $this->mediaDir . strftime($dest);
		return file_exists($absDirPath);
	}
}
