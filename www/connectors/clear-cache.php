<?php

$key = $_GET['key'];

if(preg_match('/^[0-9a-f]{40}$/i', $key)) {
	if(apc_exists($key)) {
		apc_delete();
	}
}

