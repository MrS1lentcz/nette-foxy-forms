<?php //netteCache[01]000406a:2:{s:4:"time";s:21:"0.41912100 1391782524";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:92:"/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/Homepage/detail.latte";i:2;i:1389653176;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2013-12-31";}}}?><?php

// source file: /home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/Homepage/detail.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'irl9lj5djd')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb0e75540351_content')) { function _lb0e75540351_content($_l, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>	<div>
<?php if ($entity instanceof Category) { ?>
			<h3>Detail kategorie <?php echo Nette\Templating\Helpers::escapeHtml($entity, ENT_NOQUOTES) ?></h3>

<?php $_ctrl = $_control->getComponent("categoryForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ?>

				<img src="<?php echo $presenter->context->getByType('Foxy\Media\Controler')->getUrl($entity->getImage(), array(30, NULL, TRUE)) ?>">
<?php } ?>


<?php if ($entity instanceof Product) { ?>
			<h3>Detail produktu <?php echo Nette\Templating\Helpers::escapeHtml($entity, ENT_NOQUOTES) ?></h3>

<?php $_ctrl = $_control->getComponent("productForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ;} ?>


<?php if ($entity instanceof Parameter) { ?>
			<h3>Detail parametru <?php echo Nette\Templating\Helpers::escapeHtml($entity, ENT_NOQUOTES) ?></h3>

<?php $_ctrl = $_control->getComponent("parameterForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ;} ?>

<?php if ($entity instanceof User) { ?>
			<h3>Detail uzivatele <?php echo Nette\Templating\Helpers::escapeHtml($entity, ENT_NOQUOTES) ?></h3>

<?php $_ctrl = $_control->getComponent("userForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ;} ?>
	</div>

<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>

<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 