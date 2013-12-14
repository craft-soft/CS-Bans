<?php
/**
 * Вьюшка HTTP ошибок сайта
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle=Yii::app()->name . ' - Ошибка';
$this->breadcrumbs=array(
	'Ошибка',
);
?>

<h2>Ошибка <?php echo $code; ?></h2>

<div class="alert alert-error error">
<?php echo CHtml::encode($message); ?>
</div>