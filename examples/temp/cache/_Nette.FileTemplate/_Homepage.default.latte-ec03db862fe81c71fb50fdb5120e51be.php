<?php //netteCache[01]000407a:2:{s:4:"time";s:21:"0.70877500 1391782527";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:93:"/home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/Homepage/default.latte";i:2;i:1389023271;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2013-12-31";}}}?><?php

// source file: /home/s1lent/Desktop/www-projects/nette-foxy-forms/tests/app/templates/Homepage/default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'gf0dfromy5')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lba3132bcea7_content')) { function _lba3132bcea7_content($_l, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>	<div>
		<div style="float:left; width:300px;">
			<h3>Nova kategorie</h3>

<?php $_ctrl = $_control->getComponent("categoryForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Novy produkt</h3>

<?php $_ctrl = $_control->getComponent("productForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Novy parametr</h3>

<?php $_ctrl = $_control->getComponent("parameterForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Novy uzivatel</h3>

<?php $_ctrl = $_control->getComponent("userForm"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->redrawControl(NULL, FALSE); $_ctrl->render() ?>
		</div>
	</div>

	<div style="clear: both"></div>

	<div>
		<div style="float:left; width:300px;">
			<h3>Seznam kategorii</h3>

<?php $iterations = 0; foreach ($categories as $c) { ?>
				<a href="<?php echo htmlSpecialChars($template->safeurl($_presenter->link("detail", array('model' => 'Category', 'id' => $c->getId())))) ?>">
					Upravit <?php echo Nette\Templating\Helpers::escapeHtml($c, ENT_NOQUOTES) ?><br>
				</a>
<?php $iterations++; } ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Seznam produktu</h3>

<?php $iterations = 0; foreach ($products as $p) { ?>
				<a href="<?php echo htmlSpecialChars($template->safeurl($_presenter->link("detail", array('model' => 'Product', 'id' => $p->getId())))) ?>">
					Upravit <?php echo Nette\Templating\Helpers::escapeHtml($p, ENT_NOQUOTES) ?><br>
				</a>
<?php $iterations++; } ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Seznam parametru</h3>

<?php $iterations = 0; foreach ($parameters as $p) { ?>
				<a href="<?php echo htmlSpecialChars($template->safeurl($_presenter->link("detail", array('model' => 'Parameter', 'id' => $p->getId())))) ?>">
					Upravit <?php echo Nette\Templating\Helpers::escapeHtml($p, ENT_NOQUOTES) ?><br>
				</a>
<?php $iterations++; } ?>
		</div>

		<div style="float:left; width:300px;">
			<h3>Seznam uzivatelu</h3>

<?php $iterations = 0; foreach ($users as $p) { ?>
				<a href="<?php echo htmlSpecialChars($template->safeurl($_presenter->link("detail", array('model' => 'User', 'id' => $p->getId())))) ?>">
					Upravit <?php echo Nette\Templating\Helpers::escapeHtml($p, ENT_NOQUOTES) ?><br>
				</a>
<?php $iterations++; } ?>
		</div>
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