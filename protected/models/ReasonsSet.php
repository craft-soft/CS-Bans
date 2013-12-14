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
 * Модель для таблицы "{{reasons_set}}".
 *
 * Доступные поля таблицы '{{reasons_set}}':
 * @property integer $id ID группы
 * @property string $setname Имя группы
 * @property string $setname Причины
 */
class ReasonsSet extends CActiveRecord
{
	public $reasons = array();


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{reasons_set}}';
	}

	public function rules()
	{
		return array(
			array('setname', 'length', 'max'=>32),
			array('setname', 'required'),
			array('reasons', 'safe'),
			array('id, setname', 'safe', 'on'=>'search'),
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
			'setname' => 'Название',
			'reasons' => 'Причины банов'
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('setname',$this->setname,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getList() {
		$model = self::model()->findAll();

		$list = array();
		foreach($model AS $item) {
			$list[$item->id] = $item->setname;
		}

		return $list;
	}
	
	public function afterSave() {
		parent::afterSave();
		if($this->isNewRecord)
		{
			if(!empty($this->reasons))
			{
				foreach($this->reasons as $r)
				{
					$rts = new ReasonsToSet;
					$rts->setid = intval($this->id);
					$rts->reasonid = intval($r);
					if($rts->save())
						$rts->unsetAttributes();
				}
				return TRUE;
			}
		}
		return TRUE;
	}
	
	public function beforeValidate() {
		parent::beforeValidate();
		
		if($this->isNewRecord)
		{
			if(empty($this->reasons))
				return $this->addError ('reasons', 'Выберите причины');
		}
		
		return TRUE;
	}

}