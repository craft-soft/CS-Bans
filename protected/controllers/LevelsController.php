<?php
/**
 * Контроллер уровней админов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

class LevelsController extends Controller
{
	public $layout='//layouts/column2';

	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete'
		);
	}

	public function actionCreate()
	{
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=new Levels;
		if(isset($_POST['Levels']))
		{
			$model->attributes=$_POST['Levels'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate($id)
	{
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=$this->loadModel($id);

		if(isset($_POST['Levels']))
		{
			$model->attributes=$_POST['Levels'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		if(!Webadmins::checkAccess('websettings_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionAdmin()
	{
		if(!Webadmins::checkAccess('websettings_view'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=new Levels('search');
		$model->unsetAttributes();
		if(isset($_GET['Levels']))
			$model->attributes=$_GET['Levels'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Levels::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
