<?php
/**
 * Форма добавления и редактирования бана
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

Yii::app()->clientScript->registerScript('addban', '
$("#Bans_ban_reason").change(function(){
	if(this.value == "selfreason")
	{
		//$(this).prop("disabled", true);
		$("#selfreasoncheckbox").prop("checked", true);
		$(".selfreason").show();
	}
	else{
		$("#selfreasoncheckbox").prop("checked", false);
		//$("#self_ban_reason").val("");
		$(".selfreason").hide();
	}
});
$("#Reasons").change(function(){
	if($(this).val() !== "selfreason")
		$("#Bans_ban_reason").val($(this).val());
	else
		$("#Bans_ban_reason").val("");
});');
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'bans-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>TRUE,
));

$self = FALSE;

if(!$model->isNewRecord)
{
	if(!in_array($model->ban_reason, Reasons::getList()))
	{
		Yii::app()->clientScript->registerScript('kostyl','
			$("#Bans_ban_reason").val("selfreason");
		');
		$self = true;
	}
}
?>

<p class="note">Поля, отмеченные <span class="required">*</span> обязательны к заполнению.</p>
<fieldset>
	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'player_nick', array('size'=>60,'maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model, 'player_id', array('size'=>35,'maxlength'=>35)); ?>
	<?php echo $form->textFieldRow($model, 'player_ip', array('size'=>32,'maxlength'=>32)); ?>

	<?php echo $form->dropDownListRow($model, 'ban_type', array('SI' => 'IP', 'S' => 'SteamID')); ?>

	<?php //echo $form->dropDownListRow($model, 'ban_reason', Reasons::getList()); ?>

	<div class="control-group ">
		<?php echo CHtml::label('Причина', 'Reasons', array('class' => 'control-label'));?>
		<div class="controls">
			<?php echo CHtml::dropDownList('Reasons', 'selfreason', Reasons::getList())?>
		</div>
	</div>
	
	<?php echo $form->textFieldRow($model, 'ban_reason', array('size'=>32,'maxlength'=>32)); ?>

	<?php echo $form->error($model,'ban_reason'); ?>

	<?php echo $form->dropDownListRow($model, 'ban_length', Bans::getBanLenght()); ?>
</fieldset>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Создать' : 'Обновить'));
		?>
		<?php echo CHtml::link(
				'Отмена',
				Yii::app()->createUrl('/admin/index'),
				array(
					'class' => 'btn btn-danger'
				)
			);
		?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
