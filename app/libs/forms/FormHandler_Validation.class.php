<?php

namespace libs\forms;

class FormHandler_Validation {
	public static function Email($key) {
		if(filter_var($_POST[$key],FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}

	public static function URL($key) {
		return filter_var($_POST[$key], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
	}
}