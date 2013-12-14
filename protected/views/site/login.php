<?php
/**
 * Вьюшка формы логина
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle=Yii::app()->name . ' :: Вход';
$this->breadcrumbs=array(
	'Вход',
);
?>

<h2>Вход</h2>

<p>Пожалуйста, заполните следующую форму:</p>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'login-form',
    'type'=>'horizontal',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->textFieldRow($model,'username'); ?>

	<?php echo $form->passwordFieldRow($model,'password'); ?>

	<?php echo $form->checkBoxRow($model,'rememberMe'); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Войти',
        )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
