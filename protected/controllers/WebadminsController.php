<?php
/**
 * Контроллер веб админов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class WebadminsController extends Controller
{
	public $layout='//layouts/column2';
	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}

	/**
	 * Просмотр конкретного админа
	 * @param integer $id ID админа
	 */
	public function actionView($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_view'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Добавление веб админа
	 */
	public function actionCreate()
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");

		$model=new Webadmins;

		$this->performAjaxValidation($model);

		if(isset($_POST['Webadmins']))
		{
			$model->attributes=$_POST['Webadmins'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Редактировать веб админа
	 * @param integer $id ID веб админа
	 */
	public function actionUpdate($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=$this->loadModel($id);

		// аякс проверка формы
		$this->performAjaxValidation($model);

		if(isset($_POST['Webadmins']))
		{
			$model->attributes=$_POST['Webadmins'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,

		));
	}

	/**
	 * @param integer $id ID веб админа
	 */
	public function actionDelete($id)
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$this->loadModel($id)->delete();

		// Если не аякс запрос - редиректим
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Управление веб админами
	 */
	public function actionAdmin()
	{
		// Проверяем права
		if(!Webadmins::checkAccess('webadmins_edit'))
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=new Webadmins('search');
		$model->unsetAttributes();
		if(isset($_GET['Webadmins']))
			$model->attributes=$_GET['Webadmins'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Webadmins::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='webadmins-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
