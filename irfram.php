 <?php  
	##THE GLOBALISM

	/*a simply ECHO*/ 		function E($S,$IF='') {if($S==NULL or $S==''){echo $IF;}else{echo $S;}}   
	/*a simply newLine*/	function _EN() {echo "\n";}
 
	//GET ZONE_______________________________________________________
    /*if isset get*/		function isGet($S)	{return isset($_GET["$S"]);} 
	/*if GET x Returns x*/	function get($S) 	{if(isGet($S)){return $_GET["$S"];}} 
	/*echoes GET*/			function eGet($S,$def='') 	{if(isGet($S)){E(get($S));}else{E($def);}} 
	/*echo if get*/			function EIFget($if,$Etrue,$Efalse=''){if(isGet($if)){E($Etrue);}else{E($Efalse);}}
	
	//POST ZONE_______________________________________________________
    /*if isset post*/		function isPost($S)	{return isset($_POST["$S"]);}
	/*if POST x Returns x*/	function post($S) 	{if(isPost($S)){return $_POST["$S"];}} 
	/*echoes POST*/			function ePost($S,$def='') 	{if(isPost($S)){E(Post($S));}else{E($def);}}			
	/*echo if post*/		function EIFpost($if,$Etrue,$Efalse=''){if(isPost($if)){E($Etrue);}else{E($Efalse);}}
	/*send post vars*/		function sendPost($var,$data,$url='') { 
								$_POST[$var] = $data; }	
	
	//GET FILE CONTENT ZONE_______________________________________________________	
	/*get file contents*/	function fGet($u) 	{return file_get_contents($u);} 
	/*Echo GFC*/			function eFGet($u) 	{E(fGet($u));}
		
	//SESSIONS ZONE_______________________________________________________
	/* if not session, start session */ 	
	function ifSession()
	{ if (session_status() == PHP_SESSION_NONE) { session_start(); } }
	
	/*Set session*/			function sessionSet($SN,$val=NULL) {ifSession(); $_SESSION[$SN]=$val;}
	/*Get session*/			function sessionGet($SN) {ifSession();return $_SESSION[$SN];}
 	/*Get and set session*/	function sessionGetSet($SN,$val=NULL) {return sessionGet($SN);$_SESSION[$SN]=$val;}
	/*Session delete UNSET*/function sessionDel ($SN) {ifSession();unset($_SESSION[$SN]);}
	/*Clear session*/		function sessionClear($SN) {sessionSet($SN,'');}	
	/*Use and clear*/		function sessionGetClear($SN)  {return sessionGetSet($SN,'');}
	/*Destruction session*/	function sessionDestruct($goToIf=NULL) {ifSession();session_destroy();
										if($goToIf!=NULL){redir($goToIf);}}	
	/*echoes content of session*/	function eSession($SN)	{E(sessionGet($SN));}

	//REDIRECTORS_______________________________________________________
	/* Redirects to... */   function redir($url) {return header("Location:$url");} 
		
	//TIME ZONE_______________________________________________________ 
	/* Timestamp conversion */   function timestamp($format,$time) {return date($format,strtotime($time));;} 

	
	
	

##THE AUXILIARY GLOBALS
//return tag
function  _tag($tn,$in,$m=''){if($in==NULL){return("<$tn $m />");}else{return("<$tn> $m $in</$tn>");}}
function  _input($type,$name,$value,$m='') {return _tag('input',NULL,"type='$type' value='$value' name='$name' id='$name' $m");}

//echoes a INPUT 
function  _EInput($type,$name,$value,$m='') 	{E(_input($type,$name,$value,$m=''));} 
function  _InputHidden ($var,$data,$m='') {return(_input('hidden',$var,$data,$m));} 
function  _EinputHidden ($var,$data,$m='') 		{E(_InputHidden($var,$data,$m));} 
//echoes a TAG <tn m...>in</tn>
function  _ETag($tn,$in,$m='') 	{ E(_tag($tn,$in,$m));}
//echoes a script with a script content
function  _EScr($in){_ETag('script',$in);}
function  script($in) 		{_EScr($in);}

//return a quoted string... "str"
function  _Q($S) {return "\"$S\"";}






//SCRIPT ZONE
$scriptHeadStr = $scriptEndStr  = "\n";

 

##SCRIPTS FOR SCRIPTING & JS
//loads an extern script file.
function  LoadScript($Src) 	
{
	_ETag('script','',"src='$Src'");_EN();
}

//executes ajax XHR in inner HTML of element
function AjaxLoadInPlace ($url,$id) 
{		global $scriptEndStr;

		$url = _Q($url);
		$id  = _Q($id);
	 	$scriptEndStr .= "	ajax.load($id,$url);\n";
}

function isXHR(){return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');}



function putScript($scr)
{
	global $scriptEndStr;
	$scriptEndStr .=  $scr . "\n";
}
 
function alert($s) {putScript("alert($s)");}



 

 


//writes the scripts in HEAD of page
function HEAD_SCRIPT()	{}

//writes all scripts collected during the execution of PHP at end of page.
function END_SCRIPT()	{global $scriptEndStr;if($scriptEndStr!="\n")_EScr($scriptEndStr);}



 	
 

##SANITATION
function cleanInput($input) {

  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $output = preg_replace($search, '', $input);
    return $output;
  }
 
function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

 

function lang_Get($availableLanguages, $default='en'){
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$langs=explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

		foreach ($langs as $value){
			$choice=substr($value,0,2);
			if(in_array($choice, $availableLanguages)){
				return $choice;
			}
		}
	} 
	return $default;
}


//posted data/get/

 function arrPostedValues($arr)
 {
	$arrpass = array();
	for($I=0;$I<count($arr);$I++)
	{ if(strstr($arr[$I],',')) {$ap = explode(',',$arr[$I]);
		if(strstr("_VALUE_",$ap[1]))
		{ $ap2 = explode('_VALUE_',$ap[1]);	
		  $arrpass[$ap[0]] = $ap2;
		}else{
		  $arrpass[$ap[0]] = $_POST[$ap[1]];
		}
	  }
	  else {$arrpass[$arr[$I]] = $_POST[$arr[$I]];}}
	  return $arrpass;
 } 
 
 function arrPostData($v)
 	{
		return array("$v"=>post($v));
	}
	
 function arrGetData($v)
 	{
		return array("$v"=>post($v));
	}	
 
 function arrGetedValues($arr)
 {
	$arrpass = array();
	for($I=0;$I<count($arr);$I++)
	{ if(strstr($arr[$I],',')) {$ap = explode(',',$arr[$I]);
		if(strstr("_VALUE_",$ap[1]))
		{ $ap2 = explode('_VALUE_',$ap[1]);	
		  $arrpass[$ap[0]] = $ap2;
		}else{
		  $arrpass[$ap[0]] = $_GET[$ap[1]];
		}
	  }
	  else {$arrpass[$arr[$I]] = $_GET[$arr[$I]];}}
	  return $arrpass;
 }   
 
 
?>
