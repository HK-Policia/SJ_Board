<!DOCTYPE HTML>
<html>
	<head>
		<title>SJ_board - 삭제</title>
	</head>
	<body topmargin=0 leftmargin=0>
		<?php
		include('../../SJ_Board/include/globalset.php');
		$id = $_GET['id'];
		$pagenum = 1;
		if(isset($_GET['page'])){$pagenum = $_GET['page'];}
		
		include ("../../SJ_Board/include/dbconn.php");//서버접속정보
		
		$query = 'select * from sjb_all_forum where id="'.$id.'"';
		$result = $db->query( $query );
		$row = $result->fetch_assoc();
		
		$pw = $row['password'];
		if(isset($_POST['pw_chk'])){
			$pw_chk = md5($_POST['pw_chk']);
			if($pw == $pw_chk){				
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
						opener.location.href="'.$domain.'/?edit='.$pw_chk.'&page='.$pagenum.'&content='.$id.'";
					  	window.close();
					  </script>';
				exit;
			}else{
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
					    opener.alert("비밀번호가 틀렸습니다.");
					  	location.href="'.$_SERVER['HTTP_REFERER'].'";
					  </script>';
				exit;
			}
		}
		?>
		<div style="width:200px;height:90px;margin: 5px auto;">
			<div style="text-align: center;width:99%;color: white;background-color: #88afff;border-radius: 5px;">
				수정 비밀번호입력
			</div>
			<form method="post">
				<div style="margin-top: 10px;">
					<input type="password" name="pw_chk" style="width: 99%">
					<div style="width: 99%; margin-top: 5px;text-align: center;">
						<input type="submit" value="확인">
						<input type="submit" value="취소" onclick="window.close();">
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
