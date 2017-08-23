<?php  
	date_default_timezone_set("Asia/Seoul"); //시간설정
  
	@$db = new mysqli('127.0.0.1','root','','sj_board');
	
	if ( $db->connect_errno ) {
		echo 'DB Connection Fail';
		exit;
	}
  
	$query = 'set names utf8';
	$db->query( $query );  
?>
