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
class FD_Management extends FD_Common
{
	var $layout="";
	var $paramsLayout=array();
	var $Utility;
	var $Connection;
    var $Session;
    var $DB;
    var $SQL;
    private $config_tmp = array();
    static $_instance;
	function FD_Management()
	{
        self::$_instance = $this;
        $Con = new FD_ConexionDB();
        $this->Connection = (object)array("DB"=>new FD_ManageDB(), "SQL"=>$Con);
        $this->DB = $this->Connection->DB;
        $this->SQL = $this->Connection->SQL;
        $this->Utility = new FD_Utility();
        $this->Session = new FD_Session($this->Connection); 
        $this->Session->updateFlashMessage();
	}
	
    /**
     * Set the layout for the views and initial $params (vars) for the layout
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
     * Clear the previus layout defined
     * @return null
    */
    function clearLayout()
    {
        $this->layout = "";
        $this->paramsLayout = array();
    }
	
    /**
     * Load a specific view located in: views/$view.php with Layout $layout or previus layout defined with useLayout()
     * @param $view = view filename
     * @param $params = vars for view
     * @param $layout = Layout to use, this should be in layouts/$layout.php
     * @param $paramsLayout = vars for the layout
     * @return null
    */
	function loadView($view, $params=array(), $layout="", $paramsLayout=array())
	{
		if(!is_array($params))
            dieFastDevel("Error: el par�metro \"\$params\" de la funcion \"loadView()\" debe ser de tipo array()");
        if(!is_array($paramsLayout))
            dieFastDevel("Error: el par�metro \"\$paramsLayout\" de la funcion \"loadView()\" debe ser de tipo array()");
            
        $this->layout=$layout?$layout:$this->layout;
		$this->paramsLayout=$paramsLayout?array_merge($this->paramsLayout, $paramsLayout):$this->paramsLayout;		
		ob_start();
		foreach($params as $name=>$val)	
		{	
			$$name=$val;		
		}
        if($path = $this->Utility->checkPath(VIEWS_PATH.$view.".php"))
        {
            include($path);
            $this->showBuffer(ob_get_clean());
        }else
            dieFastDevel("No existe la vista \"".$view.".php\"");
	}
	
    /**
     * Load a view for a collection views, sample: multiples user profiles with the same view
     * @param $collection = array(User1, $User2, ...)
     * @param $alias = name of each item of $collection in the View
     * @param $params = params for view
     * @return null 
    */
	function loadCollectionView($view, $alias, $collection, $params = array())
	{
	    if(!is_array($params))
            dieFastDevel("Error: el par�metro \"\$params\" de la funcion \"loadCollectionView()\" debe ser de tipo array()");
            
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
            if(file_exists(VIEWS_PATH.$view.".php"))
            {
                include(VIEWS_PATH.$view.".php");
                $numIteration ++;
            }else
                dieFastDevel("No existe la vista \"".$view.".php\"");
	 	}
	}
	
    /**
     * Load the view without layout ideal for ajax or alternative to include()
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
        if($path = $this->Utility->checkPath(VIEWS_PATH.$view.".php"))
            include($path);
        else
            dieFastDevel("No existe la vista \"".$view.".php\"");
	 	
	}
	
	private function showBuffer($contentView)
	{
		foreach($this->paramsLayout as $name=>$val)	
		{	
			$$name=$val;		
		}
		if($this->layout)
        {
            if(file_exists(LAYOUTS_PATH.$this->layout.".php"))
                include(LAYOUTS_PATH.$this->layout.".php");
            else
                dieFastDevel("No existe el Layout \"".$this->layout.".php\"");
        }else
		 	echo $contentView;
		
	}
    
    /**
     * execute shortcode tags such as [saludo msg='hola mundo']
     * Each shortcode should be configured into FB_Config.php in $FD_Shortcodes variable
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
     * @return null
    **/
	function renderController($controllerName, $functionName="index", $layout = "", $params_layout = array())
	{
		$controllerName.="_Controller";
		$o=new $controllerName();
		$o->useLayout($layout?$layout:$this->layout, $params_layout);
		$o->$functionName();
	}
	
    /**
     * redirect the page to new url: ROOT_PATH.$controller/$function?$paramsGet
     * @return null
    **/
	function redirect($controller, $function = "index", $paramsGet = "")
	{
		header("Location: ".ROOT_PATH."$controller/$function".($paramsGet?"?".$paramsGet:""));
        exit();
	}
    
    
    /**
     * Load a library Object with default parameters
     * @return Library Object
    **/
	function loadLibrary($Classname, $params = array())
	{
		$class_name1 = $Classname;
        $class_name=ucwords($Classname);
        $Object;
        if(file_exists("../library/".$class_name . '.php'))
        {
            if(!class_exists($class_name, false))
                include "../library/".$class_name . '.php';
            $reflection_class = new ReflectionClass($class_name);
            $Object = $reflection_class->newInstanceArgs($params);
                        
        
        }elseif(file_exists("../library/".$class_name1 . '.php'))
        {
            if(!class_exists($class_name1, false))
                require "../library/".$class_name1 . '.php';
            $reflection_class = new ReflectionClass($class_name1);
            $Object = $reflection_class->newInstanceArgs($params);
        
        }else
        {
            header("HTTP/1.0 404 Not Found");
            dieFastDevel("Not exist the library \"$class_name\" ");
        }
        
        return $Object;
	}
        
    /**
     * Load a Helper file
     * @return null
    **/    
    function loadHelper($source)
	{
		loadFileFastDevel("../helpers/".$source);
	}

    function loadConfig($source)
    {
        if(!in_array($source, $this->config_tmp))
        {
            loadFileFastDevel("../confi/".$source);
            array_push($this->config_tmp, $source);
        }
        
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