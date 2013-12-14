<?php 
/**
 * Форма добавления/редактирования уровней админов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'type' => 'horisontal',
	'id'=>'levels-form',
	'enableAjaxValidation'=>false,
));
$array = Levels::getValues();
$array2 = Levels::getValues(TRUE);
?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->dropDownListRow($model,'bans_add', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'bans_edit', $array2,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'bans_delete', $array2,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'bans_unban', $array2,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'bans_import', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'bans_export', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'amxadmins_view', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'amxadmins_edit', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'webadmins_view', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'webadmins_edit', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'websettings_view', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'websettings_edit', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'permissions_edit', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'prune_db', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'servers_edit', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<?php echo $form->dropDownListRow($model,'ip_view', $array,array('class'=>'span5','maxlength'=>3)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
