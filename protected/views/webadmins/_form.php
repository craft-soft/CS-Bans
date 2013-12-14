<?php
/**
 * Форма добавления/редактирования веб администратора
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */
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
	<?php echo $form->textFieldRow($model,'username',array('size'=>32,'maxlength'=>32)); ?>
	<?php echo $form->passwordFieldRow($model,'password',array('size'=>32,'maxlength'=>32, 'value' => '')); ?>
	<?php echo $form->dropdownListRow($model,'level', Levels::getList()); ?>
	<?php echo $form->textFieldRow($model,'email',array('size'=>60,'maxlength'=>64)); ?>
	<?php if(!$model->isNewRecord)
		echo $form->textFieldRow($model,'try'); 
	?>
</fieldset>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>$model->isNewRecord ? 'Создать' : 'Обновить')); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Сбросить')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>