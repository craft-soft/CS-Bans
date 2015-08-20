<?php
/**
 * Вьюшка формы логина
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
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

	<?php if(CCaptcha::checkRequirements() && Yii::app()->request->cookies['captcha_auth']): ?>
		<div class="control-group">
			<?php echo CHtml::label('Проверочный код', 'verify', array('class'=>'control-label'))?>
			<div class="controls">
				<p><?php echo CHtml::textField('verify')?></p>
				<?php $this->widget('ext.kcaptcha.KCaptcha', array('showRefreshButton' => FALSE)); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Войти',
        )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
