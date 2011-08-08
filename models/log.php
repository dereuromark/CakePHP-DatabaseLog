<?php
class Log extends DatabaseLoggerAppModel {
	var $name = 'Log';
	var $displayField = 'type';
	var $searchFields = array('Log.type','Log.message','Log.created','Log.id');
}
