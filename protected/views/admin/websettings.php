<?php
/**
 * Вьюшка настроек системы
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Настройки системы';
$this->pageTitle = Yii::app()->name . ' :: Админцентр - ' . $page;

$this->breadcrumbs=array(
	'Админцентр' => array('/admin/index'),
	$page,
);

$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'websettings'));
?>
<h2>Настройки системы</h2>
<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'settings-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,
));
?>
<p class="note">Поля, отмеченные <span class="required">*</span> обязательны к заполнению.</p>
<fieldset>
	<legend>Система</legend>
	<?php echo $form->errorSummary($model); ?>
	<?php //echo $form->dropDownListRow($model, 'default_lang', array('ru' => 'Русский', 'en' => 'English')); ?>
	<?php //echo $form->checkBoxRow($model, 'use_capture', array('1' => 'Да', '0' => 'Нет')); ?>
	<?php echo $form->checkBoxRow($model, 'auto_prune'); ?>
	<?php echo $form->textFieldRow($model, 'max_file_size'); ?>
	<?php echo $form->textFieldRow($model, 'cookie'); ?>
	<?php echo $form->textFieldRow($model, 'max_offences'); ?>
	<?php echo $form->textFieldRow($model, 'max_offences_reason'); ?>
	<legend>Вид</legend>
	<?php //echo $form->dropDownListRow($model, 'banner', array('---' => '---', 'amxbans.png' => 'amxbans.png')); ?>
	<?php //echo $form->textFieldRow($model, 'banner_url'); ?>
	<?php echo $form->dropDownListRow($model, 'design', $themes); ?>
	<?php echo $form->dropDownListRow($model, 'start_page', array(
		'/site/index' => 'Главная',
		'/bans/index' => 'Банлист',
		'/serverinfo/index' => 'Серверы',
		'/amxadmins/index' => 'Админы',
	));
	?>
	<legend>Комментарии</legend>
	<?php echo $form->checkBoxRow($model, 'use_comment'); ?>
	<?php echo $form->checkBoxRow($model, 'comment_all'); ?>
	<legend>Файлы</legend>
	<?php echo $form->checkBoxRow($model, 'use_demo'); ?>
	<?php echo $form->checkBoxRow($model, 'demo_all'); ?>
	<?php echo $form->textFieldRow($model, 'file_type'); ?>
	<legend>Банлист</legend>
	<?php echo $form->textFieldRow($model, 'bans_per_page'); ?>
	<?php echo $form->checkBoxRow($model, 'show_comment_count'); ?>
	<?php echo $form->checkBoxRow($model, 'show_demo_count'); ?>
	<?php echo $form->checkBoxRow($model, 'show_kick_count'); ?>
</fieldset>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Сохранить')); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Сбросить')); ?>
</div>

<?php $this->endWidget(); ?>
