<?php //netteCache[01]000398a:2:{s:4:"time";s:21:"0.42782100 1391782524";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:84:"/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/@layout.latte";i:2;i:1388869757;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2013-12-31";}}}?><?php

// source file: /home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/@layout.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'ublawi5jcz')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Nette foxy forms | testing</title>
</head>
<body>

    <div>

<?php $iterations = 0; foreach ($flashes as $flash) { ?>        <div class="alert alert-<?php if ($flash->type=='error') { ?>
danger<?php } else { echo htmlSpecialChars($flash->type) ;} ?>"><?php echo Nette\Templating\Helpers::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; } ?>

<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'content', $template->getParameters()) ?>

    </div>

</body>
</html>
