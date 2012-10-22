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
 
class FD_ManageDB
{    
    private $models = array();
    /**
	 * $name_object: nombre de la tabla
	 * $sql: condition sql
	 * return: un arreglo de objetos de tipo $name_object
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
    
    /**
     * $name_object: nombre de tabla
     * $where: condition sql
     * Retorna la cantidad de objetos del tipo $name_object que cumplan la condicion $where
     * */
    function countObjects($name_object, $where = "")
    {
        $FD = getInstance();
        $primary_key = $this->getPrimaryKey($name_object);
        $FD->Connection->SQL->execQuery("select count($primary_key) from ".strtolower($name_object)." ".($where?" where ".$where:""));
        $res= $FD->Connection->SQL->getArrayResultsQuery();
        return $res[0][0];
    }
	
	/**
	 * $name_object: nombre de tabla
	 * $where: condition sql
	 * $order_by: name_attr ASC / name_attr DESC
     * $limit: limit sql. Ejm: 0,10  => los primeros 10
     * Retorna los objetos de tipo $name_object que cumplan la condicion $where ordenado por $order_by la cantidad $limit
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
    
    /**
     * $name_object: nombre de tabla
     * $where: condition sql
     * $order_by: name_attr ASC / name_attr DESC
     * Retorna el ultimo objeto de tipo $name_object que cumplan la condicion $where ordenado por $order_by
     * */
    function get_last_object($name_object, $where="", $order_by = "")
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
    
    /**
     * $name_object: nombre de tabla
     * $where: condition sql
     * $order_by: name_attr ASC / name_attr DESC
     * Retorna el primer objeto de tipo $name_object que cumplan la condicion $where ordenado por $order_by
     * */
    function get_first_object($name_object, $where = "", $order_by = "")
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
	 * $name_object: nombre de la tabla
     * $id: id del objeto
     * retorna: un objeto de tipo $name_object
	 */	
	function get_object_by_id($name_object, $id="")
	{
		$primary_key = $this->getPrimaryKey($name_object);
        if(!$primary_key)        
            dieFastDevel("Debe indicar el primary key para '".ucwords($name_object)."'. <br>");
		return $this->get_object($name_object, "$primary_key = '$id' ");
	}
    
    /**
	 * $name_object: nombre de la tabla
     * $id: id del objeto
     * $name_attr: nombre del atributo
     * $val_attr: valor del atributo
     * retorna: el primer objeto de tipo $name_object con atributo $name_attr = $val_attr
	 */		
	function get_object_by_attribute($name_object, $name_attr, $val_attr = "")
	{
		return $this->get_object($name_object, "$name_attr = '$val_attr'");
	}
    
    /**
	 * $name_object: nombre de la tabla
     * $id: id del objeto
     * $name_attr: nombre del atributo
     * $val_attr: valor del atributo
     * retorna: los objetos de tipo $name_object con atributo $name_attr = $val_attr
	 */			
	function get_objects_by_attribute($name_object, $name_attr, $val_attr = "")
	{
		return $this->get_objects($name_object, "$name_attr = '$val_attr'");
	}
	
	/**
     * $name_object: nombre de la tabla
     * $where: condicion sql
     * $order_by: name_attr ASC / name_attr DESC
     * $limit: limit sql. Ejm: 0,10  => los primeros 10
     * retorna: el primer objeto de tipo $name_object que cumple condicion $where ordenado por $order_by, si el objeto no existe retorna false
	 */
	function get_object($name_object, $where="", $order_by = "")
	{
	    $FD = getInstance();
		$FD->Connection->SQL->execQuery("select * from ".strtolower($name_object)." ".($where?" where ".$where:""). " ".($order_by?" order by ".$order_by:""));
		$objects_db=$FD->Connection->SQL->getResultsQuery();
		$object_row=current($objects_db);
		$object = $this->create_object($name_object,$object_row);		
		if($object_row)
            return $object;
        else
            false;
	}
	
	/**
     * $name_object: nombre de la tabla
     * $array_valores: valores para el objeto
     * $postfix: postfijo para el nombre de los atributos en $array_valores, ejm: _user
     * retorna: el objeto $name_object con los valores $array_valores
	 */
	function create_object($name_object,$array_valores=array(), $postfix = "")
	{
        $this->checkModel($name_object);
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
    
    protected function getAttrVal($name_atribut, $array_valores, $alias_attrs)
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
     * funcion llamada por los modelos.
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
     * funcion llamada por los modelos.
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
        $key = $this->getPrimaryKey($table_name);
        $condicion==""?$condicion=$key." = ".$object->$key:$condicion.= " and ".$key." = ".$object->$key;
					
		$consulta="UPDATE ".strtolower($table_name)." SET $vals_object WHERE ".$condicion;        
		$FD->Connection->SQL->execQuery($consulta);
        if($class->hasMethod("afterUpdate"))
            $object->afterUpdate();
	}
	
	/**
     * funcion llamada por los modelos.
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
        $key = $this->getPrimaryKey($table_name);
        $condicion==""?$condicion=$key." = ".$object->$key:$condicion.= " and ".$key." = ".$object->$key;
		$consulta="DELETE FROM ".strtolower($table_name)." WHERE $condicion";
		$FD->Connection->SQL->execQuery($consulta);
        if($class->hasMethod("afterDelete"))
            $object->afterDelete();
	}
    
    /**
     * funcion llamada por los modelos.
	 */
    function getPrimaryKey($name_object)
    {
        $this->checkModel($name_object);
        $name_object = ucwords($name_object);
        $FD = getInstance();
        
        $class = new ReflectionClass($name_object);
        if($class->hasProperty("fd_primary_key"))
            return $class->getProperty("fd_primary_key")->getValue();
        
        $key = $FD->Connection->SQL->getKeysTable($name_object);
        if($key)
            return $key;
        else
            dieFastDevel("No existe la tabla '".$name_object."'. <br>");        
    }
    
    private function checkModel($name_object)
    {
        $FD = getInstance();
        if(in_array(strtolower($name_object), $FD->SQL->tables))
            return true;
        else
            dieFastDevel("No existe el modelo '".$name_object."'. <br>");
    }
}

?>