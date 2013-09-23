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
loadFileFastDevel("../core/FD_Management.php");

/**
 * Obtiene la instancia del controlador en cualquier lugar del proyecto
 * Return: FD_Management Object
 */
function getInstance()
{
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
    include(LOGS_PATH."dieFastDevel.php");
    exit;
}

/**
 * Guarda el mensaje $log en los logs
 * $skypeFileInfo: false - guarda la informacion del archivo, true - guarda la informacion de archivo
 */
function fd_log($log, $skypeFileInfo = false)
{
    if(!defined("ENABLE_LOG") || !ENABLE_LOG)
        return;
        
    $debug = "";
    if(!$skypeFileInfo)
        $debug = FD_getDebug("\n");
        
    $consolePHP = fopen(LOGS_PATH."log.log", "a+");
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

function loadFileFastDevel($sources)
{
    if(!is_array($sources))
        $sources = array($sources);
    
    foreach($sources as $source)
    {
        foreach(get_included_files() as $rpath)
        {
            if($rpath == realpath($source))
                return;
        }
        
        $a = FB_checkPath($source);
        if($a)
            include($a);
        else
            dieFastDevel("Not found source: $source");
    }
}

function __autoload($class_name)
{
    loadFileFastDevel(LIBRARIES_PATH.$class_name.".php");
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

/*************************************************************  End functions  *********************************************************************/
$_POST = unstrip_array($_POST);
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
if(isset($url_post[0]) && $url_post[0] && is_dir(FB_checkPath(CONTROLLERS_PATH.$url_post[0])))//when the controller is within sub directory
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

/**  AUTO LOAD HELPERS  ***/
foreach($FD_autohelpers as $fd_h)
{
    loadFileFastDevel(HELPERS_PATH.$fd_h);
}

$_GET=array_merge($_GET,$params_get);

/** start controller **/
$controllerName = $controllerName?$controllerName:DEFAULT_CONTROLLER;
define("CONTROLLER_NAME", $controllerName);
define("CONTROLLER_FUNCTION", $controllerFunction);
define("SUBFOLDER_CONTROLLER", $subfolderController);
$controller_name=$controllerName."_Controller";
loadFileFastDevel(CONTROLLERS_PATH.($subfolderController?$subfolderController."/":"").$controller_name.".php");
$o=new $controller_name();
$url_vars = explode("\?", key_exists("param_id", $_GET)?$_GET["param_id"]:"");
$class = new ReflectionClass(get_class($o));
if($class->hasMethod($controllerFunction))
    call_user_func_array(array($o, $controllerFunction), $post_params);
else
    dieFastDevel("Not exist the function: \"$controllerFunction\" for \"".($subfolderController?$subfolderController."/":"")."$controller_name\".php");

?>