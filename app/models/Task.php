<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@skylogix.net
 * @company Systems Development Company "skylogix" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */ 
class Task extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_task';
	
	var $id_task; 
	var $id_prioritytask; 
	var $id_typetask; 
	var $id_statustask; 
	var $id_sprint; 
	var $id_backlog; 
	var $starttime_task; 
	var $endtime_task; 
	var $title_task; 
	var $descr_task; 
	var $isextratime_task; 
	var $owner_task; 
	var $stimatehour_task; 
	var $workedhour_task; 
	var $continueof_task; 
	var $weight_task; 
	var $createat_task; 
	
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