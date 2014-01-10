<?php

# @package nette-foxy-forms
#
# Generate nette form components using Doctrine entity annotations
#
# @author Jiri Dubansky <jiri@dubansky.cz>

namespace Foxy\Media;


class Storage implements IStorage
{

	# @string
	protected $mediaDir;

	# @string
	protected $imagePattern;


	# Construct Storage
	#
	# @param string $mediaDir
	public function __construct($mediaDir, $imagePattern = 'IMG_%04d')
	{
		$this->mediaDir = $mediaDir;
		$this->imagePattern = $imagePattern;
	}


	# Saves file to media directory
	#
	# @param \Nette\Http\FileUpload $file
	# @param string $dest
	# @return string
	public function saveFile(\Nette\Http\FileUpload $file, $dest)
	{
		$mediaDir = $this->mediaDir;
		$imagePattern = $this->imagePattern;

		$dest = preg_replace_callback(
			'|(.+)\/(.+)\.(.+)$|',
			function($matches) use($mediaDir, $imagePattern) {
				$dir = strftime($matches[1]) . '/';
				$absDir = $mediaDir . $dir;
				$i = 0;

				if ($handle = @opendir($absDir)) {
					while (($file = readdir($handle)) !== false){
						if (! in_array($file, array('.', '..'))
							&& ! is_dir($absDir.$file)) {
							$i++;
						}
					}
				}

				return sprintf(
					'%s'.$imagePattern.'.%s',
					$dir,
					$i,
					strtoupper($matches[3])
				);
			},
			$dest
		);

		$file->move($mediaDir . $dest);

		return $dest;
	}


	# Checks if file exists
	#
	# @param \Nette\Http\FileUpload & $file
	# @param string $dest
	# @return bool
	public function fileExists(\Nette\Http\FileUpload $file, $dest)
	{
		$absDirPath = $this->mediaDir . strftime($dest);
		return file_exists($absDirPath);
	}
}
