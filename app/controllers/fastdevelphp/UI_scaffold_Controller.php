<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email peredo@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */ 
class UI_scaffold_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("fastdevelphp/ui_scaffold");
	}
        
    function index()
    {
        $this->loadView("fastdevelphp/ui_scaffold/index");
    }
    
    function create()
    {
        $Tables = $this->SQL->getDatabaseTables();
        $Tables_aux = array();
        foreach($Tables as $table)
        {
            $Tables_aux[$table] = $table;
        }
        $data["Tables"] = $Tables_aux;
        $data["Columns"] = $this->SQL->getFieldsTable(current($Tables_aux));
        $this->loadView("fastdevelphp/ui_scaffold/create", $data);
    }
    
    function generate()
    {
        if(!$this->Request->getParam_POST("capcha") || $this->Request->getParam_POST("capcha") != $this->Session->get_data("capcha"))
        {
            $this->Session->addFlashMessage("gen_error", "Invalid capcha value, please try again.", 1);
            $this->redirect("fastdevelphp/ui_scaffold", "create");
            return;
        }
        
        ob_start();
        loadFileFastDevel("../core/FD_Scafold.php");
        new FD_Scafold($_POST["model"], $_POST["data"], null, $_POST["type_scaffold"], $_POST["module_name"]);
        $content = ob_get_clean();
        $this->loadView("fastdevelphp/ui_scaffold/generate", array("content"=>$content));
    }
    
    function getColumns($table = null)
    {
        $Columns = $this->SQL->getFieldsTable($table);
        $res = array();
        foreach($Columns as $Col)
            $res[strtolower($Col['Field'])] = strtolower($Col['Field']);            
        echo $this->Utility->createGroupChecks(array("class"=>"fields"), "fields", $res);
    }
    
    function getColumns_and_functions($table = null)
    {
        $name = $_GET["name"];
        $Columns = $this->SQL->getFieldsTable($table);
        $res = array();
        foreach($Columns as $Col)
            $res[$Col] = $Col;
                
        /*$Class = new ReflectionClass(ucwords($table));
        foreach($Class->getMethods() as $Method)
        {
            if($Method->class != "FD_ManageModel")
                $res[$Method->name."()"] = $Method->name."()";
        }*/
        
        echo $this->Utility->createOptions(array("name"=>$name), $res, null, null, false);        
    }
        
    function getColumnsData($table = null, $qty = 1)
    {
        $data["Types"] = array("text"=>"Input field", 
                                "select"=>"Select field",
                                "select_object"=>"Select object field", 
                                "radio_list"=>"Radio field",
                                "radio_object"=>"Radio object field",
                                "checkbox"=> "Checkbox field",
                                "checkbox_object"=> "Checkbox object field",
                                "file"=>"File field",
                                "hidden_field"=>"Hidden field",
                                "password"=>"Password field",
                                "textarea"=>"Textarea field");
        
        $Tables = $this->SQL->getDatabaseTables();
        $Tables_aux = array();
        foreach($Tables as $table_i)
        {
            $Tables_aux[$table_i] = $table_i;
        }
        $data["Tables"] = $Tables_aux;
                
        $Columns = $this->SQL->getFieldsTable($table);
        $res = array();
        foreach($Columns as $Col)
            $res[$Col] = $Col;
        $data["Fields"] = $res;
        for($i = 0; $i < $qty; $i++)
            $this->loadPartialView("fastdevelphp/ui_scaffold/data", $data);
    }
    
    function capcha_image($width='120',$height='40') 
    {
        $code = rand(8111, 99999);
        $this->Session->add_data(array("capcha"=>$code));
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, VIEWS_PATH."fastdevelphp/ui_scaffold/monofont.ttf", $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, VIEWS_PATH."fastdevelphp/ui_scaffold/monofont.ttf" , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$_SESSION['security_code'] = $code;
	}
}
?>