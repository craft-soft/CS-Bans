<?php
/**
 * Вьюшка редактирования причины бана
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name .' :: Админцентр - Причины банов';

$this->breadcrumbs=array(
	'Админцентр'=>array('/admin/index'),
	'Причины банов' => array('/admin/reasons'),
	'Редактировать причину ' . $model->reason
);

$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servreasons'));

?>

<h2>Редактировать причину "<?php echo $model->reason; ?>"</h2>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>