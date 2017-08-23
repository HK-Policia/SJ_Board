<?php
if(preg_match("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $match) == 0) {  //이미지있는지 감지하고 제목에 표시해줌
	$img_chk = '';
} else {  
	$img_chk = "<img src='$domain/SJ_Board/board_img/imgs.png' />";
}
if(preg_match("/<iframe[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $match) == 0) {  //동영상있는지 감지하고 제목에 표시해줌
	$bgm_chk = '';
} else {  
	$bgm_chk = "<img src='$domain/SJ_Board/board_img/bgm.png' /> ";
}
?>
