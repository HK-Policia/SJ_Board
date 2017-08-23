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
		
		if($_GET['rp_con_id'] == 0){ //글삭제 부분
			include ("../../SJ_Board/include/dbconn.php");//서버접속정보
			
			$query = 'select * from sjb_all_forum where id="'.$id.'"';
			$result = $db->query( $query );
			$row = $result->fetch_assoc();
			
			$pw = $row['password'];
			
			if(isset($_POST['pw_chk'])){
				$pw_chk = md5($_POST['pw_chk']);
				
				if($pw == $pw_chk){					
				    $query = 'delete from sjb_all_forum where id ='.$id; // 원글 삭제
				    $db->query( $query );
					$query = 'delete from sjb_all_reply where contentid ='.$id; // 게시글안에 댓글 모두삭제
				    $db->query( $query );
					
					echo '<meta charset="utf-8">
						  <script type="text/javascript">
						    opener.alert("삭제되었습니다.");
							opener.location.href="'.$domain.'/?page='.$pagenum.'";
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
		}else{ //댓글 삭제부분
			$rp_con_id = $_GET['rp_con_id'];
			
			include ("../../SJ_Board/include/dbconn.php");//서버접속정보
			
			$query = 'select * from sjb_all_reply where id="'.$id.'"';
			$result = $db->query( $query );
			$row = $result->fetch_assoc();
			
			$pw = $row['password'];
			if(isset($_POST['pw_chk'])){
				$pw_chk = md5($_POST['pw_chk']);
				if($pw == $pw_chk){					
				    $query = 'select * from sjb_all_reply where re_reply_chk="'.$id.'"';
					$result = $db->query( $query );
					$result_num = $result->num_rows;
					
					if ( $result_num == 0 ) { //대댓글이 없다면 바로삭제 있다면 업데이트로 내용삭제
						$query = 'delete from sjb_all_reply where id ='.$id;
				    	$db->query( $query );
					}else {
						$query = 'update sjb_all_reply set Nickname="삭제", comment="",hold="D" where id ='.$id;
				    	$db->query( $query );
					}
					
					echo '<meta charset="utf-8">
						  <script type="text/javascript">
						    opener.alert("삭제되었습니다.");
							opener.location.href="'.$domain.'/?page='.$pagenum.'&contents='.$rp_con_id.'";
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
		}
		
		
		?>
		<div style="width:200px;height:90px;margin: 5px auto;">
			<div style="text-align: center;width:99%;color: white;background-color: #88afff;border-radius: 5px;">
				삭제 비밀번호입력
			</div>
			<form method="post">
				<div style="margin-top: 10px;">
					<input type="password" name="pw_chk" style="width: 99%">
					<div style="width: 99%; margin-top: 5px;text-align: center;">
						<input type="submit" value="삭제">
						<input type="submit" value="취소" onclick="window.close();">
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
