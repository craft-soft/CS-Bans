<?php
/**
 * Вьюшка добавления бана
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name . ' :: Админцентр - Добавить бан';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'Добавить бан'
);

$this->renderPartial('/admin/mainmenu', array('active' =>'main', 'activebtn' => 'admaddban'));

?>

<h2>Добавить бан</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'activebtn' => 'admaddban')); ?>
