<link rel="stylesheet" type="text/css" href="SJ_Board/board_css/sjb.css" />
<?php
if( empty($_GET['page']) || !is_numeric($_GET['page'])) // is_numeric(값) 해당값이 숫자인지 아닌지 숫자면 true
//if 문에서 or(||) 이 실행되기 전에 왼쪽부터 확인후 왼쪽이 맞으면 우측 의 조건식은 실행되지 않음
$_GET['page'] = 1;

$output = ' ';

include ("SJ_Board/include/dbconn.php");

if( !empty($_GET['keyword']) ){
	$name = $_GET['name'];
	$keyword = $_GET['keyword'];
	
	$url_keyword = '&name='.$name.'&keyword='.$keyword;
	$query = 'select count(*) from sjb_all_forum where '.$name.' like "%'.$keyword.'%"'; //글 갯수 확인
}else{
	$url_keyword = '';
	$query = 'select count(*) from sjb_all_forum'; //글 갯수 확인
}

$result = $db->query ( $query );
if( empty($result) ){
	echo '에러 없는게시판입니다.';
	exit;
}
$row = $result->fetch_row();  // 내용이 벨류값으로 들어감
$totalnum = $row[0]; // 위에서 지정해준 값 키값 // 글의 총 갯수
  
$message_per_page = $page_num; // 한페이지당 보여주는 글의 갯수
//celi(값) : '값'이상인 정수 중에서 최소값을 구한다. floor(값) : ceil와 반대의 함수
$maxpagenum = ceil($totalnum / $message_per_page);  // 페이지 숫자의 최대 값
$minpagenum = 1;  // 페이지 숫자의 최소 값
$pagenum = $_GET['page'];  // 현재 페이지 번호
  
//-----------------------------페이지 넘버 부분 --------------------------------------

if( empty($_GET['list'])){
	$list = $message_per_page;
}else{
	$list = ($_GET['list']) ? $_GET['list'] : $message_per_page; //page : default - 50
}

$b_pageNum_list = $page_list_num; //블럭에 나타낼 페이지 번호 갯수
$block = ceil($pagenum/$b_pageNum_list); //현재 리스트의 블럭 구하기
    	
$b_start_page = ( ($block - 1) * $b_pageNum_list ) + 1; //현재 블럭에서 시작페이지 번호
$b_end_page = $b_start_page + $b_pageNum_list - 1; //현재 블럭에서 마지막 페이지 번호

$total_page =  ceil($totalnum/$list); //총 페이지 수

if ($b_end_page > $total_page){
    $b_end_page = $total_page;}

if( $pagenum < $minpagenum ) // 페이지 숫자범위 지정
	$pagenum = $minpagenum;
if( $pagenum > $maxpagenum )
	$pagenum = $maxpagenum;

if( !empty($_GET['keyword']) ){
	$name = $_GET['name'];
	$keyword = $_GET['keyword'];
	
	$query = 'select * from sjb_all_forum where '.$name.' like "%'.$keyword.'%" order by id desc limit '. // 쿼리문 가져오기
			($pagenum-1)*$message_per_page . ',' . $message_per_page;
}else {
	$query = 'select * from sjb_all_forum order by id desc limit '. // 쿼리문 가져오기
			($pagenum-1)*$message_per_page . ',' . $message_per_page;
	
}

$result = $db->query( $query );
if( empty($result) ){
	$output .= '<tr>
					<td colspan="6" class="booktd">
						게시글이없습니다.
					</td>
				</tr>';
	$result_num = 0;
}else {
	$result_num = $result->num_rows; // $변수->행의 갯수 (num_rows 행의 갯수)
}

for ( $k = 0; $k < $result_num; ++$k ) {
	$row = $result->fetch_assoc(); // 하나의 행을 가져와서 배열을 만들어줌 // 호출될 때 마다 다음행 을 호출함
	$forum_id = $row['id'];
	
	$bg_color = '';
	$a_bold = '';
	$link = '<a href="'.$domain.'/?page='.$pagenum.'&contents='.$forum_id.$url_keyword.'" class="forum">';
	if(isset($_GET['contents'])){
		if( $_GET['contents'] == $forum_id ){ //게시판의 현재글 표시
			$forum_id = '▶';
			$bg_color = 'style="background-color: #d9d9d9;"';
			$a_bold = 'style="font-weight: bold;font-size: 14px;"';
			$link = '<a>';
		}
	}
	$query2 = 'select * from sjb_all_reply where contentid="'.$row['id'].'"';
	$result2 = $db->query( $query2 );
	$result_num2 = $result2->num_rows; // $변수->행의 갯수 (num_rows 행의 갯수)
	
	$countreply = $result_num2; //리플갯수 (대댓글포함)
		
	$title = nl2br(htmlspecialchars($row['title'],ENT_QUOTES,'utf-8'));
	
	if( $row['kind'] == 'anon' ){ //익명게시판 유저정보링크 가리기
		$user_link = '<a>';
	}
	
	$content = html_entity_decode(stripslashes($row['content']));
	include ("SJ_Board/include/content_chk.php"); //글안에 태그 검색
	
	$output .= '<tr class="forumtr" '.$bg_color.'>
					<td class="booktd">'.$forum_id.'</td>
					<td class="booktd" style="text-align: left;">
						<font class="select" '.$a_bold.'>
						'.$link.'['.$row['kind'].'] '.
							$bgm_chk.
							$title.'</a></font>'.
							$img_chk;
	$output .= '		<span class="reply_count">['.$countreply.']</span>
					</td>
					<td class="booktd" style="text-align: left;">'. $row['Nickname'].'</td>
					<td class="booktd">'. $row['date'] . '</td>
					<td class="booktd">'. $row['hits'] . '</td>
				</tr>';
}

$output .= '<tr>';
$output .= '<td align="center" colspan="6" style="padding: 5px 0px 5px 0px;">';
$prev_next = ' ';
if($block <=1){
}else{
	$prev = $b_start_page-1;
	$prev_next .= "<a href='$domain/?page=$prev&list=$list$url_keyword' class='select' title='이전 페이지'><span class='prev_next'><</span></a>";//이전 링크
}
 
for($j = $b_start_page; $j <=$b_end_page; ++$j){ //페이지 출력부분-------------------------
	if($pagenum == $j){
		$prev_next .= "<span class='prev_next now_page' title='$j 페이지'><b>$j</b></span>";
	}
	else{
		$prev_next .= "<a href='$domain/?page=$j&list=$list$url_keyword' class='select' title='$j 페이지'><span class='prev_next'>$j</span></a>";
	}
}
$total_block = ceil($total_page/$b_pageNum_list); //블럭의 총갯수
if($block >= $total_block){  //다음 링크 부분 -------------------------
}else{
	$next = $b_end_page+1;
	$prev_next .= "<a href='$domain/?page=$next&list=$list$url_keyword' class='select' title='다음 페이지'><span class='prev_next'>></span></a>";
}

$keyword_notice = '';
if( !empty($_GET['keyword']) ){
	$keyword_notice = '검색어 <B>'.$_GET['keyword'].'</B> 대한 검색결과 입니다.';
	$keyword_name = $_GET['name'];
}
?>
<div align="center">
	<?php echo $keyword_notice; ?>
</div>
<div class="main_table">
	<table cellpadding="0" cellspacing="0" class="book">
		<tr class="booktr">
			<th width="5%" class="bookth">
				번호
			</th>
			<th width="70%" class="bookth">
				제목
			</th>
			<th width="13%" class="bookth">
				작성자
			</th>
			<th width="7%" class="bookth">
				작성일
			</th>
			<th width="5%" class="bookth">
				조회
			</th>
		</tr>
		<?php echo $output ?>
		<?php echo $prev_next ?>
	</table>
	<div class="list_bt">
		<a href='?write=y'><button class="wt_list_bt">글쓰기</button></a>
		<a href='?page=<?php echo $pagenum;?>'><button class="wt_list_bt">목록</button></a>
	</div>
	<div align="center">
		<form method="get">
			<select id="name" name="name" style="vertical-align: middle; height: 21px;">
				<option value="title">제목</option>
				<option value="content">내용</option>
				<option value="Nickname">작성자</option>
			</select>
			<input type="text" name="keyword" size="15" style="vertical-align: middle;" value="<?php if(!empty($_GET['keyword'])){echo $_GET['keyword'];} ?>"><input type="image" src="SJ_Board/board_img/Search.png" border="0" style="vertical-align: middle;position: absolute; margin:3px 0px 0px -20px;">
		</form>
	</div>
</div>
<script type="text/javascript">
	var qq = document.getElementById("name").options.length;
	for(var i=0; i< qq ; ++i){
		if(document.getElementById("name").options[i].value == "<?php echo $keyword_name ?>"){
			document.getElementById("name").options[i].selected = true;
			break;
		}
	}
</script>