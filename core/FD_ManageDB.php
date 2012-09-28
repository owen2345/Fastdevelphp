<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */
 
class FD_ManageDB
{    
    /**
	 * Crea objetos de tipo $name_object a partir de la ejecucion de la consulta $sql.
	 * $name_object: String nombre del obejto modelo
	 * $sql: String Query
	 * return: Array de objetos de tipo $name_object
	 */	
    function get_objects_By_Sql($name_object, $sql)
    {
        $FD = getInstance();
        $FD->Connection->SQL->execQuery($sql);
		$objects_db=$FD->Connection->SQL->getResultsQuery();
		$objects=array();
		foreach($objects_db as $object_row)
		{
			$object = $this->create_object($name_object, $object_row);			
			array_push($objects,$object);
		}
		return $objects;
    }
    
    function countObjects($name_object, $where = "")
    {
        $FD = getInstance();
        $primary_key = $this->getPrimaryKey($name_object);
        $FD->Connection->SQL->execQuery("select count($primary_key) from ".strtolower($name_object)." ".($where?" where ".$where:""));
        $res= $FD->Connection->SQL->getArrayResultsQuery();
        return $res[0][0];
    }
	
	/**
	 * Create objects  of type $name_object width datas extract from data base.
	 * Parameters:
	 * $name_object -> String Table name of data base or class name of a model.
	 * $where         -> String Conditions for filter objects from data base.
	 * 
	 * Return:         return an array objects of type  $name_object.
	 */
	function get_objects($name_object, $where="", $order_by = "", $limit = "")
	{
	    $FD = getInstance();
		$FD->Connection->SQL->execQuery("select * from ".strtolower($name_object)." ".($where?" where ".$where:""). " ".($order_by?" order by ".$order_by:"")." ".($limit?" limit ".$limit:""));
		$objects_db=$FD->Connection->SQL->getResultsQuery();
		$objects=array();
		foreach($objects_db as $object_row)
		{
			$object = $this->create_object($name_object, $object_row);			
			array_push($objects,$object);
		}
		return $objects;
	}
    
    function get_last_object($name_object, $where="", $order_by = "", $limit = "")
	{
	    $FD = getInstance();
		$primary_key = $this->getPrimaryKey($name_object);
        $FD->Connection->SQL->execQuery("select * from ".strtolower($name_object)." ".($where?" where ".$where:""). " ".($order_by?" order by ".$order_by:"")." order by $primary_key DESC".($limit?" limit ".$limit:" limit 0,1"));
		$objects_db=$FD->Connection->SQL->getResultsQuery();
        if(count($objects_db))
            return $this->create_object($name_object, $objects_db[0]);
        else
            return false;		
	}
    
    function get_first_object($name_object, $where="", $order_by = "", $limit = "")
	{
		$r = $this->get_object($name_object, $where, $order_by, "0,1");
        if($r)
            return $r;
        elseif($name_object == "images")
            return $this->create_object($name_object)->url_image = "default.jpg";
        else
            return $r;
	}
	
	/**
	 * Create objects  of type $name_object width datas extract from data base.
	 * Parameters:
	 * $name_object -> String Table name of data base or class name of a model.
	 * $where         -> String Conditions for filter objects from data base.
	 * 
	 * Return:         return an array objects of type  $name_object.
	 */
	function get_objects_children($name_object, $where="", $childrens = array())
	{
	    $FD = getInstance();
		$FD->Connection->SQL->execQuery("select * from ".strtolower($name_object)." ".($where?" where ".$where:""));
		$objects_db=$FD->Connection->SQL->getResultsQuery();
		$objects=array();
		foreach($objects_db as $object_row)
		{
			$object = $this->create_object($name_object, $object_row);
			$childrens_object = array();			
			foreach($childrens as $children)
			{				
                $primary_key = $this->getPrimaryKey(get_class($object));
				$childrens_object[$children]=$this->get_objects($children, "$primary_key=".$object->$primary_key);
			}
			$object->objects_children = $childrens_object; 
			array_push($objects,$object);
		}
		return $objects;
	}
	
	/**
	 * Create object of type $name_object width datas extract from data base.
	 * Parameters:
	 * $name_object -> String Table name of data base or class name of a model.
	 * $id            -> Number Identifier of the $name_object object into data base.
	 * 
	 * Return:         return an object of type  $name_object.
	 */	
	function get_object_by_id($name_object, $id="")
	{
		$primary_key = $this->getPrimaryKey($name_object);
        if(!$primary_key)        
            dieFastDevel("Debe indicar el primary key para '".ucwords($name_object)."'. <br>");
		return $this->get_object($name_object, "$primary_key = '$id' ");
	}
    
    /** 
	 * Return:         return an object of type  $name_object, filtered by attribute $name_attr.
	 */	
	function get_object_by_attribute($name_object, $name_attr, $val_attr = "")
	{
		return $this->get_object($name_object, "$name_attr = '$val_attr'");
	}
    
    /** 
	 * Return:         return an array of objects of type  $name_object, filtered by attribute $name_attr.
	 */	
	function get_objects_by_attribute($name_object, $name_attr, $val_attr = "")
	{
		return $this->get_objects($name_object, "$name_attr = '$val_attr'");
	}
	
	/**
	 * Create object of type $name_object width datas extract from data base.
	 * Parameters:
	 * $name_object -> String Table name of data base or class name of a model.
	 * $where         -> String Conditions for filter object from data base.
	 * 
	 * Return:         return an object of type  $name_object, 
	 *                 when this object is empty => the return value is false .
	 */
	function get_object($name_object, $where="", $order_by = "", $limit = "")
	{
	    $FD = getInstance();
		$FD->Connection->SQL->execQuery("select * from ".strtolower($name_object)." ".($where?" where ".$where:""). " ".($order_by?" order by ".$order_by:"")." ".($limit?" limit ".$limit:""));
		$objects_db=$FD->Connection->SQL->getResultsQuery();
		$object_row=current($objects_db);
		$object = $this->create_object($name_object,$object_row);		
		if($object_row)
            return $object;
        else
            false;
	}
	
	/**
	 * Create object of type $name_object width datas that have send in $array_valores.
	 * Parameters:
	 * $name_object -> String class name of a model.
	 * $array_valores -> Array Values width the witch is filled the attributes of this object.
	 * 
	 * Return:         return an object of type  $name_object.
	 * 
	 * Note: 		   This array can have keys that are the names of attributes of the model. 
	 * sample: 		   array('name'=>'owen', "app"=>"peredo").
	 */
	function create_object($name_object,$array_valores=array(), $postfix = "")
	{	
	    $FD = getInstance();
		$object = new $name_object;
		if($array_valores)
		{
		    $class = new ReflectionClass($name_object);
            $alias_attrs = array(); 		    
            if($class->hasProperty("alias_of_atributes"))
                $alias_attrs = $object->alias_of_atributes;
						
			$method=$class->getConstructor();
			$params=$method->getParameters();
			foreach($params as $key_param => $param)
			{
				$name_atribut=$param->getName();
                $name_attr = $this->getAttrVal($name_atribut, $array_valores, $alias_attrs);  
                if($name_attr)
                    $object->$name_atribut=$array_valores[$postfix?$name_attr.$postfix:$name_attr];
			}
		}		
		return $object;
	}
    
    function getAttrVal($name_atribut, $array_valores, $alias_attrs)
    {
        if(array_key_exists($name_atribut,$array_valores))
            return $name_atribut;
        elseif(array_key_exists(strtoupper($name_atribut),$array_valores))
            return strtoupper($name_atribut);
        if(array_key_exists($name_atribut, $alias_attrs))
        {
            $name_atribut = $alias_attrs[$name_atribut];
            if(array_key_exists($name_atribut,$array_valores))
                return $name_atribut;
            elseif(array_key_exists(strtoupper($name_atribut),$array_valores))
                return strtoupper($name_atribut);
        }
        return "";
            
    }
	
	/**
	 * Save the attribute value of a Object into data base in the table 
	 * with equal name to class_name of this object.
	 * 
	 * Return:         return $object.
	 */
	function save_object($object, $generateKeyObject = true)
	{
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($object));		
		$table_name=get_class($object);
		$method=$class->getConstructor();
		$params=$method->getParameters();		
		$vals_object="";
		$names_atributos="";
        $primary_key = $this->getPrimaryKey($table_name);
		if($generateKeyObject)
            $object->$primary_key = $FD->Connection->SQL->getNewID($table_name, $primary_key);        		
		if($class->hasMethod("onSave"))
            $object->onSave();
        if($class->hasProperty("before_save_functions"))
            foreach($object->before_save_functions as $function)
            {
                $object->$function();
            }
        foreach($params as $key_param => $param)
		{
			$name_atribut=$param->getName();
			// if this attribute have modify of before value = '' or has default value distinct of ''
			if($object->$primary_key == "autoincrement" && $name_atribut == $primary_key){}
            elseif(array_key_exists($name_atribut, $object->attrs_modify) || $object->$name_atribut !="")			
				{
				    if(is_null($object->$name_atribut))	
                        $val="NULL";
                    else
				        $val="'".(str_replace(array("\\", "'"),array("\\\\", "\'"), $object->$name_atribut))."'";				    
					$names_atributos?$names_atributos.=",".$name_atribut:$names_atributos.=$name_atribut;
					$vals_object ? $vals_object.=",".$val:$vals_object.=$val;
				}elseif(is_null($object->$name_atribut))
                {
                    $names_atributos?$names_atributos.=",".$name_atribut:$names_atributos.=$name_atribut;
					$vals_object ? $vals_object.=",null":$vals_object.="null";
                }
		}
		$FD->Connection->SQL->execQuery("INSERT INTO ".strtolower($table_name).
			" ($names_atributos) VALUE(".$vals_object.")");
        if($object->$primary_key == "autoincrement")
        	$object->$primary_key=mysql_insert_id();
        if($class->hasMethod("afterSave"))
            $object->afterSave();
		return $object->$primary_key;
	}
	
	/**
	 * Update the attribute value of a Object into data base in the table 
	 * with equal name to class_name of this object.
	 * 
	 * Return:         return void.
	 */	
	function update_object($object)
	{
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($object));
		$table_name=get_class($object);
		$method=$class->getConstructor();
		$params=$method->getParameters();
		$vals_object="";
        if($class->hasMethod("onUpdate"))
            $object->onUpdate();
		foreach($params as $key_param => $param)
		{
			$name_atribut=$param->getName();
            if(in_array($name_atribut, $object->attrs_modify) || $object->$name_atribut !="" || is_numeric($object->$name_atribut))
			{
				//if($object->$name_atribut===NULL)
                if(is_null($object->$name_atribut))	
                    $val="NULL";
                else
                    $val="'".(str_replace(array("\\", "'"),array("\\\\", "\'"), $object->$name_atribut))."'";
				$vals_object?$vals_object.=",".$name_atribut."=$val":$vals_object.="".$name_atribut."=$val";
			
            }elseif(is_null($object->$name_atribut))
            {
                $vals_object?$vals_object.=",".$name_atribut."=null":$vals_object.="".$name_atribut."=null";
            }
				
		}
		$condicion="";
		foreach($FD->Connection->SQL->foreing_kes[$table_name] as $key)
		{
			$condicion==""?$condicion=$key." = ".$object->$key:$condicion.= " and ".$key." = ".$object->$key;
		}
					
		$consulta="UPDATE ".strtolower($table_name)." SET $vals_object WHERE ".$condicion;        
		$FD->Connection->SQL->execQuery($consulta);
        if($class->hasMethod("afterUpdate"))
            $object->afterUpdate();
	}
	
	/**
	 * Delete row of data base with equal table name to object clas_name and 
	 * identifier is equal to object_identifier.
	 * 
	 * Return:         return void.
	 */
	function delete_object($object)
	{ 
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($object));
		$table_name=get_class($object);
		$method=$class->getConstructor();
		$params=$method->getParameters();
		$condicion="";
        if($class->hasMethod("onDelete"))
            $object->onDelete();
		foreach($FD->Connection->SQL->foreing_kes[$table_name] as $key)
		{
			$condicion==""?$condicion=$key." = ".$object->$key:$condicion.= " and ".$key." = ".$object->$key;
		}		
		$consulta="DELETE FROM ".strtolower($table_name)." WHERE $condicion";
		$FD->Connection->SQL->execQuery($consulta);
        if($class->hasMethod("afterDelete"))
            $object->afterDelete();
	}
    
    function getPrimaryKey($name_object)
    {
        $FD = getInstance();
        if(key_exists(ucwords($name_object), $FD->Connection->SQL->foreing_kes))        
            return $FD->Connection->SQL->foreing_kes[ucwords($name_object)][0];
        else
            dieFastDevel("No existe la tabla '".ucwords($name_object)."'. <br>");        
    }
}

?>