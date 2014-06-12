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

class FD_ManageModel
{
	protected $attrs_modify= array();
    protected $FD_Validator;
    
    /**
     * $generateKeyObject: deprecado
     * Guarda este objeto en la base de datos
     * retorna: el objeto guardado
    */
	function save($generateKeyObject = true)
	{
	    $FD = getInstance();
        $primary_key = $this->fd_primary_key;
        $this->$primary_key = $FD->Connection->DB->save_object($this, $generateKeyObject);
        return $this;
	}
    
    /**
     * Actualiza este objeto en la base de datos
     * retorna: el objeto guardado
    */
	function update()
	{
	    $FD = getInstance();
        $FD->Connection->DB->update_object($this);
        return $this;
	}
    
    /** 
     * $name_attr_parent: nombre del atributo en el objeto parent
     * $where: condition sql para los childrens
     * retorna: los objetos children de este objeto, muy usado en recursividad
    */
    function getChildrens($name_attr_parent, $where = "")
    {
        $FD = getInstance();
        $primary_key = $this->fd_primary_key;
        return $FD->Connection->DB->get_objects(get_class($this), " $name_attr_parent = ".$this->$primary_key . ($where?" and ".$where:""));
    }
    
    /** 
     * crea un objeto $name_object con valores $array_valores y $name_object->$primary_key = valor del objeto actual
     * $name_object: nombre de la tabla
     * $array_valores: valores para el objeto
     * $postfix: postfijo para el nombre de los atributos en $array_valores, ejm: _user
     * retorna: el objeto creado
    */
    function create_object($name_object,$array_valores=array(), $postfix = "")
    {
        $FD = getInstance();
        $primary_key = $this->fd_primary_key;
        $res = $FD->Connection->DB->create_object($name_object, $array_valores, $postfix);
        $res->setAttr($primary_key, $this->$primary_key);
        return $res;
    }
    
    /** 
     * actualiza el objeto co valores $array_values
     * $array_values: nuevos valores para el objeto
     * retorna: el objeto actualizado
    */
	function merge_values($array_values=array())
	{
        $array_values = array_change_key_case($array_values, CASE_LOWER);
	    $FD = getInstance();
		$params = $FD->SQL->getFieldsTable(get_class($this));
		foreach($params as $key_param => $param)
		{
			if(key_exists($param, $array_values))
                $this->setAttr($param, $array_values[$param]);
		}
		return $this; //->ia no es necesario retornar, se puede usar el mismo objeto
	}
    
    /** 
     * $table_name: nombre de la tabla
     * $where: condition SQL
     * $order_by: name_attr ASC / name_attr DESC
     * $limit: limit sql. Ejm: 0,10  => los primeros 10
     * $attr_foreingkey: nombre del atributo (llave foranea) en la tabla $table_name para el objeto actual, 
     *                   por defecto es el indentificador del objeto actual
     * $this: objeto actual
     * retorna: los objetos $table_name, donde: $table_name->$attr_foreingkey = $this->primary_key y que cumpla $where
     *         ordenado por $order_by de cantidad $limit
    */
	function references($table_name, $where = null, $order_by = null, $limit = null, $attr_foreingkey = null)
	{	
	    $FD = getInstance();		
        if(!$attr_foreingkey)
            $attr_foreingkey = $this->fd_primary_key;
        $primary_key = $this->fd_primary_key;
		return $FD->Connection->DB->get_objects($table_name, " $attr_foreingkey = '".$this->$primary_key."'" . ($where?" and ".$where:""), $order_by, $limit);
	}
    
    /** 
     * $table_name: nombre de la tabla
     * $where: condition SQL
     * $order_by: name_attr ASC / name_attr DESC
     * $limit: limit sql. Ejm: 0,10  => los primeros 10
     * $attr_primarykey: nombre del atributo (llave foranea) de la tabla $table_name para el objeto actual, 
     *                   por defecto es el indentificador del objeto $table_name
     * $this: objeto actual
     * retorna: el objeto $table_name, donde: $table_name->primary_key = $this->$attr_primarykey y que cumpla $where
    */
    function foreing_key($table_name, $where = null, $attr_primarykey = null)
	{
	    $FD = getInstance();
        $primary_key = $FD->DB->create_object($table_name)->fd_primary_key;
        if(!$attr_primarykey)
            $attr_primarykey = $primary_key;
		return $FD->Connection->DB->get_object($table_name, " $primary_key = '".$this->$attr_primarykey."'" . ($where?" and ".$where:""));
	}
	
    /**
     * elimina el objeto actual de la DB 
     * retorna: el objeto eliminado - $this
    */   
	function delete()
	{
	    $FD = getInstance();
		$FD->Connection->DB->delete_object($this);
        return $this;
	}
    
    public function __set($name, $value) 
    {
        $name = strtolower($name);
        $this->$name = $value;
        $FD = getInstance();
        $Fields = $FD->SQL->getFieldsTable(get_class($this));
        if(!in_array($name, $this->attrs_modify) && in_array($name, $Fields))
            array_push($this->attrs_modify, $name);
    }

    public function __get($name) 
    {
        return $this->$name;
    }
    
    /**
     * retorna el nombre del atributo identificador del objeto
    */    
    function getPrimaryKey()
    {
        return $this->fd_primary_key;
        $FD = getInstance();
        return $FD->DB->getPrimaryKey(get_class($this));
    }
    
    /**
     * actualiza el valor del atributo $attr al nuevo valor $val
     * $attr: nombre del atributo en el modelo
     * $val: nuevo valor para este atributo
     * retorna el mismo objeto
    */    
    function setAttr($attr, $val = "")
    {
        $this->__set($attr, $val);
        return $this;
    }
    
    
    /**
     * $attr: nombre del atributo en el modelo
     * retorna el valor del atributo en este objeto
    */    
    function getAttr($attr)
    {
        return $this->__get($attr);
    }
    
    /**
     * verifica si el modelo cumple las reglas $fd_rules
     * retorna true: si el objeto cumple con todas las reglas, FALSE: si hay algun error en las reglas $fd_rules
    */  
    function isValid()
    {
        $class = new ReflectionClass(get_class($this));        
        if($class->hasProperty("fd_rules"))
        {
            $this->FD_Validator = new FD_FormValidator();
            foreach($this->fd_rules as $rule)
            {
                $this->FD_Validator->addValidation($rule[0],$rule[1],$rule[2]);
            }
            return $this->FD_Validator->isValid($this);
        }
    }
    
    /**
     * retorna los mensajes de error de la validacion generada por isValid()
    */ 
    function getValidateErrors()
    {
        $class = new ReflectionClass(get_class($this));        
        if($class->hasProperty("fd_rules"))
        {
            if($this->FD_Validator)
                return $this->FD_Validator->GetErrors();
            else
            {
                $this->isValid();
                return $this->FD_Validator->GetErrors();
            }
        }
    }
}

?>