<?php

$schema = array (
  'user' => 
  array (
    'id' => 
    array (
      'table' => 'user',
      'field' => 'id',
      'show' => 0,
      'filter' => 'toString(*)',
    ),
    'email' => 
    array (
      'table' => 'user',
      'field' => 'email',
      'show' => 1,
      'filter' => 'toLink(*,*,\'mailto\')',
    ),
    'password' => 
    array (
      'table' => 'user',
      'field' => 'password',
      'show' => 1,
      'filter' => 'toString(*)',
    ),
  ),
);