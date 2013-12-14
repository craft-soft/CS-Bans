<?php
/**
 * Форма редактирования сервера
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */


Yii::app()->clientScript->registerScript('command', '
	$("#commandlist").change(function() {
		$("#command").val($(this).val());
	});
	$("#sendcommand").click(function() {
		var command = $("#command").val();
		if(!command)
			return alert("Введите команду");

		$.post(
			"'.Yii::app()->createUrl('/serverinfo/sendcommand', array('id' => $model->id)).'",
			{
				"'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'",
				"command": command
			},
			function(data) {$("#output").html(data);}
		);
		return false;
	})
');
?>

<div class="form">

	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'type' => 'vertical',
		'id'=>'webadmins-form',
		'enableAjaxValidation'=>true,
	)); ?>

	<p class="note">Поля, отмеченные <span class="required">*</span> обязательны к заполнению.</p>
	<fieldset>
		<?php echo $form->errorSummary($model); ?>
			<?php echo $form->passwordFieldRow($model,'rcon',array('size'=>32,'maxlength'=>32)); ?>
			<?php echo $form->textFieldRow($model,'amxban_motd',array('size'=>60,'maxlength'=>250)); ?>
			<?php echo $form->textFieldRow($model,'motd_delay'); ?>
			<?php echo $form->dropDownListRow($model,'reasons', ReasonsSet::getList()); ?>
			<?php echo $form->dropDownListRow($model,'timezone_fixx', $timezones); ?>
	</fieldset>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>$model->isNewRecord ? 'Создать' : 'Обновить')); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Сбросить')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<?php if(!$model->isNewrecord && $model->rcon): ?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>
				Отправить RCON команду на сервер
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo CHtml::dropDownList('', '', Serverinfo::getCommands(), array('id' => 'commandlist', 'style' => 'margin-top: 10px')); ?>
				&nbsp;
				<?php echo CHtml::textField('', '', array('id' => 'command', 'style' => 'margin-top: 10px')); ?>
				&nbsp;
				<?php echo CHtml::button('Отправить', array('id' => 'sendcommand', 'class' => 'btn btn-info')); ?>
			</td>
		</tr>
		<tr>
			<td>
				<pre id="output" style="min-height: 400px; font-size: 12px"></pre>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
