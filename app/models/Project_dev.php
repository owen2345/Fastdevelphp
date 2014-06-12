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
class Project_dev extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id_projectdev';
	
	var $id_projectdev; 
	var $versionurl_projectdev; 
	var $serverdev_projectdev; 
	var $serverprod_projectdev; 
	var $credentials_projectdev; 
	
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