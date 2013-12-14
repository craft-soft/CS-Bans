<?php
/**
 * Контроллер системного лога
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

class LogsController extends Controller
{

	public $layout='//layouts/column2';

	public function filters()
	{
		return array();
	}

	public function actionView($id)
	{
		// Проверка прав
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionDelete($id)
	{
		// Проверка прав
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		if(Yii::app()->request->isPostRequest)
		{
			$this->loadModel($id)->delete();
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionAdmin()
	{
		// Проверка прав
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=new Logs('search');
		$model->unsetAttributes();
		if(isset($_GET['Logs']))
			$model->attributes=$_GET['Logs'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Logs::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='logs-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
