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
		$primary_key = $FD->Connection->DB->getPrimaryKey(get_class($this));
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
        $primary_key = $this->getPrimaryKey();
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
        $primary_key = $this->getPrimaryKey();
        $res = $FD->Connection->DB->create_object($name_object, $array_valores, $postfix);
        $res->$primary_key = $this->$primary_key;
        return $res;
    }
    
    /** 
     * actualiza el objeto co valores $array_values
     * $array_values: nuevos valores para el objeto
     * retorna: el objeto actualizado
    */
	function merge_values($array_values=array())
	{
	    $FD = getInstance();
		$class = new ReflectionClass(get_class($this));				
		$method=$class->getConstructor();
		$params=$method->getParameters();
        $alias_attrs = array(); 		    
        if($class->hasProperty("alias_of_atributes"))
            $alias_attrs = $this->alias_of_atributes;
		foreach($params as $key_param => $param)
		{
			$name_atribut=$param->getName();
            $name_attr = $FD->Connection->DB->getAttrVal($name_atribut, $array_values, $alias_attrs);  
            if($name_attr)            
                $this->$name_atribut=$array_values[$name_attr];
            if(!in_array($name_atribut, $this->attrs_modify))
                array_push($this->attrs_modify, $name_atribut);
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
            $attr_foreingkey = $this->getPrimaryKey();
        $primary_key = $FD->Connection->DB->getPrimaryKey(get_class($this));
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
        $primary_key = $FD->Connection->DB->getPrimaryKey($table_name);
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
        $name = $this->getAliasAttrName($name);
        $this->$name = $value;
        if(!in_array($name, $this->attrs_modify))
            array_push($this->attrs_modify, $name);
    }

    public function __get($name) 
    {
        $name = $this->getAliasAttrName($name);
        $class = new ReflectionClass(get_class($this));        
        if($class->hasProperty($name))
            return $this->$name;
        else
            dieFastDevel("Doesn't exist attribute \"$name\" for model \"".get_class($this)."\"");
    }
    
    /**
     * busca en $alias_of_atributes el key que es igual a $alias.
     * return attr_db_name.
    */
    protected function getAliasAttrName($alias)
    {
        $class = new ReflectionClass(get_class($this));        
        if($class->hasProperty("alias_of_atributes"))
        {
            $aliases = array_keys($this->alias_of_atributes, $alias);
            if(count($aliases))
                return $aliases[0];
            else
                return $alias;
        }
        return $alias;
    }
    
    /**
     * retorna el nombre del atributo identificador del objeto
    */    
    function getPrimaryKey()
    {
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
        $this->$attr = $val;
        return $this;
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