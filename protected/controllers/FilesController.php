<?php
/**
 * Контроллер файлов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
class FilesController extends Controller
{

	public $layout='//layouts/column2';

	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}

	public function actionUpdate($id)
	{
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403, "У Вас недостаточно прав");
		
		$model=$this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST['Files']))
		{
			$model->attributes=$_POST['Files'];
			if($model->save())
				$this->redirect(array('/bans/view','id'=>$model->bid));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403, "У Вас недостаточно прав");
		$this->loadModel($id)->delete();

		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Загрузка файла
	 * @param integer $id
	 * @throws CHttpException
	 */
	public function actionDownload($id)
	{
		// загружаем модель
		$file = $this->loadModel($id);
		// Путь к папке с файлом
		$file_local = Yii::getPathOfAlias('webroot.include.files') . DIRECTORY_SEPARATOR . $file->demo_file;
		if(!is_file($file_local))
		{
			throw new CHttpException(404, 'Файл не найден. Обратитесь к администратору');
			$this->redirect(Yii::app()->user->returnUrl);
		}

		$file->down_count++;
		$file->save(FALSE);

		Yii::app()->request->sendFile($file->demo_real, file_get_contents($file_local), 'application/download');
	}

	public function loadModel($id)
	{
		$model=Files::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model, $id='files-form')
	{
		if(isset($_POST['ajax']) && $_POST['ajax']===$id)
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
