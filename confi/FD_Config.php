<?php

session_start();

$protocol = isset($_SERVER['HTTPS'])?'https':'http';
define("ROOT_PATH","$protocol://".$_SERVER["HTTP_HOST"]."/path_to_project/");
 
/**
 * Database
 */
 define("USE_DB","false");
define("HOST_DB","host");
define("USER_DB","user db");
define("PSSWD_DB","password db");
define("NAME_DB","database name");
define("PREFIX_TABLE_IGNORE","wp_"); // wp_    esto ignora las tablas: wp_config, wp_posts, wp_.., ...
 
/**
 * Path's
 */
define("CSS_PATH",ROOT_PATH."css/");
define("JS_PATH",ROOT_PATH."js/");
define("IMAGES_PATH",ROOT_PATH."images/");
define("VIEWS_PATH","../views/");
define("LAYOUTS_PATH","../layouts/");
define("MODELS_PATH","../models/");
 
/**
 * Configurations
 */
define("DEFAULT_CONTROLLER","home");
define("ENABLE_CONSOLE","true");
define("SITE_NAME","nombre del sitio");
 
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
//header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
//header("Cache-Control: no-cache, must-revalidate" ); 
//header("Pragma: no-cache" );
header("Content-type: text/html; charset=utf-8");
 
 
/**
 * Used for mask the url's, Key as mask name, Value as real path
 * Static sample: "contacto.html" => "home/contacto"
 * Dinamic sample: "producto/[0-9]" => "producto/show/$0" (support reg expresions)
*/
$FD_Routes = array(
    "profile" => "company_user/profile"
);
 
 
/**
 * key = shortcode name.
 * val = Controller Name and function name.
 * Example: "saludo" => "Home::hola_mundo" This sample execute the function hola_mundo($atts){  } from Home_Controller
 * 
 * After defined this, you can use the shortcode like to: [key attrName1='val' attrName2='val2' ...] with $this->do_shortCode()
 * Example: $this->do_shortCode("Here is my salute: [saludo msg='hola mundo'], good bye.");
 * The holamundo() function, should be similar to 
 * function hola_mundo($atts){  ob_start(); echo $atts["msg"]; return ob_get_clean(); } 
*/
$FD_Shortcodes = array(
    //"saludo" => "Home::hola_mundo"
);
?>