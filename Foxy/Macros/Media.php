<?php

/**
 * @package nette-foxy-forms
 *
 * Generate nette form components using Doctrine entity annotations
 *
 * @author Jiri Dubansky <jiri@dubansky.cz>
 */


namespace Foxy\Macros;


class Media extends \Nette\Latte\Macros\MacroSet
{

    /**
     * Instal media macro
     *
     * @param \Nette\Latte\Compiler $compiler
     */
    public static function install(\Nette\Latte\Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('media', array($set, 'macroMedia'));
    }


    /**
     * macroMedia
     *
     * @param \Nette\Latte\MacroNode $node
     * @param \Nette\Latte\PhpWriter $writer
     * @return string
     */
    public function macroMedia(\Nette\Latte\MacroNode $node, \Nette\Latte\PhpWriter $writer)
    {
        $mediaControler = '$presenter->context->getByType(\'Foxy\Media\Controler\')';
        $getUrl = '->getUrl(%node.word, %node.array);';
        return $writer->write('echo ' . $mediaControler . $getUrl);
    }
}
