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
 * Модель для таблицы "{{admins_servers}}"
 * Доступные поля таблицы '{{admins_servers}}':
 * @property integer $admin_id ID админа
 * @property integer $server_id ID сервера
 * @property string $custom_flags Свои флаги админки
 * @property string $use_static_bantime
 */
class AdminsServers extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string имя таблицы
	 */
	public function tableName()
	{
		return '{{admins_servers}}';
	}

	public static function getFlags() {

		return str_split($this->custom_flags);
	}

	public function setFlags(Array $value) {

		$this->custom_flags = implode('', $value);
	}

	/**
	 * @return правила проверки для полей
	 */
	public function rules()
	{
		return array(
			//array('custom_flags', 'required'),
			array('admin_id, server_id', 'numerical', 'integerOnly'=>true),
			array('custom_flags', 'length', 'max'=>32),
			array('use_static_bantime', 'in', 'range' => array('yes', 'no')),
			array('admin_id, server_id, custom_flags, use_static_bantime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array реляции
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array Имена полей (поле=>имя)
	 */
	public function attributeLabels()
	{
		return array(
			'admin_id' => 'Админ',
			'server_id' => 'Сервер',
			'custom_flags' => 'Дополнительные флаги',
			'use_static_bantime' => 'Использовать установленные сроки бана',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('admin_id',$this->admin_id);
		$criteria->compare('server_id',$this->server_id);
		$criteria->compare('custom_flags',$this->custom_flags,true);
		$criteria->compare('use_static_bantime',$this->use_static_bantime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

}