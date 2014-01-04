<?php

class KCaptchaAction extends CAction
{
    /**
     * @var array
     */
    public $settings = array();

    public function run()
    {
        require_once __DIR__ . '/kcaptcha/index.php';

		Yii::app()->end();
    }
}
