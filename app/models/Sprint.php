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
class Sprint extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_sprint';
	
	var $id_sprint; 
	var $id_typesprint; 
	var $id_project; 
	var $id_backlog; 
	var $type_sprint; 
	var $startdate_sprint; 
	var $enddate_sprint; 
	var $status_sprint; 
	var $createat_sprint; 
	var $title_sprint; 
	var $descr_sprint; 
	var $id_statussprint; 
	
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