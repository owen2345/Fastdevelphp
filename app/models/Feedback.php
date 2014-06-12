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
class Feedback extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_feedback';
	
	var $id_feedback; 
	var $id_project; 
	var $id_task; 
	var $id; 
	var $id_typefeedback; 
	var $title_feedback; 
	var $descr_feedback; 
	var $status_feedback; 
	var $createat_feedback; 
	var $idparent_feedback; 
	var $includeclient_feedback; 
	
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