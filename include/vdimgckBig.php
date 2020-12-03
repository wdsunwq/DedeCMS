<?php 

//本验证采用加密的cookie验证
//新文件的验证数据为 GetCookie("dd_ckstr")

require_once(dirname(__FILE__)."/config_hand.php");

//Session保存路径
$sessSavePath = dirname(__FILE__)."/../data/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

//按默认参数设置一个Cookie
function PutCookie($key,$value,$kptime,$pa="/"){
	 global $cfg_cookie_encode;
	 setcookie($key,$value,time()+$kptime,$pa);
	 setcookie($key.'__ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),time()+$kptime,$pa);
}

//获取随机字符
$rndstring = "";
for($i=0;$i<4;$i++){
	$rndstring .= chr(mt_rand(65,90));
}


//如果支持GD，则绘图
if(function_exists("imagecreate"))
{
	//PutCookie("dd_ckstr",strtolower($rndstring),1800,"/");
	session_register('dd_ckstr');
	$_SESSION['dd_ckstr'] = strtolower($rndstring);
	$rndcodelen = strlen($rndstring);
  
   //字体设置
  $ffsize = 16; //字体大小
  $ffpos = 8; //字间距
  //图片大小
  $iiwidth = 90;
  $iiheight = 35;
  
  $im = imagecreate($iiwidth,$iiheight);
  //字体
  $font_type = dirname(__FILE__)."/data/ant".mt_rand(1,2).".ttf";
  //背景颜色
  $bgcolor = ImageColorAllocate($im, 225,245,255);
  //边框色
  $iborder = ImageColorAllocate($im, 56,172,228);
  //字体色
  $fontColor = ImageColorAllocate($im, 6,110,240);
  $fontColor1 = ImageColorAllocate($im, 166,213,248);
  $fontColor2 = ImageColorAllocate($im, 8,160,246);
  //杂点背景线
  $lineColor1 = ImageColorAllocate($im, 130,220,245);
  $lineColor2 = ImageColorAllocate($im, 225,245,255);
  
  
  //背景线
  for($j=3;$j<$iiheight-2;$j=$j+3) imageline($im,2,$j,$iiwidth-1,$j,$lineColor1);
  for($j=2;$j<$iiwidth;$j=$j+(mt_rand(3,6))) imageline($im,$j,2,$j-6,$iiheight-2,$lineColor2);
  
  //边框
  imagerectangle($im, 0, 0, $iiwidth-1, $iiheight-1, $iborder);
  
  $strposs = array();
  //文字
  $m = 0;
  for($i=0;$i<$rndcodelen;$i++){
	  if(function_exists("imagettftext")){
	  	$strposs[$i][0] = $i*10+10;
	  	$strposs[$i][1] = mt_rand($ffsize+5,$ffsize+10);
	  	imagettftext($im, $ffsize, 0, $strposs[$i][0]+1+$m, $strposs[$i][1]+1, $fontColor1, $font_type, $rndstring[$i]);
	    $m = $m+$ffpos;
	  }
	  else{
	  	imagestring($im, 5, $i*10+6, mt_rand(2,4), $rndstring[$i], $fontColor);
	  }
  }
  
  //文字
  $m = 0;
  for($i=0;$i<$rndcodelen;$i++){
	  if(function_exists("imagettftext")){
	  	imagettftext($im, $ffsize,0, $strposs[$i][0]-1+$m, $strposs[$i][1]-1, $fontColor2, $font_type, $rndstring[$i]);
	    $m = $m+$ffpos;
	  }
  }
  
  header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
  //输出特定类型的图片格式，优先级为 gif -> jpg ->png
  if(function_exists("imagejpeg")){
  	header("content-type:image/jpeg\r\n");
  	imagejpeg($im);
  }else{
    header("content-type:image/png\r\n");
  	imagepng($im);
  }
  ImageDestroy($im);

}else{ //不支持GD，只输出字母 ABCD	
	//PutCookie("dd_ckstr","abcd",1800,"/");
	session_register('dd_ckstr');
	$_SESSION['dd_ckstr'] = "abcd";
	header("content-type:image/jpeg\r\n");
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	$fp = fopen("./vdcode.jpg","r");
	echo fread($fp,filesize("./vdcode.jpg"));
	fclose($fp);
}

?>