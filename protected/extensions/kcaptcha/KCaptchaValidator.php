<?php

class KCaptchaValidator extends CValidator {

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel the object being validated
	 * @param string the attribute being validated
	 */
	protected function validateAttribute($object, $attribute) {

		if($this->testCode($object->$attribute)) {
			$message = $this->message !== null ? $this->message : Yii::t('yii', 'The verification code is incorrect.');
			$this->addError($object, $attribute, $message);
		}
	}

	public function testCode($code) {

		Yii::app()->session->open();

		if(empty($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !== $code) {
			return FALSE;
		}

		return TRUE;
	}
}