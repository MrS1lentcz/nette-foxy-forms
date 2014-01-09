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

    # @string
    protected $imagePattern;


    # Construct MediaStorage
    #
    # @param string $mediaUrl
    # @param string $mediaDir
    public function __construct($mediaUrl,
                                $mediaDir,
                                $imagePattern = 'IMG_%04d')
    {
        $this->mediaUrl = $mediaUrl;
        $this->mediaDir = $mediaDir;
        $this->imagePattern = $imagePattern;
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
    # @param \Nette\Http\FileUpload & $file
    # @param string $dest
    # @param bool $unlink
    # @return \Nette\Http\FileUpload
    public function saveFile(\Nette\Http\FileUpload & $file, & $dest)
    {
        $mediaDir = $this->mediaDir;
        $imagePattern = $this->imagePattern;

        $dest = preg_replace_callback(
            '|(.+)\/(.+)\.(.+)$|',
            function($matches) use($mediaDir, $imagePattern) {
                $dir = strftime($matches[1]) . '/';
                $absDir = $mediaDir . $dir;
                $i = 0;

                if ($handle = opendir($absDir)) {
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
