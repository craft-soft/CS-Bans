<?php
/**
 * Вьюшка создания группы причин банов
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$page = 'Добавить причину бана';
$this->pageTitle = Yii::app()->name .' :: Админцентр - ' . $page;

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Причины банов'=>array('/admin/reasons'),
	$page
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servreasons'));
?>

<h2>Добавить группу причин</h2>

<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'reasons-set-form',
	'enableAjaxValidation'=>TRUE,
));
?>

	<p class="note">Поля, отмеченные <span class="required">*</span> обязательны к заполнению.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'setname',array('class'=>'span5','maxlength'=>32)); ?>

	<?php echo $form->checkboxListRow($model, 'reasons', Reasons::model()->getList(FALSE)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
		<?php echo CHtml::link(
				'Отмена',
				Yii::app()->createUrl('/admin/reasons'),
				array(
					'class' => 'btn btn-danger'
				)
			);
		?>
	</div>

<?php $this->endWidget(); ?>