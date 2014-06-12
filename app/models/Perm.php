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
class Perm extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_perm';
	
	var $id_perm; 
	var $date_perm; 
	var $descr_perm; 
	var $isapproved_perm; 
	var $answer_perm; 
	var $createat_perm; 
	var $id; 
	var $id_typeperm; 
	
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