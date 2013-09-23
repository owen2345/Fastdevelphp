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
class FD_Management
{
	var $layout="";
	var $paramsLayout=array();
	var $Utility;
	var $Connection;
    var $Session;
    var $DB;
    var $SQL;
    var $Request;
    private $config_tmp = array();
    static $_instance;
    private $core_folder = "../core/";
	function FD_Management()
	{
		self::$_instance = $this;
        loadFileFastDevel(array($this->core_folder."FD_ConexionDB.php", $this->core_folder."FD_ManageDB.php", $this->core_folder."FD_ManageModel.php", $this->core_folder."FD_Url.php"));
        $Con = new FD_ConexionDB();
		$this->DB = new FD_ManageDB();
		$this->Request = new FD_Url();
        $this->SQL = $Con;
        $this->Connection = (object)array("DB"=>$this->DB, "SQL"=>$this->SQL);
        $this->Utility = $this->loadLibrary("FD_Utility");
        $this->Session = $this->loadLibrary("FD_Session"); 
        $this->Session->updateFlashMessage();
	}
	
    /**
     * establace un layour
     * $layout: nombre del layout
     * $params: parametros para el layout
     * @return null
    */
	function useLayout($layout, $params=array())
	{
	    if(!is_array($params))
            dieFastDevel("Error: el par�metro \"\$params\" de la funcion \"useLayout()\" debe ser de tipo array()");
		$this->layout=$layout?$layout:$this->layout;
		$this->paramsLayout=$params;
	}
    
    /**
     * Limpia el layout definido
     * @return null
    */
    function clearLayout()
    {
        $this->layout = "";
        $this->paramsLayout = array();
    }
	
    /**
     * Carga la vista con layout
     * $view = nombre de la vista en views/$view.php
     * $params = parametros para la vista - 
     *                  array(
     *                       "nombre_variable_en_vista"=>"valor de variable",
     *                       "nombre_var2"=>valor de variable2
     *                       )
     * $layout = nombre del layout ubicado en layouts/$layout.php, si es vacio usa definicion anterior
     * $paramsLayout = parametros para layout
     * @return null
    */
	function loadView($view, $params=array(), $layout="", $paramsLayout=array())
	{
		if(!is_array($params))
            dieFastDevel("Error: el parámetro \"\$params\" de la funcion \"loadView()\" debe ser de tipo array()");
        if(!is_array($paramsLayout))
            dieFastDevel("Error: el parámetro \"\$paramsLayout\" de la funcion \"loadView()\" debe ser de tipo array()");
            
        $this->layout=$layout?$layout:$this->layout;
		$this->paramsLayout=$paramsLayout?array_merge($this->paramsLayout, $paramsLayout):$this->paramsLayout;		
		ob_start();
		foreach($params as $name=>$val)	
		{	
			$$name=$val;		
		}
        $view_ = FB_checkPath(VIEWS_PATH.$view.".php");
        if($view_)
            include($view_);
        else
            dieFastDevel("This view '$view' doesn't exist.");
        $this->showBuffer(ob_get_clean());
	}
	
    /**
     * Carga una vista sin layout por serie para cada item de $collection
     * $view = nombre de la vista ubicada en views/$view.php
     * $collection = un array de objetos
     * $alias = nombre de la variable $collection[$i]
     * $params = parametros para la vista
     * @return null 
    */
	function loadCollectionView($view, $alias, $collection, $params = array())
	{
	    if(!is_array($params))
            dieFastDevel("Error: el parámetro \"\$params\" de la funcion \"loadCollectionView()\" debe ser de tipo array()");
            
		if(!count($collection))
            return;
            
        $numIteration=0;
        foreach($collection as $Obj)
		{
			$$alias = $Obj;
			foreach($params as $name=>$val)	
			{
				$$name=$val;		
			}
            $view_ = FB_checkPath(VIEWS_PATH.$view.".php");
            if($view_)
                include($view_);
            else
                dieFastDevel("This view '$view' doesn't exist.");
            $numIteration ++;
	 	}
	}
	
    /**
     * Carga una vista sn layout, ideal para ajax
     * $view: nombre de la vista ubicada en views/$view.php
     * $params = parametros para la vista
     * return null
    */
	function loadPartialView($view, $params=array())
	{
        if(!is_array($params))
            dieFastDevel("Error: el par�metro \"\$params\" de la funcion \"loadPartialView()\" debe ser de tipo array()");
            
		foreach($params as $name=>$val)	
		{	
			$$name=$val;		
		}
        $view_ = FB_checkPath(VIEWS_PATH.$view.".php");
        if($view_)
            include($view_);
        else
            dieFastDevel("This view '$view' doesn't exist.");
	}
	
	private function showBuffer($contentView)
	{
		foreach($this->paramsLayout as $name=>$val)	
		{	
			$$name=$val;		
		}
		if($this->layout)
        {
            $view_ = FB_checkPath(LAYOUTS_PATH.$this->layout.".php");
            if($view_)
                include($view_);
            else
                dieFastDevel("This Layout '$this->layout' doesn't exist.");
        }
        else
		 	echo $contentView;
	}
    
    /**
     * ejecuta todos los shortcode tags como: [saludo msg='hola mundo']
     * $content: contenido html o texto
     * Nota: todos los shortcodes deben ser configurados en FD_config.php
     * return string
    */
    function do_shortCode($content = "")
    {
        global $FD_Shortcodes;

    	if (empty($FD_Shortcodes) || !is_array($FD_Shortcodes))
    		return $content;
        
        $tagnames = array_keys($FD_Shortcodes);
    	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
    	// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcodes()
    	$pattern = '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
        
    	return preg_replace_callback('/'.$pattern.'/s', array($this, 'do_shortcode_tag'), $content);
    }
    
    private function do_shortcode_tag($m)
    {
        global $FD_Shortcodes;
        // allow [[foo]] syntax for escaping a tag
    	if ( $m[1] == '[' && $m[6] == ']' ) {
    		return substr($m[0], 1, -1);
    	}
    
    	$tag = $m[2];
    	$attr = $this->Utility->shortcode_parse_atts( $m[3] );
        $Obj = $this;
        $functionName = $FD_Shortcodes[$tag];
        if(strpos($FD_Shortcodes[$tag], "::")!==false)
        {
            $tmp = explode("::", $FD_Shortcodes[$tag]);
            $Obj = new $tmp[0]();
            $functionName = $tmp[1];
        }
    	if ( isset( $m[5] ) ) {
    		// enclosing tag - extra parameter
    		return $m[1] . call_user_func(array($Obj, $functionName), $attr, $m[5], $tag ) . $m[6];
    	} else {
    		// self-closing tag
    		return $m[1] . call_user_func(array($Obj, $functionName), $attr, NULL,  $tag ) . $m[6];
    	}
    }
    
    /**
     * Render another controller
     * funcion deprecada
     * @return null
    **/
	function renderController($controllerName, $functionName="index", $layout = "", $params_layout = array())
	{
        $o = $this->loadController($controllerName);
		$o->useLayout($layout?$layout:$this->layout, $params_layout);
		$o->$functionName();
	}
	
    /**
     * redirecciona la pagina a: ROOT_PATH.$controller/$function?$paramsGet
     * $controller: nombre del controlador
     * $function: nombre de la funcion en $controller
     * $paramsGet: parametros $_GET : nombre=owen&app=peredo
     * @return null
    **/
	function redirect($controller, $function = "index", $paramsGet = "")
	{
		header("Location: ".ROOT_PATH."$controller/$function".($paramsGet?"?".$paramsGet:""));
        exit();
	}
    
    
    /**
     * Carga una libreria $Classname con parametors $params
     * $Classname: nombre de la clase y del archivo ubicado en library/$Classname.php
     * @return Library Object
    **/
	function loadLibrary($Classname, $params = array())
	{
        loadFileFastDevel(LIBRARIES_PATH.$Classname.".php");
        $reflection_class = new ReflectionClass(ucwords($Classname));
        $Object = $reflection_class->newInstanceArgs($params);
        return $Object;
	}
        
    /**
     * Carga un helper
     * $source: nombre del archivo ubicado en helpers/$source
     * @return null
    **/    
    function loadHelper($source)
	{
	   $view_ = FB_checkPath(HELPERS_PATH.$source);
        if($view_)
            include($view_);
        else
            dieFastDevel("This helper '$source' doesn't exist.");
	}
    
    /**
     * Carga una configuracion
     * $source: nombre del archivo ubicado en confi/$source
     * @return null
    **/ 
    function loadConfig($source)
    {
        if(!in_array($source, $this->config_tmp))
        {
            loadFileFastDevel("../confi/".$source);
            array_push($this->config_tmp, $source);
        }
    }
    
    /**
     * Private function used for core functions
     */
    function loadModel($model_name)
    {
        loadFileFastDevel(MODELS_PATH.$model_name.".php");
    }
    
    /**
     * $controller_name: name of the controller to load
     * $module: module where the controller is located of
     * return controller object
     */
    function loadController($controller_name, $module = null)
    {
        $controller_name.="_Controller";
        loadFileFastDevel(CONTROLLERS_PATH.($module?$module."/":"").$controller_name.".php");
		return new $controllerName();
    }

    private function __clone(){ }

    public static function getInstance(){
        /*if (!(self::$_instance instanceof self)){
            self::$_instance=new self();
        }*/
        return self::$_instance;
    }
}
?>