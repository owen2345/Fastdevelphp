<?php

/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */

class FD_Url
{   
    /**
     * Retorna el valor de ROOT_PATH
     */
    function getRootPath()
    {
        return ROOT_PATH;
    }
    
    /**
     * Retorna la direccion del folder del proyecto
     */
    function getRootDIR()
    {
        return "../";
    }
    
    /**
     * Retorna el nombre del controlador accedido
     */
    function getCurrentController()
    {
        return CONTROLLER_NAME;
    }
    
    /**
     * Retorna el nombre de la funcion accedida 
     */
    function getCurrentFunction()
    {
        return CONTROLLER_FUNCTION;
    }
    
    /**
     * Verifica si el acceso al controlador fue dentro un modulo
     * Retorna boolean
     */
    function isCurrentSubmodule()
    {
        return SUBFOLDER_CONTROLLER?true:false;
    }
    
    /**
     * Retorna el nombre del modulo accedido
     */
    function getCurrentModulename()
    {
        return SUBFOLDER_CONTROLLER;
    }
    
    /**
     * Retorna un array de las variables enviadas por metodo $_GET
     */
    function getParams_GET()
    {
        return $_GET;
    }
    
    /**
     * Retorna un array de las variables enviadas por metodo $_POST
     */
    function getParams_POST()
    {
        return $_POST;
    }
    
    /**
     * Retorna el valor del parametro $paramName enviado por metodo $_POST
     * sino existe, retorna false
     */
    function getParam_POST($paramName)
    {
        if(isset($_POST["$paramName"]))
            return $_POST["$paramName"];
        else
            return false;
    }
    
    /**
     * Retorna el valor del parametro $paramName enviado por metodo $_GET
     * sino existe, retorna false
     */
    function getParam_GET($paramName)
    {
        if(isset($_GET["$paramName"]))
            return $_GET["$paramName"];
        else
            return false;
    }
    
    /**
     * Verifica si el parametro fue enviado por metodo $_POST
     */
    function has_POSTParam($paramName)
    {
        return isset($_POST["$paramName"]);
    }
    
    /**
     * Verifica si el parametro fue enviado por metodo $_GET
     */
    function has_GETParam($paramName)
    {
        return isset($_GET["$paramName"]);
    }
    
    /**
     * retorna la direccion del directorio de uploads
     */
    function getUploadDIR()
    {
        return "../uploads/";
    }
    
    /**
     * retorna un array de los archivos enviados por metodo $_POST
     */
    function getFiles()
    {
        return $_FILES;
    }
    
    /**
     * retorna la url enviada desde el browser
     * ideal para buscar o comparar textos en la url
     */
    function getRequestUrl()
    {
        return strtolower(URL_CALLED);
    }
}
?>