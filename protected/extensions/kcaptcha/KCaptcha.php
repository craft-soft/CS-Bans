<?php
/**
 * KCaptcha for Yii.
 *
 * @author Kapman
 * @package application.extensions.kcaptcha
 * @since 2.0
 */

class KCaptcha extends CCaptcha {

	/**
	 * Renders the CAPTCHA image.
	 */
	protected function renderImage()
	{
		if(!isset($this->imageOptions['id']))
			$this->imageOptions['id']=$this->getId();

		$url=$this->getController()->createUrl($this->captchaAction, array(session_name() => session_id(), 'v' => uniqid()));
		$alt=isset($this->imageOptions['alt'])?$this->imageOptions['alt']:'';
		echo CHtml::image($url,$alt,$this->imageOptions);
	}
}