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
class Company_Controller extends FD_Management
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
        
        $data['Enableds'] = array( "0" => "Enable",  "1" => "Disable");
        $data["Companys"] = $this->DB->get_objects("Company");        
        $this->loadView("admin/Company/index", $data);
    }
    
    /**
    * show create form view
    **/
    function create()
    {
        
        $data['Enableds'] = array( "0" => "Enable",  "1" => "Disable");
        $data["Company"] = $this->DB->create_object("Company", array('name' => 'Here default value', 'email' => 'Here default value', 'address' => 'Here default value', 'phone' => 'Here default value'));
        $data["action"] = "save";
        $data["submitText"] = "Registrar";        
        $this->loadView("admin/Company/form", $data);
    }
    
    /**
    * save information into database and then redirect to list items.
    **/
    function save()
    {
        
        $this->DB->create_object("Company", $this->Request->getParams_POST())->save();
        $this->Session->addFlashMessage("action", "El item fue guardado!", 0);
        $this->redirect("admin/Company");
    }
    
    /**
    * show edit form view
    * $id: id_Company (identifier value)
    **/
    function edit($id)
    {
        
        $data['Enableds'] = array( "0" => "Enable",  "1" => "Disable");
        $data["Company"] = $Company = $this->DB->get_object_by_id("Company", $id);
        $data["action"] = "update/$Company->id";
        $data["submitText"] = "Actualizar";
        $this->loadView("admin/Company/form", $data);
    }
    
    /**
    * save changes in to database and then redirect to list items.
    * $id: id_Company (identifier value)
    **/
    function update($id)
    {
        
        $this->DB->get_object_by_id("Company", $id)->merge_values($this->Request->getParams_POST())->update();
        $this->Session->addFlashMessage("action", "El item fue actualizado!", 0);
        $this->redirect("admin/Company");
    }
    
    /**
    * Delete information from database and then redirect to list items.
    * $id: id_Company (identifier value)
    **/
    function delete($id)
    {
        $Company = $this->DB->get_object_by_id("Company", $id);
        $Company->delete();
        $this->Session->addFlashMessage("action", "El item fue eliminado!", 0);
        $this->redirect("admin/Company");
    }
    
}
?>