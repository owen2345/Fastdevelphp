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
class Log extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_log';
	
	var $id_log; 
	var $id; 
	var $type_log; 
	var $descr_log; 
	var $title_log; 
	var $createat_log; 
	var $value_log; 
	var $action_log; 
	var $id_project; 
	var $id_sprint; 
	var $id_task; 
	
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