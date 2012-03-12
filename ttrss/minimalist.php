<?php
require_once "../include/database.php";

function safe_param($param)
{
	$param=str_replace("'", "&#39;", $param);
	$param=str_replace('"', "&#34;", $param);
	$param=str_replace('*', "&#42;", $param);
	$param=str_replace('/', "&#47;", $param);
	$param=str_replace('\\', '&#92;', $param);
	return $param;
}
function de_escape($param)
{
	$param=str_replace("&#39;","'",  $param);
	$param=str_replace("&#34;",'"',  $param);
	$param=str_replace( "&#42;",'*', $param);
	$param=str_replace("&#47;",'/',  $param);
	$param=str_replace('&#92;','\\',  $param);
	return $param;

}
function updatefield($tablename,$id,$field,$value)
{
	if(!$tablename){return 0;}
	if(!$id){return 0;}
	if(!$field){return 0;}
	global $database;
	$value=safe_param($value);
	$sql="update $tablename set $field='$value' where id='$id'";
	$result=$database->query($sql);
	return $result;
}
?>
