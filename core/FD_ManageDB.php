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
            $object->attrs_modify = array();			
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
        {
            $object = $this->create_object($name_object, $objects_db[0]);
            $object->attrs_modify = array();
            return $object;
        }
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
        {
            $r->attrs_modify = array();
            return $r;
        }
            
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
		$object = $this->get_object($name_object, "$primary_key = '$id' ");
        return $object;
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
        if($object)
            $object->attrs_modify = array();		
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
        $FD = getInstance();
        $FD->loadModel($name_object);
		$object = new $name_object;
		if($array_valores)
		{
            $params = $FD->SQL->getFieldsTable($name_object);
			foreach($params as $key_param => $param)
			{
                if(key_exists($param, $array_valores))
                    $object->setAttr($param, $array_valores[$param]);
			}
		}		
		return $object;
	}
	
	/**
     * funcion llamada por los modelos.
     * $generateKeyObject: deprecated
	 */
	function save_object($object, $generateKeyObject = true)
	{
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($object));		
		$table_name=get_class($object);
        //$params = $FD->SQL->getFieldsTable($table_name);		
		$vals_object=array();
		$names_atributos=array();
		if($class->hasMethod("onSave"))
            $object->onSave();
        
        //foreach($params as $key_param => $param)
        foreach($object->attrs_modify as $param)
		{
            $names_atributos[] = $param;
            $vals_object[] = "'".$object->getAttr($param)."'";
		}
        $consulta = "INSERT INTO ".strtolower($table_name).
			" (".implode(", ", $names_atributos).") VALUE(".implode(",", $vals_object).")";
		$FD->Connection->SQL->execQuery($consulta);
        $primary_key = $object->fd_primary_key;
        $object->setAttr($primary_key, mysql_insert_id());
        
        if($class->hasMethod("afterSave"))
            $object->afterSave();
		
        return $object->getAttr($primary_key);
	}
	
	/**
     * funcion llamada por los modelos.
	 */
	function update_object($object)
	{
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($object));
		$table_name=get_class($object);
        //$params = $FD->SQL->getFieldsTable($table_name);
		$vals_object=array();
        if($class->hasMethod("onUpdate"))
            $object->onUpdate();
		//foreach($params as $key_param => $param)
        foreach($object->attrs_modify as $param)
		{
            if(is_null($object->getAttr($param)))
                $vals_object[] = "$param = null";
            else
                $vals_object[] = " $param = '".(str_replace(array("\\", "'"),array("\\\\", "\'"), $object->getAttr($param)))."'";
		}
		$condicion="";
        $key = $object->fd_primary_key;
        $condicion==""?$condicion=$key." = ".$object->$key:$condicion.= " and ".$key." = ".$object->$key;
					
		$consulta="UPDATE ".strtolower($table_name)." SET ".implode(", ", $vals_object)." WHERE ".$condicion;        
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
		$condicion="";
        if($class->hasMethod("onDelete"))
            $object->onDelete();
        $key = $object->fd_primary_key;
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
        $name_object = ucwords($name_object);
        $FD = getInstance();
        $FD->loadModel($name_object);
        $a = new $name_object();
        return $a->fd_primary_key;
    }
}
?>