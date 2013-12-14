<?php
/**
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

/**
 * Модель таблицы Причин в группе
 * @property integer $id ID записи
 * @property integer $setid ID группы
 * @property integer $reasonid ID причины
 */
class ReasonsToSet extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{reasons_to_set}}';
	}

	public function rules()
	{
		return array(
			array('setid, reasonid', 'required'),
			array('setid, reasonid', 'numerical', 'integerOnly'=>true),
			array('id, setid, reasonid', 'safe', 'on'=>'search'),
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
			'setid' => 'Группа',
			'reasonid' => 'Причина',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('setid',$this->setid);
		$criteria->compare('reasonid',$this->reasonid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}