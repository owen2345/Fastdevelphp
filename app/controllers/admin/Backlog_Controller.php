<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@skylogix.com
 * @company Systems Development Company "Skylogix" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */ 
class Backlog_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("fastdevelphp/backend");
	}
    
    /**
    * default controller function
    **/
    function index()
    {
        $this->lists();
    }
    
    /**
    * show list items
    **/
    function lists()
    {
        
        $data["Backlogs"] = $this->DB->get_objects("Backlog");        
        $this->loadView("admin/Backlog/index", $data);
    }
    
    /**
    * show create form view
    **/
    function create()
    {
        
        $data["Backlog"] = $this->DB->create_object("Backlog", array('id_backlog' => 'Here default value', 'createat_backlog' => 'Here default value'));
        $data["action"] = "save";
        $data["submitText"] = "Register";        
        $this->loadView("admin/Backlog/form", $data);
    }
    
    /**
    * save information into database and then redirect to list items.
    **/
    function save()
    {
        
        $this->DB->create_object("Backlog", $this->Request->getParams_POST())->save();
        $this->Session->addFlashMessage("action", "Item saved!", 0);
        $this->redirect("admin/Backlog");
    }
    
    /**
    * show edit form view
    * $id: id_Backlog (identifier value)
    **/
    function edit($id)
    {
        
        $data["Backlog"] = $Backlog = $this->DB->get_object_by_id("Backlog", $id);
        $data["action"] = "update/$Backlog->id_backlog";
        $data["submitText"] = "Update";
        $this->loadView("admin/Backlog/form", $data);
    }
    
    /**
    * save changes in to database and then redirect to list items.
    * $id: id_Backlog (identifier value)
    **/
    function update($id)
    {
        
        $this->DB->get_object_by_id("Backlog", $id)->merge_values($this->Request->getParams_POST())->update();
        $this->Session->addFlashMessage("action", "Item updated!", 0);
        $this->redirect("admin/Backlog");
    }
    
    /**
    * Delete information from database and then redirect to list items.
    * $id: id_Backlog (identifier value)
    **/
    function delete($id)
    {
        $Backlog = $this->DB->get_object_by_id("Backlog", $id);
        $Backlog->delete();
        $this->Session->addFlashMessage("action", "Item deleted!", 0);
        $this->redirect("admin/Backlog");
    }
    
}
?>