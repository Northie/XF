<?php

namespace libs\models;

interface iModel {
	public function create($data);
	public function update($id,$col='id');
	public function read($where=false,$order=false);
	public function destroy($id,$col='id');
	public function getMany($mode=0);
	public function getOne($mode=1);
	public function set($data);
}
