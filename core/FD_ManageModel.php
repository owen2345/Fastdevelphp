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
     * Guarda este objeto en la base de datos y retorn este mismo
    */
	function save($generateKeyObject = true)
	{
	    $FD = getInstance();
		$primary_key = $FD->Connection->DB->getPrimaryKey(get_class($this));
        $this->$primary_key = $FD->Connection->DB->save_object($this, $generateKeyObject);
        return $this;
	}
    
    /**
    * actualiza la informacion de este objeto en la base de detos y retorna este mismo
    */	
	function update()
	{
	    $FD = getInstance();
        $FD->Connection->DB->update_object($this);
        return $this;
	}
    
    /**
     * obtiene un arreglo de objetos childrens de este objeto, para casos de recursividad en una tabla
    */
    function getChildrens($name_attr_parent, $where = "")
    {
        $FD = getInstance();
        $primary_key = $this->getPrimaryKey();
        return $FD->Connection->DB->get_objects(get_class($this), " $name_attr_parent = ".$this->$primary_key . ($where?" and ".$where:""));
    }
    
    /**
     * crea un objeto $name_object con valores $array_valores y $name_object->$primary_key = valor del objeto actual
     * return nuevo objeto
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
     * combina los valores actuales del objeto con los valores enviados en $array_values
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
	 * obtiene los objetos con el nombre $table_name que se relacionan 
	 * con este objeto mediante su foreing key o mediante el nombre del atributo identificador presente en la tabla $table_name
     * asi mismo se puede hacer un filtro adicional con condicionales enviados en $where
	*/
	function references($table_name, $where = null, $order_by = null, $limit = null)
	{	
	    $FD = getInstance();		
        $primary_key = $FD->Connection->DB->getPrimaryKey(get_class($this));
		return $FD->Connection->DB->get_objects($table_name, " $primary_key = '".$this->$primary_key."'" . ($where?" and ".$where:""), $order_by, $limit);
	}
    
    /**
     * optiene un objeto de tipo $table_name mediante el foreing key de la tabla $table_name presente en este objeto o 
     * el nombre del atributo identificador de la tabla $table_name en el actual Objeto
    */
    function foreing_key($table_name, $where = null, $order_by = null, $limit = null)
	{
	    $FD = getInstance();
        $primary_key = $FD->Connection->DB->getPrimaryKey($table_name);
		return $FD->Connection->DB->get_object($table_name, " $primary_key = '".$this->$primary_key."'" . ($where?" and ".$where:""), $order_by, $limit);
	}
	
    /**
     * Elimina el objeto de la base de datos
     * retorn este mismo objeto
    */    
	function delete()
	{
	    $FD = getInstance();
		$FD->Connection->DB->delete_object($this);
        return $this;
	}
    
    /**
     * Elimina las filas en cascada, donde $attr_recursive tiene el valor del parent ID. 
    */
    function deleteCascade($attr_recursive)
    {
        $FD = getInstance();
        $primary_key = $this->getPrimaryKey();
        $Objs= $this->Connection->DB->get_objects(get_class($this), "$attr_recursive = ".$this->$primary_key);
        foreach($Objs as $Obj)
        {
            $Obj->deleteCascade($attr_recursive);
        }
        $FD->Connection->DB->delete_object($this);
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
     * find in alias_of_atributes Array that is contain at value equal to $alias.
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
     * retorn el nombre del atributo identificador del objeto
    */
    
    function getPrimaryKey()
    {
        $FD = getInstance();
        return $FD->Connection->DB->getPrimaryKey(get_class($this));
    }
    
    /**
     * actualiza el valor del atributo $attr al nuevo valor $val
     * retorn el mismo objeto
    */
    
    function setAttr($attr, $val = "")
    {
        $this->$attr = $val;
        return $this;
    }
    
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
    
    function printObject()
    {   
        $class = new ReflectionClass(get_class($this)); 
        $Res = array();
        foreach($class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $Attr)
        {
            $name = $Attr->name;
            if($name != "FD_ManageDB" && $name != "Connection" && $name != "FD_Validator" && $name != "attrs_modify")
                $Res["Attributes"][$Attr->name] = $this->$name;
        }
        
        foreach($class->getMethods(ReflectionProperty::IS_PUBLIC) as $Method)
        {
            $name = $Method->name;
            if($name != get_class($this) && $name != "__set" && $name != "__get")
                $Res["Methods"][$Method->name] = $this->$name;
        }
        
        $Res = array(get_class($this)=>(object)$Res);
        echo "<pre>";
        print_r(current($Res));
        echo "</pre>";
    }
}

?>