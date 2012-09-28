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

class FD_Scafold
{
    var $Connection;
    var $datas;
    var $controller_dir = 'controllers';
    var $view_dir = 'views';
    var $url_module = "<?php echo ROOT_PATH ?>";
    var $module_name = '';
    function FD_Scafold($tableName, $datas, $Connection, $simple = true, $module_name = null)
    {
        $this->Connection = $Connection;
        $this->datas = $datas;
        $className = ucwords($tableName);
        $this->url_module = $className;
        $this->module_name = $module_name;
        if($module_name)
        {
            $this->controller_dir .= '/'.$module_name;
            $this->view_dir .= '/'.$module_name;
            if(!file_exists("../".$this->controller_dir))
                mkdir("../".$this->controller_dir, 0777); 
            if(!file_exists("../".$this->view_dir))
                mkdir("../".$this->view_dir, 0777); 
            $this->url_module = "$module_name/".$this->url_module;              
        }
                
        /*****/        
        if($simple)
            $this->FD_createController_scaffold_simple($className);
        else
            $this->FD_createController_scaffold($className);
        
        $this->FD_createViews_scaffold($className, $simple);
        /**********/
        echo "<a href='".ROOT_PATH.$this->url_module."'>Ir a listado</a>";
    }
        
    function FD_createViews_scaffold($className, $simple = true)
    {
        if(!file_exists("../".$this->view_dir."/$className"))
            mkdir("../".$this->view_dir."/$className", 0777);
        $this->FD_createFormView($className);
        
        if($simple)
            $this->FD_createListView_simple($className);
        else        
            $this->FD_createListView($className);
    }
    
    function FD_createFormView($className)
    {
        $Object = $this->Connection->DB->create_object($className);        
        $primaryKey = $Object->getPrimaryKey();
        if(file_exists("../".$this->view_dir."/".$className.'/form.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/form.php\" view already exist.<br>";
            return;
        }
        
        $inputs = "";
        $encitype = "";
        foreach($this->datas as $attrName => $type)
        {            
            switch(strtolower($type))
            {
                case "textarea":
                    $typeHtml = "<textarea name='$attrName' class='' ><?php echo \$".$className."->$attrName ?></textarea>";
                break;
                
                case "select":
                    $typeHtml = "<select name='$attrName'> <option value=''> Select one option</option> </select>";
                break;                
                
                case "file":
                    $encitype = "multipart/form-data";
                    $typeHtml = "<input type='file' name='$attrName' class='' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
                
                case "hidden":
                    $typeHtml = "<input type='hidden' name='$attrName' class='' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
                
                case "checkbox":
                    $typeHtml = "<input type='checkbox' name='$attrName' <?php echo \$".$className."->$attrName?\"checked=''\":\"\" ?> class='' value='1' />";
                break;
                
                default:
                    $typeHtml = "<input type='text' name='$attrName' class='' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
            }
            
            $inputs .= "<li>
                            <label>".ucwords($attrName)."</label>
                            $typeHtml
                        </li>";
        }
        
        $fp = fopen("../".$this->view_dir."/".$className.'/form.php',"a+");
fwrite($fp,"
        <div class='form_register' id='form_$className'>
            <h2>Formulario $className</h2>
            <form method='post' action='<?php echo ROOT_PATH ?>".$this->url_module."/<?php echo \$action ?>' enctype='$encitype'>
                <input type='hidden' name='$primaryKey' value='<?php  echo \$".$className."->$primaryKey ?>' />
                <ul>
                    $inputs
                    <li class='controls'>
                        <button type='submit'><?php echo \$submitText ?></button>
                        <button type='button' onclick='history.back();'>Cancelar</button>
                    </li>            
                </ul>
            </form>
        </div>");
        fclose($fp);
        
        echo "<br> Vista \"".$this->view_dir.'/'.$className."/form.php\" creada! <br>";
    }
    
    function FD_createListView($className)
    {
        if(file_exists("../".$this->view_dir."/".$className.'/index.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/index.php\" view already exist.<br>";
            return;
        }
        
        $object = $this->Connection->DB->create_object("$className");        
        $primaryKey = $object->getPrimaryKey();
        $class = new ReflectionClass(get_class($object));		
		$table_name=get_class($object);
		$method=$class->getConstructor();
		$params=$method->getParameters();		
		$vals_object="";
		$names_atributos="";
        //reset($this->Connection->foreing_kes[$table_name]);
        $row_titles = "";
        $row_body = "";
        foreach($params as $key_param => $param)
		{
			$name_atribut=$param->getName();
			if($name_atribut != $primaryKey)
                $row_titles .= "<th class='<?php echo \$sort_order_by=='$name_atribut'?'sorted':'' ?>'> <a href='<?php echo ROOT_PATH ?>$className/lists/$name_atribut/<?php echo \$sort_dir=='ASC'?'DESC':'ASC' ?>/<?php echo \$start_pag ?>/<?php echo \$current_pag?>'>".ucwords($name_atribut)."</a></th>\n                    ";
                /*$row_titles .= "<th> <a href='<?php echo ROOT_PATH ?>$className/index/$name_atribut/<?php echo \$sort_dir=='ASC'?'DESC':'ASC' ?>'>".ucwords($name_atribut)."</a></th>";*/
		}
        
        foreach($params as $key_param => $param)
		{            
			$name_atribut=$param->getName();
			if($name_atribut != $primaryKey)
                $row_body .= " <td><?php  echo \$".$className."->$name_atribut ?></td>\n";
		}
        
        $fp = fopen("../".$this->view_dir."/".$className.'/index.php',"a+");
fwrite($fp," 
        <div class='panel_listado'>  
            <h1>Lista de $className</h1>            
            <table id='listado_$className'>
                <tr>
                    $row_titles
                    <th>Actions</th>
                </tr>
                <?php  if(count(\$".$className."s)): ?>
                    <?php  foreach(\$".$className."s as \$".$className."): ?>
                        <tr>
                            $row_body
                            <td class='actions'>
                                <a href='<?php echo ROOT_PATH ?>".$this->url_module."/edit/<?php echo \$".$className."->$primaryKey ?>' class='editar'>Editar</a>
                                <a href='<?php echo ROOT_PATH ?>".$this->url_module."/delete/<?php echo \$".$className."->$primaryKey ?>' onclick=\"var d = confirm('Esta seguro de eliminar este Item?'); return d;\" class='eliminar'>Eliminar</a>
                            </td>
                        </tr>
                    <?php  endforeach ?>
                <?php  else: ?>
                <tr>
                    <td colspan='99'>No existen $className registradas</td>
                </tr>
                <?php  endif ?>
            </table>
            <div class='panel_paginator'>
                <?php echo \$this->Utility->create_paginator(\$total_items, \$per_page, \$current_pag, ROOT_PATH.\"".$this->url_module."/lists/\$sort_order_by/\$sort_dir\"); ?>
            </div>
            <div class='panel_controls'>
                <a id='btn_registrar' href='<?php echo ROOT_PATH ?>".$this->url_module."/create'>Registrar nuevo</a>
            </div>
        </div>
            ");
        fclose($fp);
        echo "<br> Vista \"".$this->view_dir."/".$className."/index.php\" creada! <br>";
    }
    
    function FD_createListView_simple($className)
    {
        if(file_exists("../".$this->view_dir."/".$className.'/index.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/index.php\" view already exist.<br>";
            return;
        }
        
        $object = $this->Connection->DB->create_object("$className");        
        $primaryKey = $object->getPrimaryKey();
        $class = new ReflectionClass(get_class($object));		
		$table_name=get_class($object);
		$method=$class->getConstructor();
		$params=$method->getParameters();		
		$vals_object="";
		$names_atributos="";
        //reset($this->Connection->foreing_kes[$table_name]);
        $row_titles = "";
        $row_body = "";
        foreach($params as $key_param => $param)
		{
			$name_atribut=$param->getName();
			if($name_atribut != $primaryKey)
                $row_titles .= "<th>".ucwords($name_atribut)."</th>\n                    ";
                /*$row_titles .= "<th> <a href='<?php echo ROOT_PATH ?>$className/index/$name_atribut/<?php echo \$sort_dir=='ASC'?'DESC':'ASC' ?>'>".ucwords($name_atribut)."</a></th>";*/
		}
        
        foreach($params as $key_param => $param)
		{            
			$name_atribut=$param->getName();
			if($name_atribut != $primaryKey)
                $row_body .= "<td><?php  echo \$".$className."->$name_atribut ?></td>\n";
		}
        
        $fp = fopen("../".$this->view_dir."/".$className.'/index.php',"a+");
fwrite($fp," 
        <div class='panel_listado'>  
            <h1>Lista de $className</h1>            
            <table id='listado_$className'>
                <tr>
                    $row_titles
                    <th>Actions</th>
                </tr>
                <?php  if(count(\$".$className."s)): ?>
                    <?php  foreach(\$".$className."s as \$".$className."): ?>
                        <tr>
                            $row_body
                            <td class='actions'>
                                <a href='<?php echo ROOT_PATH ?>".$this->url_module."/edit/<?php echo \$".$className."->$primaryKey ?>' class='editar'>Editar</a>
                                <a href='<?php echo ROOT_PATH ?>".$this->url_module."/delete/<?php echo \$".$className."->$primaryKey ?>' onclick=\"var d = confirm('Esta seguro de eliminar este Item?'); return d;\" class='eliminar'>Eliminar</a>
                            </td>
                        </tr>
                    <?php  endforeach ?>
                <?php  else: ?>
                <tr>
                    <td colspan='99'>No existen $className registradas</td>
                </tr>
                <?php  endif ?>
            </table>
            <div class='panel_controls'>
                <a id='btn_registrar' href='<?php echo ROOT_PATH ?>".$this->url_module."/create'>Registrar nuevo</a>
            </div>
        </div>
            ");
        fclose($fp);
        echo "<br> Vista \"".$this->view_dir."/".$className."/index.php\" creada! <br>";
    }
    
    function FD_createController_scaffold($className)
    {
        $module_name = '';
        if($this->module_name)
            $module_name = $this->module_name.'/';
            
        if(file_exists("../".$this->controller_dir."/".ucwords($className).'_Controller.php'))        
        {
            echo "<br> \"".$this->controller_dir."/$className\" controller already exist.<br>";
            return;
        }
        
        $object = $this->Connection->DB->create_object("$className");        
        $primaryKey = $object->getPrimaryKey();
        
        $fp = fopen("../".$this->controller_dir."/".ucwords($className).'_Controller.php',"a+");
fwrite($fp,"<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company \"Sysdecom\" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"public\");
	}
    
    function index()
    {
        \$this->lists();
    }
    
    function lists(\$order_by='$primaryKey', \$dir_order='ASC', \$start=0, \$page=1)
    {
        \$data['per_page'] = \$per_page = 10;
        \$data[\"".$className."s\"] = \$this->DB->get_objects(\"$className\", \"\", \$order_by?\$order_by.' '.\$dir_order:'', \$start.','.\$per_page);
        \$data['sort_order_by'] = \$order_by;
        \$data['sort_dir'] = \$dir_order;
        \$data['current_pag'] = \$page;
        \$data['start_pag'] = \$start;
        \$data['total_items'] = \$this->DB->countObjects(\"$className\");
        \$this->loadView(\"$module_name$className/index\", \$data);
    }
    
    function create()
    {
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\");
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Registrar\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function save()
    {
        \$this->DB->create_object(\"$className\", \$_POST)->save();
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function edit(\$id)
    {
        \$data[\"$className\"] = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update\";
        \$data[\"submitText\"] = \"Actualizar\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function update()
    {
        \$this->DB->create_object(\"$className\", \$_POST)->update();
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);

    }
    
    function FD_createController_scaffold_simple($className)
    {
        $module_name = '';
        if($this->module_name)
            $module_name = $this->module_name.'/';
            
        if(file_exists("../".$this->controller_dir."/".ucwords($className).'_Controller.php'))        
        {
            echo "<br> \"".$this->controller_dir."/$className\" controller already exist.<br>";
            return;
        }

        $object = $this->Connection->DB->create_object("$className");
        $primaryKey = $object->getPrimaryKey();

        $fp = fopen("../".$this->controller_dir."/".ucwords($className).'_Controller.php',"a+");
fwrite($fp,"<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company \"Sysdecom\" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"public\");
	}
    
    function index()
    {
        \$this->lists();
    }
    
    function lists()
    {        
        \$data[\"".$className."s\"] = \$this->DB->get_objects(\"$className\");        
        \$this->loadView(\"$module_name$className/index\", \$data);
    }
    
    function create()
    {
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\");
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Registrar\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function save()
    {
        \$this->DB->create_object(\"$className\", \$_POST)->save();
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function edit(\$id)
    {
        \$data[\"$className\"] = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update\";
        \$data[\"submitText\"] = \"Actualizar\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function update()
    {
        \$this->DB->create_object(\"$className\", \$_POST)->update();
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);
    echo "<br> Controller \"".$this->controller_dir."/".ucwords($className)."_Controller.php\" creado! <br>";
    }
    
}

?>