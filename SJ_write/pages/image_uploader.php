<?php
function generateRandomString($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$target_dir = "upload/";

$fCnt = count($_FILES['upload_file']['name']);

if($fCnt > 10) { //파일갯수 체크
	echo '<meta charset="utf-8">
		  <script type="text/javascript">
		    alert("파일의 갯수가 10개를 초과하였습니다. \n (업로드 실패)");
		  	location.href="'.$_SERVER['HTTP_REFERER'].'";
		  </script>';
	exit;
}

$all_size = 0;
for($size_chk=0; $size_chk<$fCnt;$size_chk++){
	$all_size = $all_size + $_FILES["upload_file"]["size"][$size_chk];
}
if ($all_size > 5000000) { //파일 크기 체크 핫하!
	$uploadOk = 0;
	echo '<meta charset="utf-8">
		  <script type="text/javascript">
		    alert("죄송합니다. 총 5MB이상의 사진은 업로드 할수없습니다.");
		  	location.href="'.$_SERVER['HTTP_REFERER'].'";
		  </script>';
	exit;
}

for($i=0;$i<$fCnt;$i++) 
{
	
	$file_name[] = $_FILES['upload_file']['name'][$i];
	$file_size[] = $_FILES["upload_file"]["size"][$i];
	
	$imageFileType = pathinfo($_FILES['upload_file']['name'][$i],PATHINFO_EXTENSION);
	$filename =  iconv("UTF-8","EUC-KR", date("YmdHis")."_BYLSJ_BOARD_".generateRandomString(5).".".$imageFileType);
	$image_url[] = 'SJ_write/pages/upload/'.$filename;
		
	$target_file = $target_dir . $filename;
	$uploadOk = 1;
	// Check if image file is a actual image or fake image
	$check = getimagesize($_FILES["upload_file"]["tmp_name"][$i]);
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
	if ($_FILES["upload_file"]["size"][$i] > 3000000) { //파일 크기 지정
		$uploadOk = 0;
		echo '<meta charset="utf-8">
			  <script type="text/javascript">
			    alert("죄송합니다. 3MB이상의 사진은 업로드 할수없습니다.");
			  	location.href="'.$_SERVER['HTTP_REFERER'].'";
			  </script>';
		exit;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
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
	    if (move_uploaded_file($_FILES["upload_file"]["tmp_name"][$i], $target_file)) {
	        echo "The file ". basename( $_FILES["upload_file"]["name"][$i]). " has been uploaded.";
	    } else {
	    	echo '<meta charset="utf-8">
				  <script type="text/javascript">
				    alert("죄송합니다. 파일을 업로드하는 중 오류가 발생했습니다.");
				  	location.href="'.$_SERVER['HTTP_REFERER'].'";
				  </script>';
			exit;
	    }
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8">
<title>image_uploader.php</title> 
<script src="../js/popup.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="../css/popup.css" type="text/css"  charset="utf-8"/>
<script type="text/javascript">
// <![CDATA[
    
    function initUploader(){
            
        var _opener = PopupUtil.getOpener();
        if (!_opener) {
            alert('잘못된 경로로 접근하셨습니다.');
            return;
        }
        
        var _attacher = getAttacher('image', _opener);
        registerAction(_attacher);
            
            if (typeof(execAttach) == 'undefined') { //Virtual Function
            return;
        }
        var _mockdata = new Array();
        
        <?php for($ii=0;$ii<$fCnt;$ii++){ ?>
        
	    _mockdata[<?php echo $ii ?>] = {
	    	'imageurl': "<?php echo $image_url[$ii]; ?>",
            'filename': "<?php echo $file_name[$ii]; ?>",
            'filesize': <?php echo $file_size[$ii]; ?>,
            'imagealign': 'C',
            'originalurl': "<?php echo $image_url[$ii]; ?>",
            'thumburl': "<?php echo $image_url[$ii]; ?>"
	    };
	    <?php } ?>
	    
	    for (var i=0;i<_mockdata.length;i++) 
	    {        
	        execAttach(_mockdata[i]);    
	    }
        closeWindow();
                
    }
// ]]>
</script>
</head>
<body onload="initUploader();">
</body>
</html> 