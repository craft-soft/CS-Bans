<?php
/**
 * Компонент, подгружающий конфиг из базы
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
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
