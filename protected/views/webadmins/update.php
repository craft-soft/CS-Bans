<?php
/**
 * Вьюшка редактирования веб админа
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Редактировать WEB админа' . $model->username;
$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Веб админы'=>array('admin'),
	'Редактировать ВЕБ админа ' . $model->username,
);

$this->menu=array(
	array('label'=>'Админцентр', 'url'=>array('/admin/index')),
	array('label'=>'Управление веб админами', 'url'=>array('admin')),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Удалить?')),
);
$this->renderPartial('/admin/mainmenu', array('active' =>'site', 'activebtn' => 'webadmins'));
?>

<h2>Редактировать ВЕБ админа "<?php echo $model->username; ?>"</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>