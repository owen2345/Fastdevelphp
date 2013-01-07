<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */

include("../confi/FD_Config.php");

$_POST = unstrip_array($_POST);
/** end **/

define("URL_CALLED", strtolower($_GET["url_fastdevel"]));

/** routes*/
if(key_exists($_GET["url_fastdevel"], $FD_Routes))
    $_GET["url_fastdevel"] = $FD_Routes[$_GET["url_fastdevel"]];
else
{
    foreach($FD_Routes as $keyy => $vall)
    {
        $keyy = str_replace("/", "\/", $keyy);
        preg_match("/$keyy/", $_GET["url_fastdevel"], $res_route);
        if(count($res_route))
            $_GET["url_fastdevel"] = preg_replace("/$keyy/", $vall, $_GET["url_fastdevel"]);
    }
}
/** end routes*/


$url_fastdevelA = explode("\?", $_GET["url_fastdevel"]);
$url_post = $url_fastdevelA[0];

/** POST params */
$url_post = explode("/", $url_post);
$posControllerName = 0;
$posControllerFunction = 1;
$subfolderController = "";
if(isset($url_post[0]) && $url_post[0] && is_dir(FB_checkPath("../controllers/".$url_post[0])))//when the controller is within sub directory
{
    $posControllerName ++;
    $posControllerFunction ++;
    $subfolderController = $url_post[0];
}
$controllerName = isset($url_post[$posControllerName])?$url_post[$posControllerName]:"";
$controllerFunction =  isset($url_post[$posControllerFunction]) && $url_post[$posControllerFunction]?$url_post[$posControllerFunction]:"index";

$post_params = array();
if(count($url_post)>($posControllerFunction+1))
{
    for($pi = $posControllerFunction+1; $pi<count($url_post); $pi++)
    {
        if($url_post[$pi] || $url_post[$pi] == 0)
            array_push($post_params, $url_post[$pi]);
    }
}
/** end **/

/** GET params */
$url_orig=explode("\?",$_SERVER["REQUEST_URI"]);
if(count($url_orig)==1)
    $url_orig[1] = "";
$params_get_ant=explode("&",$url_orig[1]);
$params_get=array();
foreach($params_get_ant as $p)
{
	$v=explode("=",$p);
    if(count($v)==1)
        $v[1] = "";
	$params_get["$v[0]"]=$v[1];
}
/** end **/

/** start controller **/
$controllerName = $controllerName?$controllerName:DEFAULT_CONTROLLER;
define("CONTROLLER_NAME", $controllerName);
define("CONTROLLER_FUNCTION", $controllerFunction);
define("SUBFOLDER_CONTROLLER", $subfolderController);

$_GET=array_merge($_GET,$params_get);

$controllerName=$controllerName."_Controller";
$o=new $controllerName();
//$o->FD_Management();
$url_vars = explode("\?", key_exists("param_id", $_GET)?$_GET["param_id"]:"");
$class = new ReflectionClass(get_class($o));
if($class->hasMethod($controllerFunction))
    call_user_func_array(array($o, $controllerFunction), $post_params);
else
    dieFastDevel("Not exist the function: \"$controllerFunction\" for \"$controllerName\"");





/**
 * Obtiene la instancia del controlador en cualquier lugar del proyecto
 * Return: FD_Management Object
 */
function getInstance()
{
    $aux = CONTROLLER_NAME.'_Controller';
    $C = FD_Management::getInstance();
    return $C;
}


function FB_checkPath($path = null)
{
    if(!$path)
        return $path;
        
    $custom = false;
    $res = "";
    $parts = explode("/", $path);
    if(count($parts) <= 1)
    {
        $custom = true;
        $parts = explode("/", "./".$path);
    }
        
        
    for($i=0; $i < count($parts); $i++)
    {
        $part = $parts[$i];
        if(is_dir($res.$part))
            $res = $res.$part."/";
        else
        {
            $res = $res = $res.$part;
            break;
        }
        if(!isset($parts[$i+1]))
            continue;
        $band = false;
        foreach(scandir($res) as $dir_file_name)
        {
            if(strtolower($parts[$i+1]) == strtolower($dir_file_name))
            {
                $parts[$i+1] = $dir_file_name;
                $band = true;
            }
        }
        
        if(!$band)
            return false;
    }
    
    if($custom)
        $res = str_replace("./", "", $res);
    return $res;
}

/**
 * Muestra mensaje de error $msg
 */
function dieFastDevel($msg)
{
    include("../logs/dieFastDevel.php");
    exit;
}

/**
 * Guarda el mensaje $log en los logs
 * $skypeFileInfo: false - guarda la informacion del archivo, true - guarda la informacion de archivo
 */
function fd_log($log, $skypeFileInfo = false)
{
    if(!ENABLE_LOG)
        return;
        
    $debug = "";
    if(!$skypeFileInfo)
        $debug = FD_getDebug("\n");
    //chmod("../logs/log.log", 0777);
    $consolePHP = fopen("../logs/log.log", "a+");
    fwrite($consolePHP, "\n\n".$log ."\n".$debug);
    fclose($consolePHP);
}
/**
 * DEPRECATED
 */
function FD_getDebug($breakLine = "<br>")
{
    $caller = debug_backtrace();
    $e = "";
	foreach(array_reverse($caller) as $c)
	{
	    if(isset($c["file"]))
        {
            $ttmp = explode('\\', $c["file"]);
            $file = $ttmp[count($ttmp)-1];
            if($file == "Owen.php" || $c["function"] == "getDebug" || $c["function"] == "FD_getDebug" || $c['function'] == 'include' || $c['function'] == 'dieFastDevel')
                continue;
            $e.="File: ".$file." --- Function: ".$c["function"]
                ." --- Line: ".$c["line"].$breakLine;
        }
	}
    return $e;
}

function loadFileFastDevel($source)
{
    $a = FB_checkPath($source);
    if($a)
        include($a);
    else
        dieFastDevel("Not found source: $source");
}

function __autoload($class_name)
{	
    $class_name1 = $class_name;
	$class_name=ucwords($class_name);
    /*******controllers********/
    if(SUBFOLDER_CONTROLLER && FB_checkPath("../controllers/".SUBFOLDER_CONTROLLER."/".$class_name . '.php'))
		require_once FB_checkPath("../controllers/".SUBFOLDER_CONTROLLER."/".$class_name . '.php');
	elseif(FB_checkPath("../controllers/".$class_name . '.php'))
		require_once FB_checkPath("../controllers/".$class_name . '.php');
	/*******configs********/
	elseif(FB_checkPath("../core/".$class_name . '.php'))
		require_once FB_checkPath("../core/".$class_name . '.php');
	/*******modelos********/
	elseif(FB_checkPath("../models/".$class_name . '.php'))
		require_once FB_checkPath("../models/".$class_name . '.php');
    /*******vendors********/
	elseif(FB_checkPath("../vendors/".$class_name . '.php'))
		require_once FB_checkPath("../vendors/".$class_name . '.php');
	else	
    {
        header("HTTP/1.0 404 Not Found");
        dieFastDevel("Not exist the controller \"$class_name\" o no existe la url: ".$_SERVER["REDIRECT_URL"]."");
    }
}

/** unstrip $_post slashes**/
function unstrip_array($array){
	foreach($array as &$val){
		if(is_array($val)){
			$val = unstrip_array($val);
		}else{
			$val = stripslashes($val);
		}
	}
    return $array;
}

?>