<?php
/**
 * Компонент, подгружающий конфиг из базы
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

class DConfig extends CApplicationComponent
{
	protected $data = array();

    public function init()
    {
        $this->data = Webconfig::getCfg();
        parent::init();
    }

    public function get($key)
    {
		$key = CHtml::encode($key);
		if (isset($this->data[$key])){
			return $this->data[$key];
        } else {
            throw new CException('Undefined parameter '.$key);
        }
    }

	public function __get($name) {
		return $this->get($name);
	}
}

?>
