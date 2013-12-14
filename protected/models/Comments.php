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
 * Модель для таблицы "{{comments}}".
 * Доступные поля таблицы '{{comments}}':
 * @property integer $id ID комментария
 * @property string $name Имя комментатора
 * @property string $comment Комментарий
 * @property string $email Почта
 * @property string $addr IP
 * @property integer $date дата
 * @property integer $bid ID бана
 */
class Comments extends CActiveRecord
{
	public $verifyCode;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{comments}}';
	}

	public function rules()
	{
		return array(
			array('name, comment, email', 'required'),
			array('name', 'length', 'max'=>35),
			array('email', 'length', 'max'=>100),
			array('email', 'email'),
			array('id, name, comment, email, addr, date, bid', 'safe', 'on'=>'search'),
			array(
                'verifyCode',
                'captcha',
                // авторизованным пользователям код можно не вводить
                'allowEmpty'=>!Yii::app()->user->isGuest || !CCaptcha::checkRequirements(),
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
			'name' => 'Имя',
			'comment' => 'Комментарий',
			'email' => 'Email',
			'addr' => 'Адрес',
			'date' => 'Дата',
			'bid' => 'ID бана',
			'verifyCode' => 'Код проверки',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('addr',$this->addr,true);
		$criteria->compare('date',$this->date);
		$criteria->compare('bid',$this->bid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeSave() {

		if($this->isNewRecord) {
			$this->date = time();
			$this->addr = $_SERVER['REMOTE_ADDR'];
		}

		return parent::beforeSave();
	}

	public function afterSave() {
		if(!$this->isNewRecord)
			Syslog::add(Logs::LOG_EDITED, 'Изменен комментарий к бану № <strong>' . $this->bid . '</strong>, написанный пользователем <strong>' . $this->name . '</strong>');
		return parent::afterSave();
	}

	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удален комментарий к бану <strong>' . $this->bid . '</strong>, написанный пользователем <strong>' . $this->name . '</strong>');
		return parent::afterDelete();
	}
}