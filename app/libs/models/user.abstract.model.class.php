<?php

/**
 *
 * This file is automatically generated from your database
 * This fill will be re-written each time the schema script is run
 * Please do not modify it.
 *
 * You may use the corresponding concrete class to overwrite methods
 *
 */

namespace libs\models;
use libs\pdo;

abstract class _user extends Model {

	public $resource = "user";

	protected function fieldVisibility() {
		$fields = array("show"=>array (
  0 => 'email',
  1 => 'password',
),"hide"=>array (
  0 => 'id',
));
		return $fields;
	}
}