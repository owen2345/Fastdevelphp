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
class Retrasos extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_retraso';
	
	var $id_retraso; 
	var $id; 
	var $date_retraso; 
	var $time_retraso; 
	var $descr_retraso; 
	var $status_retraso; 
	
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