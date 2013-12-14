<?php
/**
 * Контроллер настроек сайта
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class WebconfigController extends Controller
{
	public $layout='//layouts/column2';
	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}

	public function actionView($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionUpdate($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=$this->loadModel($id);

		// $this->performAjaxValidation($model);

		if(isset($_POST['Webconfig']))
		{
			$model->attributes=$_POST['Webconfig'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$this->loadModel($id)->delete();

		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionAdmin()
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=new Webconfig('search');
		$model->unsetAttributes();
		if(isset($_GET['Webconfig']))
			$model->attributes=$_GET['Webconfig'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Webconfig::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='webconfig-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
