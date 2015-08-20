<?php
/**
 * Вьюшка инсталлера
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle=Yii::app()->name . ' - Установка';
$this->breadcrumbs=array(
	'Установка',
);
?>

<h2>Установка</h2>

<?php if(count($error)): ?>

	<div class="alert alert-error">
	<b>Установка невозможна. Устраните следующие проблемы:</b><br>
	<ul><?php foreach($error AS $er): ?>
		<li><?php echo $er; ?></li>
	<?php endforeach; ?>
	</ul></div>

	<?php echo CHtml::link('Проверить ещё раз', array('site/install'), array('class' => 'btn')); ?>

<?php elseif($success): ?>

	<div class="alert alert-success">Установка прошла успешно!</div>

<?php else: ?>

	<?php echo CHtml::form(); ?>

	<?php echo CHtml::errorSummary($form); ?>

	<fieldset>
		<legend>Данные для подключения к БД</legend>
		<?php echo CHtml::activeTextField($form, 'db_host', array('placeholder' => 'Хост')); ?><br>
		<?php echo CHtml::activeTextField($form, 'db_user', array('placeholder' => 'Пользователь')); ?><br>
		<?php echo CHtml::activePasswordField($form, 'db_pass', array('placeholder' => 'Пароль')); ?><br>
		<?php echo CHtml::activeTextField($form, 'db_db', array('placeholder' => 'База данных')); ?><br>
		<?php echo CHtml::activeTextField($form, 'db_prefix', array('placeholder' => 'Префикс таблиц (необязательное)')); ?><br>
		<?php echo CHtml::ajaxButton('Проверить подключение', '', array('type' => 'post',
			'update' =>'#db-status'), array('class' => 'btn btn-small')); ?><br><br>
		<span id="db-status"></span>
	</fieldset>
	<br>
	<fieldset>
		<legend>Данные администратора</legend>
		<?php echo CHtml::activeTextField($form, 'login', array('placeholder' => 'Логин')); ?><br>
		<?php echo CHtml::activePasswordField($form, 'password', array('placeholder' => 'Пароль')); ?><br>
		<?php echo CHtml::activeEmailField($form, 'email', array('placeholder' => 'E-mail')); ?><br>
	</fieldset>
	<br>
	<label class="checkbox"><?php echo CHtml::activeCheckBox($form, 'license'); ?> Я принимаю условия <?php
		echo CHtml::link('лицензионного соглашения', array('/site/license'), array('target' => '_blank')) ?></label><br>
	<br>
	<?php echo CHtml::submitButton('Установить', array('class' => 'btn btn-primary')); ?><br>

	<?php echo CHtml::endForm(); ?>

<?php endif; ?>