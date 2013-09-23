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
class Project extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_project';
	
	var $id_project; 
	var $id_statusproj; 
	var $id_projectdev; 
	var $id_money; 
	var $id_client; 
	var $id_backlog; 
	var $urlpresupuesto_project; 
	var $cost_project; 
	var $startdate_project; 
	var $endate_project; 
	var $title_project; 
	var $descr_project; 
	var $estimation_project; 
	var $status_project; 
	var $createat_project; 
	var $iduser_contable; 
	var $taskclient_project; 
	
    /**
    //function executed before to save object.
    function onSave(){    }*/
    
    /**
    //function executed after to save object.
    function afterSave(){     }*/
    
    /**
    //function executed before to update object.
    function onUpdate(){    }*/
    
    /**
    //function executed after to update object.
    function afterUpdate(){     }*/
    
    /**
    //function executed before to delete object.
    function onDelete(){    }*/
    
    /**
    //function executed after to delete object.
    function afterDelete(){     }*/
}
?>