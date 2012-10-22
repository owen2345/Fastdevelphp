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
    include($source);
}

function __autoload($class_name)
{	
    $class_name1 = $class_name;
	$class_name=ucwords($class_name);
    /*******controllers********/
    if(SUBFOLDER_CONTROLLER && file_exists("../controllers/".SUBFOLDER_CONTROLLER."/".$class_name . '.php'))
		require_once "../controllers/".SUBFOLDER_CONTROLLER."/".$class_name . '.php';
    elseif(SUBFOLDER_CONTROLLER && file_exists("../controllers/".SUBFOLDER_CONTROLLER."/".$class_name1 . '.php'))
		require_once "../controllers/".SUBFOLDER_CONTROLLER."/".$class_name1 . '.php';
	elseif(file_exists("../controllers/".$class_name . '.php'))
		require_once "../controllers/".$class_name . '.php';
    elseif(file_exists("../controllers/".$class_name1 . '.php'))
		require_once "../controllers/".$class_name1 . '.php';            
	/*******configs********/
	elseif(file_exists("../core/".$class_name . '.php'))
		require_once "../core/".$class_name . '.php';
    elseif(file_exists("../core/".$class_name1 . '.php'))
		require_once "../core/".$class_name1 . '.php';        
	/*******modelos********/
	elseif(file_exists("../models/".$class_name . '.php'))
		require_once "../models/".$class_name . '.php';
    elseif(file_exists("../models/".$class_name1 . '.php'))
		require_once "../models/".$class_name1 . '.php';        	
    /*******vendors********/
	elseif(file_exists("../vendors/".$class_name . '.php'))
		require_once "../vendors/".$class_name . '.php';	
    elseif(file_exists("../vendors/".$class_name1 . '.php'))
		require_once "../vendors/".$class_name1 . '.php';        
	else	
    {
        header("HTTP/1.0 404 Not Found");
        dieFastDevel("Not exist the controller \"$class_name\" o no existe la url: ".$_SERVER["REDIRECT_URL"]."");
    }
}

//session_set_cookie_params(43200);` // set the session lifetime

session_start();
include("../confi/FD_Config.php");

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
$_POST = unstrip_array($_POST);
/** end **/

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
if(isset($url_post[0]) && $url_post[0] && is_dir("../controllers/".$url_post[0]))//when the controller is within sub directory
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

?>