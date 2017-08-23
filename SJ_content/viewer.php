<!DOCTYPE HTML>
<html>
	<head>
		<script type="text/javascript" src="../SJ_Board/board_js/jquery-2.1.0.min.js"></script>
		<script type="text/javascript">
			var count = 10;
			var zoom_p_t;
			function Picture(){
				clearTimeout(zoom_p_t); //사용자가 스크롤계속 사용시 대비하여 페이드아웃시간 초기화
				count = Counting(count);
				Resize(count);
				return false;
			}
			function Counting(count){
			    if (event.wheelDelta >= 120){
			        if(count<50){count++;}
			    }
			    else if (event.wheelDelta <= -120){
			        if(count>8){count--;}
			    }
			    return count;
			}
			function Resize(count){
			    oImage.style.zoom = count + '0%';
			    document.getElementById("zoom_p_c").innerHTML = count + '0%';
			    $('#zoom_p').css("display", "block"); //퍼센트 표시하기
			    zoom_p_t = setTimeout(function() { $('#zoom_p').fadeOut('slow'); }, 2500); //페이드아웃
			}
			
			var pre_x = 0, pre_y = 0;
			// 이미지 움직이기
			function moveDrag(e){
				var delta_x = pre_x - event.x ;
				var delta_y = pre_y - event.y ;
				pre_x = event.x;
				pre_y = event.y
				document.getElementById("oImage").style.cursor = "-webkit-grabbing";
				window.scrollBy(delta_x,delta_y);
				return false;
			}

			// 드래그 시작
			function startDrag(e, obj){
				pre_x = event.x;
				pre_y = event.y;
			    document.onmousemove = moveDrag;
			    var btn=event.button;
			    if(btn==2){
			    	document.onmouseup = stopDrag_close;
			    }else{
			    	document.onmouseup = stopDrag;
			    }
			}
			
			// 드래그 멈추기
			function stopDrag(){
				document.getElementById("oImage").style.cursor = "-webkit-grab";
				document.onmousemove = null;
				document.onmouseup = null;
			}
			
			function stopDrag_close(){
				document.getElementById("oImage").style.cursor = "-webkit-grab";
				document.onmousemove = null;
				document.onmouseup = null;
				window.close();
			}
		</script>
		<title>YEAH 이미지 상세 보기</title>
	</head>
	<body topmargin=0 leftmargin=0>
		<style>
			img {
				border: none;
				margin: 0;
				padding: 0;
				float: left;
				display: inline;
				vertical-align: top;
			}
			#zoom_p{
				border: 1px solid black;
				display: none;
				left: 3%;
				top: 95%;
				width: 40px;
				position: fixed;
				background-color: #c9c9c9;
				text-align: center;
			}
			#zoom_p_c{
				font-weight: bold;
				font-size: 14px;
			}
		</style>
		<div onmousewheel="Picture()" style="width: 100%;height: 100%;float:left;">
			<img id="oImage" src="../<?php echo $_GET['img'] ?>" style='cursor: -webkit-grab;' alt="오른쪽버튼을 클릭하시면 창이 닫힙니다" title="오른쪽 버튼을 클릭하시면 창이 닫힙니다." onmousedown="startDrag(event, this)">
		</div>
		<div id="zoom_p">
			<span id="zoom_p_c">100%</span>
		</div>
	</body>
</html>
