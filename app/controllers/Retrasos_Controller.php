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
class Retrasos_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("fastdevelphp/backend");
	}
    
    function index()
    {
        $this->lists();
    }
    
    function lists()
    {
        
        $data['Company_users'] = $this->DB->get_objects('Company_user', '');
        $data['Status_retrasos'] = array( "0" => "No Pagado",  "1" => "Pagado",  "2" => "Por revisar");
        $data["Retrasoss"] = $this->DB->get_objects("Retrasos");        
        $this->loadView("Retrasos/index", $data);
    }
    
    function create()
    {
        
        $data['Company_users'] = $this->DB->get_objects('Company_user', '');
        $data['Status_retrasos'] = array( "0" => "No Pagado",  "1" => "Pagado",  "2" => "Por revisar");
        $data["Retrasos"] = $this->DB->create_object("Retrasos", array('date_retraso' => '', 'time_retraso' => 'Tiempo en minutos', 'descr_retraso' => ''));
        $data["action"] = "save";
        $data["submitText"] = "Registrar";        
        $this->loadView("Retrasos/form", $data);
    }
    
    function save()
    {
        
        $this->DB->create_object("Retrasos", $this->Request->getParams_POST())->save();
        $this->Session->addFlashMessage("action", "El item fue guardado!", 0);
        $this->redirect("Retrasos");
    }
    
    function edit($id)
    {
        
        $data['Company_users'] = $this->DB->get_objects('Company_user', '');
        $data['Status_retrasos'] = array( "0" => "No Pagado",  "1" => "Pagado",  "2" => "Por revisar");
        $data["Retrasos"] = $Retrasos = $this->DB->get_object_by_id("Retrasos", $id);
        $data["action"] = "update/$Retrasos->id_retraso";
        $data["submitText"] = "Actualizar";
        $this->loadView("Retrasos/form", $data);
    }
    
    function update($id)
    {
        
        $this->DB->get_object_by_id("Retrasos", $id)->merge_values($this->Request->getParams_POST())->update();
        $this->Session->addFlashMessage("action", "El item fue actualizado!", 0);
        $this->redirect("Retrasos");
    }
    
    function delete($id)
    {
        $Retrasos = $this->DB->get_object_by_id("Retrasos", $id);
        $Retrasos->delete();
        $this->Session->addFlashMessage("action", "El item fue eliminado!", 0);
        $this->redirect("Retrasos");
    }
    
}
?>