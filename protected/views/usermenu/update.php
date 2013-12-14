<?php
/**
 * Вьюшка редактирования ссылки главного меню
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Редактировать ссылку ';
$this->breadcrumbs=array(
	'Админцентр'=> array('/admin/index'),
	'Главное меню'=>array('admin'),
	'Ссылка № '.$model->id=>array('view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Добавить ссылку','url'=>array('create')),
	array('label'=>'Управление ссылками','url'=>array('admin')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webmainmenu'));
?>

<h2>Редактировать ссылку № <?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>