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
 * Модель для таблицы "{{reasons}}".
 *
 * Доступные поля таблицы '{{reasons}}':
 * @property integer $id ID причины
 * @property string $reason Причина
 * @property integer $static_bantime Срок бана
 */
class Reasons extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{reasons}}';
	}

	public function rules()
	{
		return array(
			array('static_bantime', 'numerical', 'integerOnly'=>true),
			array('reason', 'length', 'max'=>100),
			array('reason', 'required'),
			array('id, reason, static_bantime', 'safe', 'on'=>'search'),
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
			'reason' => 'Причина',
			'static_bantime' => 'Срок бана',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('static_bantime',$this->static_bantime);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getList($addban = TRUE) {
		$reasons = self::model()->findAll();

		$list = array();
		foreach($reasons AS $reason) {
			$list[$addban ? $reason->reason : $reason->id] = $reason->reason;
		}

		if($addban)
			$list['selfreason'] = 'Другая причина';

		return $list;
	}
	
	public function afterSave() {
		if($this->isNewRecord)
			Syslog::add(Logs::LOG_ADDED, 'Добавлена причина банов <strong>' . $this->reason . '</strong>');
		else
			Syslog::add(Logs::LOG_EDITED, 'Изменена причина банов <strong>' . $this->reason . '</strong>');
		return parent::afterSave();
	}
	
	public function afterDelete() {
		Syslog::add(Logs::LOG_DELETED, 'Удалена причина банов <strong>' . $this->reason . '</strong>');
		return parent::afterDelete();
	}

}