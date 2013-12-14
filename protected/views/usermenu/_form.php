<?php
/**
 * Форма редактирования/добавления ссылки главного меню
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
	'id'=>'usermenu-form',
	'enableAjaxValidation'=>TRUE,
)); 
$q = Yii::app()->db->createCommand('SELECT MAX(`pos`) AS `mpos` FROM {{usermenu}}')->queryAll();
$q = intval($q[0]['mpos'] + 1);
if($model->isNewRecord)
	$model->pos = $q;
?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->dropDownListRow(
			$model,
			'pos',
			Usermenu::getPositions(),
			array(
				'class'=>'span5',
			)
		); 
	?>

	<?php echo $form->dropDownListRow($model,'activ',array('0' => 'Нет', '1' => 'Да'),array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'lang_key',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'url',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'lang_key2',array('class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'url2',array('class'=>'span5','maxlength'=>64)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
