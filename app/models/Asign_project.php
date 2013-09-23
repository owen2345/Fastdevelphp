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
class Asign_project extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_asign';
	
	var $id_asign; 
	var $id_role; 
	var $id; 
	var $id_project; 
	var $receivenotif_asign; 
	var $createat_asign; 
	
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