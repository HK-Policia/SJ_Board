function cancelok(){
	if(confirm("삭제하시겠습니까?"))
	{ alert("삭제되었습니다."); return true;	} else { return false; }
}

function rp_submit(){
	if(document.getElementById("file_rp").value == "" && document.getElementById("rep").value == ""){
		alert("댓글 내용이 없습니다. 내용을 입력해주세요");
		return false;
	}else if(document.getElementById("rp_nick").value == ""){
		alert("닉네임을 입력하여주세요.");
		return false;
	}else if(document.getElementById("rp_pw").value == ""){
		alert("비밀번호를 입력하여주세요");
		return false;
	}else{
		document.getElementById("rp_submit_chk").style.display = "none";
		document.getElementById("loading").style.display = "inline-block";
		return true;		
	}
}
function rp2_submit(num){
	var nu = num;
	if(document.getElementById("file_rp"+nu).value == "" && document.getElementById("rep"+nu).value == ""){
		alert("댓글 내용이 없습니다. 내용을 입력해주세요");
		return false;
	}else if(document.getElementById("re_rp_nick").value == ""){
		alert("닉네임을 입력하여주세요.");
		return false;
	}else if(document.getElementById("re_rp_pw").value == ""){
		alert("비밀번호를 입력하여주세요");
		return false;
	}else {
		document.getElementById("rp"+nu+"_submit_chk").style.display = "none";
		document.getElementById("loading"+nu).style.display = "inline-block";
		return true;
	}
}

function content_del(){
	if(confirm("해당 글을 정말로 삭제하시겠습니까? \n (원글,댓글이 모두 삭제됩니다.)"))
	{ alert("삭제가 완료 되었습니다."); return true; } else { return false;	}
}

function deletePop(id,page,rp_con_id){
	var popWidth  = '300'; // 파업사이즈 너비
	var popHeight = '100'; // 팝업사이즈 높이
	var winWidth  = document.body.clientWidth;  // 현재창의 너비
	var winHeight = window.screen.height; // 현재창의 높이
	var winX      = window.screenX || window.screenLeft || 0;// 현재창의 x좌표
	var winY      = window.screenY || window.screenTop || 0; // 현재창의 y좌표
	w = winX + (winWidth - popWidth) / 2;
	h = winY + (winHeight/2) - (popHeight/2);
	
	window.name = "forum_content";
	O = "left="+w+",top="+h+",width="+popWidth+",height="+popHeight+",scrollbars=no,resizable=no";
	var url= 'SJ_content/pw_chk/delete_chk.php?id='+id+'&page='+page+'&rp_con_id='+rp_con_id;
	imgWin=window.open(url,"YEAH-viewer",O);
}
function editPop(id,page,rp_con_id){
	var popWidth  = '300'; // 파업사이즈 너비
	var popHeight = '100'; // 팝업사이즈 높이
	var winWidth  = document.body.clientWidth;  // 현재창의 너비
	var winHeight = window.screen.height; // 현재창의 높이
	var winX      = window.screenX || window.screenLeft || 0;// 현재창의 x좌표
	var winY      = window.screenY || window.screenTop || 0; // 현재창의 y좌표
	w = winX + (winWidth - popWidth) / 2;
	h = winY + (winHeight/2) - (popHeight/2);
		
	window.name = "forum_content";
	O = "left="+w+",top="+h+",width="+popWidth+",height="+popHeight+",scrollbars=no,resizable=no";
	var url= 'SJ_content/pw_chk/edit_chk.php?id='+id+'&page='+page;
	imgWin=window.open(url,"YEAH-viewer",O);
}

function doImgPop(img){
	img1= new Image();
	img1.src=(img);
	imgControll(img);
}
function imgControll(img){
	if((img1.width!=0)&&(img1.height!=0)){
		viewImage(img); 
	}else{ 
		controller="imgControll('"+img+"')";
		intervalID=setTimeout(controller,20);
	}
}
function viewImage(img){
	if(img1.width > 1200){
		W=1200;
	}else{ W=img1.width; }
	if(img1.height > 800){
		H=800;
	}else{ H=img1.height; }
	O="width="+W+",height="+H+",scrollbars=yes";
	var url= 'SJ_content/viewer.php?img='+img;
	imgWin=window.open(url,"YEAH-viewer",O);
}
	
function rereply_ad(for_k, nowid, rp_id){
	$(".re_reply_ad").html(" ");
	$("#rprp"+for_k).html('<div id="replyad'+for_k+'" style="border-top: 2px dotted rgb(219, 219, 219);">'
			+'<div class="reply_2_1">'
				+'<img src="SJ_Board/board_img/rere_icon.png" />'
			+'</div>'
			+'<div class="reply_2_2">'
						+'<form method="post" enctype="multipart/form-data">'
				+'<div style="float: left;width:100%">'
					+'닉네임<input type="text" id="rp_nick" name="rp_nick">비밀번호<input type="password" id="rp_pw" name="rp_pw">'
				+'</div>'
				+'<div style="float: left;width:80%;padding: 5px 0px 10px 0px;">'
						+'<textarea rows="4" id="rep'+for_k+'" name="rep" class="rp"></textarea>'
						+'<input type="hidden" name="idrep" value="'+nowid+'" />'
						+'<input type="hidden" name="replyid" value="'+rp_id+'" />'
						+'<BR>'
						+'<span style="float: left;">'
							+'<img src="SJ_Board/board_img/imgs.png" />댓글 이미지 첨부'
							+'<input type="file" id="file_rp'+for_k+'" name="upload" id="upload">'
						+'</span>'
						+'<span style="float: right;">'
							+'<div class="rp_imgup_rp_bt">'
								+'<input class="rp_bt" type="submit" id="rp'+for_k+'_submit_chk" value="댓글입력" onClick="return rp2_submit('+for_k+')">'
								+'<img id="loading'+for_k+'" src="SJ_Board/board_img/loading.gif" style="width: 20px;height: 20px;border: 0px;display: none">'
							+'</div>'
						+'</span>'
					+'</form>'
				+'</div>'
			+'</div>'
		+'</div>');
	$("#rep"+for_k).focus();
}


//------------------url 자동링크 ------------------
function autolink(con_rp_auto) {
	var container = document.getElementById('con_rp_auto');
	var doc = container.innerHTML;
	var regURL = new RegExp("(http|https|ftp|telnet|news|irc)://([-/.a-zA-Z0-9_~#%$?&=:;200-377()가-힣]+)","gi");
	container.innerHTML = doc.replace(regURL,"<a class='autolink' href='$1://$2' target='_blank'>$1://$2</a>");
}