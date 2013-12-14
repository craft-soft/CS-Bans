<?php
/**
 * Вьюшка редактирования админа серверов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name . ' :: Админцентр - Редактировать админа';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'AmxModX админы' => array('admin'),
	'Редактировать админа ' . $model->nickname
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servamxadmins'));

$this->menu=array(
	array('label'=>'Добавить AmxModX админа', 'url'=>array('create')),
	array('label'=>'Управление AmxModX админами', 'url'=>array('admin')),
);
?>

<h2>Редактировать AmxModX админа <?php echo $model->nickname; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>