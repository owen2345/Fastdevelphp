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
class Company_user extends FD_ManageModel
{	
    var $fd_rules = array();
    var $fd_primary_key = 'id';
	
	var $id; 
	var $id_company; 
	var $name; 
	var $email; 
	var $position; 
	var $username; 
	var $password; 
	var $admin; 
	var $enabled; 
	var $id_position; 
	var $celular; 
	var $telefono; 
	var $direccion; 
	var $fechainicio; 
	var $fechafin; 
	var $fecharenovado; 
	var $fnacimiento; 
	var $estadocivil; 
	var $observaciones; 
	var $descripcion; 
	var $sueldo; 
	var $enplanilla; 
	var $isclient; 
	var $avatar; 
	
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