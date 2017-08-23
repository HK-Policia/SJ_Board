<!DOCTYPE html>
<html lang="kr">
	<head>
		<meta charset="utf-8">		
		<title>에스제이보드 - 게시판</title>
	</head>
	<body>
		<!-- 게시판 출력 소스입니다 삭제시 출력 안됩니다 중요! -->
		<?php 
		ob_start();

        $domain = 'http://127.0.0.4'; //자신의 도메인주소를 입력하세요
		$og_image = 'http://yeah.pe.hu/images/logo.jpg'; //게시판글 미리보기시 게시글에 이미지가없으면 보여줄 이미지선택
		$page_num = 20; //한페이지당 보여줄 글갯수
		$page_list_num = 10; //글 리스트의 블럭갯수 (리스트 갯수 입력)
		
		if(isset($_GET['write'])){
			 include('SJ_write/write.php');
		}else if(isset($_GET['edit'])){
			 include('SJ_content/edit.php'); 
		}else{
			 if(isset($_GET['contents'])){
			 	 include('SJ_content/content.php'); 
			 } include('sj_board.php'); 
		} ?>
	</body>
</html>