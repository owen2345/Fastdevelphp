<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email peredo@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */ 
class UI_scaffold_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("ui_scaffold");
	}
        
    function index()
    {
        $this->loadView("ui_scaffold/index");
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
        $this->loadView("ui_scaffold/create", $data);
    }
    
    function generate()
    {
        //echo "<pre>";
        //print_r($_POST);
        new FD_Scafold($_POST["model"], $_POST["data"], null, $_POST["type_scaffold"], $_POST["module_name"]);
        //$this->loadView("ui_scaffold/generate");
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
            $res[strtolower($Col['Field'])] = strtolower($Col['Field']);
                
        $Class = new ReflectionClass(ucwords($table));
        foreach($Class->getMethods() as $Method)
        {
            if($Method->class != "FD_ManageModel")
                $res[$Method->name."()"] = $Method->name."()";
        }
        
        echo $this->Utility->createOptions(array("name"=>$name), $res, null, null, false);        
    }
        
    function getColumnsData($table = null, $qty = 1)
    {
        $data["Types"] = array("text"=>"Input field", 
                                "select"=>"Select field",
                                "select_object"=>"Select object field", 
                                "radio"=>"Radio field",
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
            $res[strtolower($Col['Field'])] = strtolower($Col['Field']);
        $data["Fields"] = $res;
        for($i = 0; $i < $qty; $i++)
            $this->loadPartialView("ui_scaffold/data", $data);
    }
}
?>