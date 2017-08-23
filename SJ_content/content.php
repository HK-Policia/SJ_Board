<link rel="stylesheet" type="text/css" href="SJ_Board/board_css/sjb.css" />
<?php
/////////////////////////////////////////////// 댓글 작성
function reply_write()
{	
	if ( isset($_POST['rep']) ) {
		$nick = $_POST['rp_nick'];
		$rp_pw = md5($_POST['rp_pw']);
		$comment = $_POST['rep'];
		$contentid = $_POST['idrep'];
		$replyid = $_POST['replyid'];
		$ip = ip2long($_SERVER['REMOTE_ADDR']);
		
		if ( !empty($_FILES['upload']['tmp_name']) || !empty($_POST['rep']) ){
		}else{ // 내용이 없을경우 추방
			echo '<meta charset="utf-8">
				  <script type="text/javascript">
				    alert("댓글 내용이 없습니다. 내용을 입력해주세요");
				  	location.href="'.$_SERVER['HTTP_REFERER'].'";
				  </script>';
			exit;
		}
		
		if ( empty($_FILES['upload']['tmp_name']) ){
			$target_file = '';
		}else{
			$target_dir = "SJ_content/rp_upload/";
			$imageFileType = pathinfo($_FILES["upload"]["name"],PATHINFO_EXTENSION);
			$filename =  iconv("UTF-8","EUC-KR", date("YmdHis")."_SJBOARD_".$ip.".".$imageFileType);
			$target_file = $target_dir . $filename;
			$uploadOk = 1;
			
			// Check if image file is a actual image or fake image
			$check = getimagesize($_FILES["upload"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$uploadOk = 0;
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
					    alert("이미지 파일이 아닙니다.");
					  	location.href="'.$_SERVER['HTTP_REFERER'].'";
					  </script>';
				exit;
			}
			
			// Check file size
			if ($_FILES["upload"]["size"] > 500000) { //파일 크기 지정
				$uploadOk = 0;
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
					    alert("죄송합니다. 500kb이상의 사진은 업로드 할수없습니다.");
					  	location.href="'.$_SERVER['HTTP_REFERER'].'";
					  </script>';
				exit;
			}
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			    $uploadOk = 0;
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
					    alert("죄송합니다. 사진 업로드는 JPG, JPEG, PNG , GIF 의 확장자 파일만 가능합니다.");
					  	location.href="'.$_SERVER['HTTP_REFERER'].'";
					  </script>';
				exit;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				echo '<meta charset="utf-8">
					  <script type="text/javascript">
					    alert("죄송합니다. 파일이 업로드되지 않았습니다.");
					  	location.href="'.$_SERVER['HTTP_REFERER'].'";
					  </script>';
				exit;
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
			        echo "The file ". basename( $_FILES["upload"]["name"]). " has been uploaded.";
			    } else {
			    	echo '<meta charset="utf-8">
						  <script type="text/javascript">
						    alert("죄송합니다. 파일을 업로드하는 중 오류가 발생했습니다.");
						  	location.href="'.$_SERVER['HTTP_REFERER'].'";
						  </script>';
					exit;
			    }
			}
			
			$target_file = $target_dir . iconv("EUC-KR","UTF-8", $filename);
		}
		
		include ("SJ_Board/include/dbconn.php");//서버접속정보
		
		$query = 'select * from sjb_all_forum where id="'.$contentid.'"';
		$result = $db->query( $query );
		$row = $result->fetch_assoc();
			
		$query = 'insert into sjb_all_reply set contentid="'. $contentid .'",
												re_reply_chk="'.$replyid.'", 
												date="' . date('Y-m-d H:i:s') . '",
												Nickname="'.$nick.'",
												password="'.$rp_pw.'",
												ip="'.$ip.'",
												comment=?,
												rp_img="'.$target_file.'"';
		
		$smt = $db->stmt_init();  //MySQL 실행준비를 위한 초기화
		if ( !$smt->prepare( $query ) ) {
			echo $smt->error . ' prepare error'; // 문제가 있을경우 출력할 문자열
			exit;
		}
		if( !$smt->bind_param('s', $comment) ) { // s 는 문자열이라는 것을 의미 
			//ㄴ 쿼리문이든 어떤 문자를 입력해도 그 값이 쿼리문이 아닌 문자열로 인식하게함
			echo 'binding error';
			exit;
		}
		if ( !$smt->execute() ) { // 에러
			echo 'execute error';
			exit;
		}
		$db->query( $query );
		
		$query = 'select MAX(id) from sjb_all_reply';
		$result = $db->query( $query );
		$last_id = $result->fetch_assoc();
		
		$smt->close();
		$db->close();
		
		header("Location: ".$_SERVER['HTTP_REFERER']."#rp_nav_".$last_id['MAX(id)']); // 이전페이지로 돌아가기
		exit;
	}
}

$reply_write = reply_write();

if( empty($_GET['contents']) || !is_numeric($_GET['contents']))
	$_GET['contents'] = 1;
if( empty($_GET['page']) || !is_numeric($_GET['page'])){
	$_GET['page'] = 1;
}else{
	$page = $_GET['page'];
}
include ("SJ_Board/include/dbconn.php");//서버접속정보

$query3 = 'select * from sjb_all_forum where id='.$_GET['contents'];
$result3 = $db->query( $query3 );

$row = $result3->fetch_assoc();
$result_num = $result3->num_rows;

$userip = ip2long($_SERVER['REMOTE_ADDR']);
$forumip = $row['ip'];

if( $userip != $forumip ){
	//조회수 부분
	$bNo = $row['id'];
	if(!empty($bNo) && empty($_COOKIE['SJ_board_' . $bNo])) { //해당 쿠키가 없으면 조회수 증가 쿼리 작동
		$sql = 'update sjb_all_forum set hits = hits + 1 where id = ' . $bNo;
		$result = $db->query($sql); 
		if(empty($result)) {
		?>
			<script>
				alert('DB접속 오류가 발생했습니다.');
			</script>
		<?php 
		} else { // 중복방지 쿠키 생성!
			setcookie('SJ_board_' . $bNo, TRUE, time() + (60 * 60 * 24), '/'); //쿠키생성은 html head이전에 선언되거나 echo문 이전에 선언되어야함
		}
	}
}
?>
<script type="text/JavaScript" src="SJ_Board/board_js/content.js" charset="utf-8"></script>
<script type="text/javascript" src="SJ_Board/board_js/jquery-2.1.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="SJ_Board/board_css/content.css" />
<table align="center" cellpadding="0" cellspacing="0" class="content_book">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr class="booktr">
					<?php
					if( $result_num == 0 ){ //결과 값이 없을경우. 이경우 없는 게시물을 들어왔을 때에 해당됨
						echo '<meta charset="utf-8">
				       		  <script type="text/javascript">
				       				alert("없는 게시물입니다.");
									location.href="'.$domain.'";
				       		  </script>';
						echo '<p align="center">존재하지 않는 게시물 입니다.</p><BR>';
						exit;
					}
					
					$reloadid = $row['id'];
					
					$forum_user_ip = long2ip($forumip);
					$forum_user_ip_nums = explode(".", $forum_user_ip); 
					$forum_user_ip_nums[2] = str_repeat("*", strlen($forum_user_ip_nums[2])); 
					$forum_user_ip_nums[3] = str_repeat("*", strlen($forum_user_ip_nums[3])); 
					$forum_user_ip_all = $forum_user_ip_nums[0].".".$forum_user_ip_nums[1].".".$forum_user_ip_nums[2].".".$forum_user_ip_nums[3];
					?>
					<td colspan="5" class="booktd m_hide" style="text-align: right;padding: 5px 5px 5px 0px;"> <!-- 글수정 / 글삭제 부분 -->
						<a href="javascript:void(0)" onclick="editPop('<?php echo $row['id'] ?>','<?php echo $page ?>','0')"><button class="de_ed_bt">수정</button></a>
						<a href="javascript:void(0)" onclick="deletePop('<?php echo $row['id'] ?>','<?php echo $page ?>','0')"><button class="de_ed_bt">삭제</button></a>
					</td>
				</tr>
				<!-- 내용 타이틀 정보 시작 -->
				<tr align="center" class="m_hide">
					<th class="line" style="width: 5%;">번호</th>
					<th class="line" style="width: 70%;">제목</th>
					<th class="line" style="width: 13%;">작성자</th>
					<th class="line" style="width: 7%;">작성일</th>
					<th class="line" style="width: 5%;">조회</th>
				</tr>
				<tr class="m_hide">
					<td class="booktd">
						<?php echo $row['id']; ?>
					</td>
				    <td class="booktd">
				    	<div class="title">
				    		<B><?php echo '['.$row['kind'].'] '.nl2br(htmlspecialchars($row['title'],ENT_QUOTES,'utf-8')); ?></B>
				    	</div>
				    	<script>
							document.title = '에스제이보드 - <?php echo nl2br(htmlspecialchars($row['title'],ENT_QUOTES,'utf-8')) ?>';
						</script>
						<meta property="og:title" content="<?php echo nl2br(htmlspecialchars($row['title'],ENT_QUOTES,'utf-8')) ?>">
				    </td>
				    <td class="booktd">
				    	<?php echo $row['Nickname']; ?>
					</td>
				    <td class="booktd">
				    	<?php echo $row['date']; ?>
				    </td>
					<td class="booktd">
						<?php echo $row['hits']; ?>
					</td>
				</tr>
				<!-- 내용 타이틀 정보 끝 -->
				<!-- 게시판 내용정보 시작 -->
				<tr>
					<td colspan="5" style="border-bottom: 1px solid #c4c4c4;">
						<div class="content2"><!-- 글내용 부분  -->
							<div style="padding: 10px">
								<?php echo html_entity_decode(stripslashes($row['content']));
								
								if(preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", html_entity_decode(stripslashes($row['content'])), $matches)){
									$og_image = 'http://'.$_SERVER["HTTP_HOST"].'/'.$matches[1][0];
								}
								?>
								<meta property="og:image" content="<?php print_r($og_image) ?>"><!-- url링크시 보여줄 이미지 설정  -->
							</div>
						</div>
						<div class="ed_del_bt"><!-- 수정 삭제 버튼  -->
							<a href="javascript:void(0)" onclick="editPop('<?php echo $row['id'] ?>','<?php echo $page ?>','0')"><button class="de_ed_bt">수정</button></a>
							<a href="javascript:void(0)" onclick="deletePop('<?php echo $row['id'] ?>','<?php echo $page ?>','0')"><button class="de_ed_bt">삭제</button></a>
						</div>
						<div class="list_report_bt">
							<form method="post" style="display:inline"> <!-- 신고 목록 버튼  -->
								<input type="hidden" name="report" value="<?php echo $row['id']; ?>">
								<input type="hidden" name="reloadid" value="<?php echo $reloadid ?>">
								<button class="de_ed_bt" style="color: #b80000" onclick="javascript:popup(this.form);">신고</button>
							</form>
							<a href='<?php echo $domain ?>/?page=<?php echo $page;?>'>
								<button class="de_ed_bt">목록</button>
							</a>
						</div>					
					</td>
				</tr>
				<!-- 게시판 내용정보 끝 -->
				<tr>
					<td colspan="5" id="con_rp_auto" style="border-bottom: 1px solid #c4c4c4;">
						<?php
						if( empty($_GET['rep']) || !is_numeric($_GET['rep']) )
							$_GET['rep'] = 1;
						
						$id = $row['id'];
						
						$query = 'select * from sjb_all_reply where contentid="'.$id.'" and re_reply_chk=0 order by id ASC';
						$result = $db->query( $query );
						$result_num = $result->num_rows; // $변수->행의 갯수 (num_rows 행의 갯수)
						
						if ( !empty($result_num) )
						{
							// 댓글 출력 시작 부분
							for ( $k = 0; $k < $result_num; ++$k ) {
								$row = $result->fetch_assoc(); // 하나의 행을 가져와서 배열을 만들어줌 // 호출될 때 마다 다음행 을 호출함 
												
								$rp_user_color = '';
								if( ($row['ip'] == $forumip) && $row['hold'] != 'N' ){ //댓글이 글작성자 일경우!
									$rp_user_color = 'style="background-color: #E7E1D7"';
								}
								
								$rp_user_ip = long2ip($row['ip']);
								$rp_user_ip_nums = explode(".", $rp_user_ip); 
								$rp_user_ip_nums[2] = str_repeat("*", strlen($rp_user_ip_nums[2])); 
								$rp_user_ip_nums[3] = str_repeat("*", strlen($rp_user_ip_nums[3])); 
								$rp_user_ip_all = $rp_user_ip_nums[0].".".$rp_user_ip_nums[1].".".$rp_user_ip_nums[2].".".$rp_user_ip_nums[3];
								?>
							<table cellpadding="0" cellspacing="0" class="reply" <?php if($row['hold'] == 'N'){ echo 'bgcolor="pink"';} ?>>
								<tr>
									<td>
										<div id="rp_nav_<?php echo $row['id'] ?>" class="reply_1" <?php echo $rp_user_color ?>><div style="padding : 15px 15px 0px 15px;"><!-- 댓글 전체를 감싸는 div -->
										<?php if( $row['hold'] == 'D' ){ //삭제된 댓글 표시안하기 ?>
										<div style="float: left;width: 100%;padding: 0px 0px 10px 0px;color: #686868;">
											[삭제된 코맨트 입니다]
										</div>
										<?php }else{ ?>
										<div style="float: left;width: 100%;">
												<b><?php echo $row['Nickname'] ?></b>
												<span class="rp_date">(<?php echo $row['date'] ?>)</span> 
												<span class="rp_date"><?php echo $rp_user_ip_all ?></span>
											<div style="float: right;">
												<a href="javascript:void(0)" onclick="deletePop('<?php echo $row['id'] ?>','<?php echo $page ?>','<?php echo $_GET['contents']; ?>')">
													<input type="image" src="SJ_Board/board_img/message.gif" border="0">
												</a>
											</div>
										</div>
										<?php if( $row['hold'] == 'Y') { //-----------댓글차단시 내용 숨기기 ?>
											<div class="reply_con">
												<span class="rp_hold">[차단된 댓글입니다]</span>
												<a class="hide" onclick="hold<?php echo $k ?>.style.display=(hold<?php echo $k ?>.style.display=='none')?'block':'none';" href="javascript:void(0)">
													[내용보기]
												</a>
												<div id="hold<?php echo $k ?>" style="DISPLAY: none"> <!-- 차단댓글 숨기기 활성화 -->
										<?php } ?>
												<div class="reply_con">
													<?php if($row['rp_img']!=''){ //댓글이미지 출력부분
														echo '<img src="'.$row['rp_img'].'" class="reply_img" /><BR>';
													} ?>
													<?php echo nl2br(htmlspecialchars($row['comment'],ENT_QUOTES,'utf-8')) ?>
												</div>
										<?php if( $row['hold'] == 'Y') { //-----------댓글차단시 내용 숨기기 ?>
												</div>
											</div>
										<?php } ?>
										<?php }//삭제된 댓글 표시안하기_else문 닫기 ?>
										<div style="float: left;width: 100%;padding-bottom: 15px;">
										<?php 
										$query3 = 'select * from sjb_all_reply where re_reply_chk='.$row['id'].'
													ORDER BY id ASC'; //순서확인 ASC 값이 낮은순으로 정렬
										$result3 = $db->query( $query3 );
										$result_num3 = $result3->num_rows; // $변수->행의 갯수 (num_rows 행의 갯수)
										?>
											<div style="float: right;">
												<a class="menu" onclick="rereply_ad('<?php echo $k ?>','<?php echo $id ?>','<?php echo $row['id'] ?>')" href="javascript:void(0)">
													<button class="rp_good_bt" style="color: black;">
														댓글
													</button>
												</a>
											</div>
										</div>
										</div></div><!-- 댓글 전체를 감싸는 div -->
										<div style="float: left;width: 100%;"><!---------------- 대댓글 출력부분  ------------------->
											<?php
											if( empty($result_num3) ){
											}else {
												for ( $k2 = 0; $k2 < $result_num3; ++$k2 ) {
													$row3 = $result3->fetch_assoc();
													
													$re_rp_user_color = '';
													if( $row3['ip'] == $forumip ){ //댓글이 글작성자 일경우!
														$re_rp_user_color = 'style="background-color: #E7E1D7"';
													}
													
													$rprp_user_ip = long2ip($row3['ip']);
													$rprp_user_ip_nums = explode(".", $rprp_user_ip); 
													$rprp_user_ip_nums[2] = str_repeat("*", strlen($rprp_user_ip_nums[2])); 
													$rprp_user_ip_nums[3] = str_repeat("*", strlen($rprp_user_ip_nums[3])); 
													$rprp_user_ip_all = $rprp_user_ip_nums[0].".".$rprp_user_ip_nums[1].".".$rprp_user_ip_nums[2].".".$rprp_user_ip_nums[3];
												?>
												<div class="reply_2"><!-- 대댓글 전체를 감싸는 div -->
													<div class="reply_2_1">
														<img src="SJ_Board/board_img/rere_icon.png" />
													</div>
													<div id="rp_nav_<?php echo $row3['id'] ?>" class="reply_2_2" <?php echo $re_rp_user_color ?>>
															<b><?php echo $row3['Nickname'] ?></b>
															<span class="rp_date">(<?php echo $row3['date'] ?>)</span> 
															<span class="rp_date"><?php echo $rprp_user_ip_all ?></span>
														<div style="float: right;margin-right: 15px;">
															<a href="javascript:void(0)" onclick="deletePop('<?php echo $row3['id'] ?>','<?php echo $page ?>','<?php echo $_GET['contents']; ?>')">
																<input type="image" src="SJ_Board/board_img/message.gif" border="0">
															</a>
														</div>
													<?php if( $row3['hold'] == 'Y') { //-----------댓글차단시 내용 숨기기 ?>
														<div class="re_reply_con">
															<span class="rp_hold">[차단된 댓글입니다]</span>
															<a class="hide" onclick="hold2<?php echo $k2 ?>.style.display=(hold2<?php echo $k2 ?>.style.display=='none')?'block':'none';" href="javascript:void(0)">
																[내용보기]
															</a>
															<div id="hold2<?php echo $k2 ?>" style="DISPLAY: none"> <!-- 차단댓글 숨기기 활성화 -->
															<?php } ?>
															<div class="re_reply_con">
																<?php if($row3['rp_img']!=''){ //댓글이미지 출력부분
																	echo '<img src="'.$row3['rp_img'].'" class="re_reply_img"/><BR>';
																} ?>
																<?php echo nl2br(htmlspecialchars($row3['comment'],ENT_QUOTES,'utf-8')) ?>
															</div>
													<?php if( $row3['hold'] == 'Y') { //-----------댓글차단시 내용 숨기기 ?>
															</div>
														</div>
													<?php } ?>
													</div>
												</div><!-- 댓글 전체를 감싸는 div -->
												<?php
												}
											}
											?>
										</div>
										<!-- 대댓글 입력부분  시작 -->
										<div style="float: left;width: 100%;" id="rprp<?php echo $k ?>" class="re_reply_ad">
										</div>
										<!-- 대댓글 입력부분  끝-->
									</td>
								</tr>
							</table>
							<?php }
						}else {	?>
							<table cellpadding="0" cellspacing="0" class="reply" style="padding: 15px;">
								<tr>
									<td>
										댓글이 없습니다아아아아.
									</td>
								</tr>
							</table>
						<?php }	?>
					</td>
				</tr>
				<tr>
					<td colspan="5" style="padding: 10px 0px 10px 0px">
						<!-- 댓글 입력 시작 -->
						<div style="width: 80%; margin: auto">
							<form method="post" enctype="multipart/form-data">
								<div style="padding-bottom: 5px;">
									닉네임<input type="text" id="rp_nick" name="rp_nick">
									비밀번호<input type="password" id="rp_pw" name="rp_pw">
								</div>
								<textarea rows="4" id="rep" name="rep" class="rp"></textarea>
								<input type="hidden" name="idrep" value="<?php echo $id;?>" /> <!--현재글 번호-->
								<input type="hidden" name="replyid" value="0" />
								<span style="float: left;">
									<img src="<?php echo $domain ?>/SJ_Board/board_img/imgs.png" />댓글 이미지 첨부
									<input type="file" id="file_rp" name="upload" id="upload">
								</span>
								<span style="float: right;">
									<div class="rp_imgup_rp_bt">
										<input class="rp_bt" type="submit" id="rp_submit_chk" value="댓글입력" onClick="return rp_submit()">
										<img id="loading" src="<?php echo $domain ?>/SJ_Board/board_img/loading.gif" style="width: 20px;height: 20px;border: 0px;display: none">
									</div>
								</span>
							</form>
						</div>
						<!-- 댓글 입력 끝 -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<BR>
<script>autolink('con_rp_auto');
if(location.hash){
    var rp_id = location.hash.replace('#','');
    var rp_nav = document.getElementById(rp_id);
    rp_nav.style.border = '2px solid red';
	rp_nav.style.margin = '-2px';
}
</script>