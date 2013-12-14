<?php
/**
 * Вьюшка инсталлера
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle=Yii::app()->name . ' - Обновление';
$this->breadcrumbs=array(
	'Обновление',
);
?>

<h2>Обновление</h2>

<?php if(empty($_POST['license'])): ?>

	<?php echo CHtml::form(); ?>

	<p><label class="checkbox"><?php echo CHtml::checkBox('license'); ?> Я принимаю условия <?php
			echo CHtml::link('лицензионного соглашения', array('/site/license'), array('target' => '_blank')) ?></label></p>

	<?php echo CHtml::submitButton('Обновить', array('class' => 'btn btn-primary')); ?><br>

	<?php echo CHtml::endForm(); ?>

<?php else: ?>

	<div class="alert alert-success">Обновление прошло успешно!</div>

<?php endif; ?>