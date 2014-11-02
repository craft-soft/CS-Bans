<?php
/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

/**
 * Модель для таблицы  "{{files}}".
 *
 * Поля таблицы '{{files}}' доступны в следующих свойствах:
 * @property integer $id
 * @property integer $upload_time
 * @property integer $down_count
 * @property integer $bid
 * @property string $demo_file
 * @property string $demo_real
 * @property integer $file_size
 * @property string $comment
 * @property string $name
 * @property string $email
 * @property string $addr
 */
class Files extends CActiveRecord
{
	public $verifyCode;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{files}}';
	}

	public function rules()
	{
		return array(
			array('down_count', 'default', 'value' => 0),
			array('down_count', 'unsafe'),
			array('name, email, comment', 'required'),
			array('email', 'email'),
			array('name, email', 'length', 'max'=>64),
			array('demo_real', 'file', 'types' => Yii::app()->config->file_type,
					'maxSize' => Yii::app()->config->max_file_size * 1024 * 1024, 'on' => 'insert'),
			array('id, upload_time, down_count, bid, demo_file, demo_real, file_size, comment, name, email, addr', 'safe', 'on'=>'search'),
			array(
                'verifyCode',
                'captcha',
                // авторизованным пользователям код можно не вводить
                'allowEmpty'=>!Yii::app()->user->isGuest || !CCaptcha::checkRequirements(),
				'on' => 'insert',
            ),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'upload_time' => 'Дата загрузки',
			'down_count' => 'Скачек',
			'bid' => 'ID бана',
			'demo_file' => 'Файл демо',
			'demo_real' => 'Демо',
			'file_size' => 'Размер файла',
			'comment' => 'Комментарий',
			'name' => 'Имя',
			'email' => 'Email',
			'addr' => 'Адрес',
			'verifyCode' => 'Код проверки',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('upload_time',$this->upload_time);
		$criteria->compare('down_count',$this->down_count);
		$criteria->compare('bid',$this->bid);
		$criteria->compare('demo_file',$this->demo_file,true);
		$criteria->compare('demo_real',$this->demo_real,true);
		$criteria->compare('file_size',$this->file_size);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('addr',$this->addr,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeDelete()
	{
		if (parent::beforeDelete())
		{
                    if (
                        $this->demo_real
                            &&
                        @unlink(Yii::getPathOfAlias('webroot.include.files') . DIRECTORY_SEPARATOR . $this->demo_file)    
                    ) {
			return true;
                    }
		}
                
                return false;
	}

	protected function beforeValidate() {

		if($this->isNewRecord) {
			$this->demo_real = CUploadedFile::getInstance($this, 'demo_real');
			if($this->demo_real === NULL) {
				$this->addError('demo_real', 'Ошибка загрузки файла');
			}
		}

		return parent::beforeValidate();
	}

	protected function beforeSave() {

		if($this->isNewRecord) {
			$this->file_size = $this->demo_real->getSize();
			$this->addr = $_SERVER['REMOTE_ADDR'];
			$this->upload_time = time();
			$this->demo_file = md5(microtime().uniqid(rand(),true))."_".intval($this->bid);
		}

		return parent::beforeSave();
	}

	protected function afterSave() {
		if(!$this->isNewRecord) {
			Syslog::add(Logs::LOG_EDITED, 'Изменены детали файла <strong>' . $this->demo_real . '</strong> к бану № <strong>' . $this->bid . '</strong>');
		}
		else {
			$this->demo_real->saveAs(Yii::getPathOfAlias('webroot.include.files') . DIRECTORY_SEPARATOR . $this->demo_file);
		}

		return parent::afterSave();
	}

	protected function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удален файл <strong>' . $this->demo_real . '</strong> к бану № <strong>' . $this->bid . '</strong>');
		return parent::afterDelete();
	}

}