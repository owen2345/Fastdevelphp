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

class FD_Scafold
{
    var $Connection;
    var $datas;
    var $fields;
    var $controller_dir = 'controllers';
    var $view_dir = 'views';
    var $url_module = "<?php echo ROOT_PATH ?>";
    var $module_name = '';
    var $default_values = array();
    var $params_view;
    var $params_view_aux = array();
    var $files;
    var $row_titles;
    var $row_body;
    var $simple = true;
    function FD_Scafold($tableName, $datas, $fields = array(), $simple = true, $module_name = null)
    {
        $FD = getInstance();
        $this->simple = $simple;
        $this->Connection = $FD->Connection;
        $this->datas = $datas;
        $className = ucwords($tableName);
        $this->url_module = $className;
        $this->module_name = $module_name;
        $this->fields = $fields;
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
        $this->FD_createViews_scaffold($className, $simple);
        
        if($simple)
            $this->FD_createController_scaffold_simple($className);
        else
            $this->FD_createController_scaffold($className);
        
        /**********/
        echo "<a href='".ROOT_PATH.$this->url_module."'>Ir a listado</a>";
    }
    
    private function getVarName($name)
    {
        if(!in_array($name, $this->params_view_aux))
            return $name;
            
        for($i = 1; $i<=20; $i++)
        {
            if(!in_array($name."_$i", $this->params_view_aux))
            {
                $this->params_view_aux[] = $name."_$i";
                return $name."_$i";
            }
                
        }
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
        $FD = getInstance();
        $Object = $this->Connection->DB->create_object($className);        
        $primaryKey = $Object->getPrimaryKey();
        if(file_exists("../".$this->view_dir."/".$className.'/form.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/form.php\" view already exist.<br>";
            return;
        }
        
        $inputs = "";
        $encitype = "";        
        foreach($this->datas as $data_val)
        {
            $attrName = $data_val["fieldname"];
            $rules = implode(", ", $data_val["rule"]);
            echo $rules."<br>";
            switch(strtolower($data_val["fieldtype"]))
            {
                case "textarea":
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["textarea"])."'";
                    $typeHtml = "<textarea name='$attrName' class='$rules' ><?php echo \$".$className."->$attrName ?></textarea>";
                break;
                
                case "select":
                    $aux_name = $this->getVarName(ucwords($attrName)."s");
                    $val_attr = "\$$aux_name"."[$".$className."->$attrName]";
                    $aux = array();
                    foreach($data_val["select_val"] as $ki=>$vi)                    
                        $aux[] = " \"$ki\" => \"".str_replace("\"", '\"', $vi)."\"";
                    $this->params_view .= "
        \$data['$aux_name'] = array(".join(", ", $aux).");";
                    $typeHtml = "<?php echo \$this->Utility->createOptions(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, \$".$className."->$attrName) ?>";
                break;
                
                case "select_object":                    
                    $aux_name = $this->getVarName(ucwords($data_val["model"])."s");
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["model"]."', $".$className."->$attrName)->".$data_val["attr_show"].":'No definido';";
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["model"]."', '".str_replace("'", "\'", $data_val["cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createOptionsObject(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, '".$data_val["attr_show"]."', \$".$className."->$attrName); ?>";                    
                break;
                
                case "checkbox_object":
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["checkbox_model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["checkbox_model"]."', $".$className."->$attrName)->".$data_val["checkbox_attr_show"].":'No definido';";
                    $aux_name = $this->getVarName(ucwords($data_val["checkbox_model"])."s");
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["checkbox_model"]."', '".str_replace("'", "\'", $data_val["checkbox_cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createGroupChecksObject(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, '".$data_val["checkbox_attr_show"]."', array(\$".$className."->$attrName)); ?>";
                break;
                
                case "radio_object":
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["radio_model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["radio_model"]."', $".$className."->$attrName)->".$data_val["radio_attr_show"].":'No definido';";
                    $aux_name = $this->getVarName(ucwords($data_val["radio_model"])."s");
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["radio_model"]."', '".str_replace("'", "\'", $data_val["radio_cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createGroupRadiosObject(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, '".$data_val["radio_attr_show"]."', \$".$className."->$attrName); ?>";
                break;
                
                case "file":
                    $val_attr = "$".$className."->$attrName?\"<img width='50' alt='' src='\".ROOT_PATH.\"uploads/\".\$".$className."->$attrName.\"'>\":'No definido';";
                    $encitype = "multipart/form-data";
                    
                    $typeHtml = "<input type='file' name='file_$attrName' class='$rules' />
                                <?php if(\$".$className."->$attrName): ?>
                                    <img width='50' alt='' src='<?php echo ROOT_PATH.\"uploads/\".\$".$className."->$attrName ?>'>
                                <?php endif ?>";
                                
                    $this->files .= "
        \$res_file = \$this->Utility->uploadFile('file_$attrName', '../uploads/', array('gif', 'jpg', 'jpeg', 'png'), 500000);
        if(\$res_file)
        {
            if(!\$res_file[\"error\"])
                \$_POST['$attrName'] =  \$res_file['file'];
            else
                \$this->Session->addFlashMessage(\"action\", \$res_file['msg'], 2);
        }";
                                    
                break;
                
                case "hidden_field":
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["hidden_field"])."'";
                    $typeHtml = "<input type='hidden' name='$attrName' class='$rules' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
                
                case "checkbox":
                    $aux_name = $this->getVarName(ucwords($attrName)."s");
                    $val_attr = "\$$aux_name"."[$".$className."->$attrName]";
                    $aux = array();
                    foreach($data_val["checkbox_key"] as $ki=>$vi)                    
                        $aux[] = " \"$vi\" => \"".str_replace("\"", '\"', $data_val["checkbox_val"][$ki])."\"";
                    
                    $this->params_view .= "
        \$data['$aux_name'] = array(".join(", ", $aux).");";
                                            
                    $typeHtml = "<?php echo \$this->Utility->createGroupChecks(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, array(\$".$className."->$attrName)) ?>";
                break;
                
                case "radio":
                    $aux_name = $this->getVarName(ucwords($attrName)."s");
                    $val_attr = "\$$aux_name"."[$".$className."->$attrName]";
                    $aux = array();
                    foreach($data_val["radio_key"] as $ki=>$vi)                    
                        $aux[] = " \"$vi\" => \"".str_replace("\"", '\"', $data_val["radio_val"][$ki])."\"";
                    
                    $this->params_view .= "
        \$data['$aux_name'] = array(".join(", ", $aux).");";    
                    
                    $typeHtml = "<?php echo \$this->Utility->createGroupRadios(array(\"name\"=>\"$attrName\", \"class\"=>\"$rules\"), \$$aux_name, \$".$className."->$attrName) ?>";
                break;
                
                case "password":
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["password"])."'";
                    $typeHtml = "<input type='password' name='$attrName' class='$rules' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
                
                case "text":
                default:
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["text"])."'";
                    $typeHtml = "<input type='text' name='$attrName' class='$rules' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
            }
            
        $inputs .= "
                    <li>
                        <label>".$data_val["fieldtext"]."</label>
                        $typeHtml
                    </li>";
                    
            if($this->simple)
            {
                $this->row_titles .= "<th>".$data_val["fieldtext"]."</th>\n                    ";
                $this->row_body .= "
                            <td><?php  echo $val_attr ?></td>";
            }else
            {
                $this->row_titles .= "
                    <th class='<?php echo \$sort_order_by=='$attrName'?'sorted':'' ?>'> <a href='<?php echo ROOT_PATH ?>$this->url_module/lists/$attrName/<?php echo \$sort_dir=='ASC'?'DESC':'ASC' ?>/<?php echo \$current_pag?>'>".$data_val["fieldtext"]."</a></th>";
                $this->row_body .= "
                            <td><?php  echo $val_attr ?></td>";
            }
            
        }
        
        $this->default_values = join(', ', $this->default_values);
        
        $fp = fopen("../".$this->view_dir."/".$className.'/form.php',"a+");
fwrite($fp,"
        <div class='form_register' id='form_$className'>
            <script>
                jQuery(function($)
                {
                    $(\"#form_$className form\").validate();
                });
            </script>
            <h2>Formulario $className</h2>
            <form method='post' action='<?php echo ROOT_PATH ?>".$this->url_module."/<?php echo \$action ?>' enctype='$encitype'>
                <input type='hidden' name='$primaryKey' value='<?php  echo \$".$className."->$primaryKey ?>' />
                <ul>$inputs
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
        $FD = getInstance();
        if(file_exists("../".$this->view_dir."/".$className.'/index.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/index.php\" view already exist.<br>";
            return;
        }
                
        //$primaryKey = $object->getPrimaryKey();
        $row_titles = $this->row_titles;
        $row_body = $this->row_body;
        $primaryKey = $FD->DB->getPrimaryKey($className);
                
        $fp = fopen("../".$this->view_dir."/".$className.'/index.php',"a+");
fwrite($fp," 
        <div class='panel_listado'>  
            <h1>Lista de $className</h1>            
            <table id='listado_$className'>
                <tr>$row_titles<th>Actions</th>
                </tr>
                <?php  if(count(\$".$className."s)): ?>
                    <?php  foreach(\$".$className."s as \$".$className."): ?>
                        <tr>$row_body
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
                <?php echo \$this->Utility->create_paginator(\$total_items, \$per_page, \$current_pag, ROOT_PATH.\"".$this->url_module."/lists/\$sort_order_by/\$sort_dir\", 4); ?>
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
        $FD = getInstance();
        if(file_exists("../".$this->view_dir."/".$className.'/index.php'))        
        {
            echo "<br> \"".$this->view_dir."/$className/index.php\" view already exist.<br>";
            return;
        }
        
        $row_titles = $this->row_titles;
        $row_body = $this->row_body;
        $primaryKey = $FD->DB->getPrimaryKey($className);
        
        $fp = fopen("../".$this->view_dir."/".$className.'/index.php',"a+");
fwrite($fp," 
        <div class='panel_listado'>
            <div class='messages'>
                <?php \$this->Session->printFlashMessages(); ?>
            </div>  
            <h1>Lista de $className</h1>            
            <table id='listado_$className'>
                <tr>
                    $row_titles<th>Actions</th>
                </tr>
                <?php  if(count(\$".$className."s)): ?>
                    <?php  foreach(\$".$className."s as \$".$className."): ?>
                        <tr>$row_body
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
 * @version 2.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"ui_scaffold\");
	}
    
    function index()
    {
        \$this->lists();
    }
    
    function lists(\$order_by='$primaryKey', \$dir_order='ASC', \$page=1)
    {
        $this->params_view
        \$data['per_page'] = \$per_page = 10;
        \$start = (\$page - 1)*\$data['per_page'];
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
        $this->params_view
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\", array($this->default_values));
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Registrar\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function save()
    {
        $this->files
        \$this->DB->create_object(\"$className\", \$_POST)->save();
        \$this->Session->addFlashMessage(\"action\", \"El item fue guardado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function edit(\$id)
    {
        $this->params_view
        \$data[\"$className\"] = \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update/\$".$className."->$primaryKey\";
        \$data[\"submitText\"] = \"Actualizar\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function update(\$id)
    {
        $this->files
        \$this->DB->get_object_by_id(\"$className\", \$id)->merge_values(\$_POST)->update();
        \$this->Session->addFlashMessage(\"action\", \"El item fue actualizado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->Session->addFlashMessage(\"action\", \"El item fue eliminado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);
    echo "<br> Controller \"".$this->controller_dir."/".ucwords($className)."_Controller.php\" creado! <br>";
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
 * @version 2.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"ui_scaffold\");
	}
    
    function index()
    {
        \$this->lists();
    }
    
    function lists()
    {
        $this->params_view
        \$data[\"".$className."s\"] = \$this->DB->get_objects(\"$className\");        
        \$this->loadView(\"$module_name$className/index\", \$data);
    }
    
    function create()
    {
        $this->params_view
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\", array($this->default_values));
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Registrar\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function save()
    {
        $this->files
        \$this->DB->create_object(\"$className\", \$_POST)->save();
        \$this->Session->addFlashMessage(\"action\", \"El item fue guardado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function edit(\$id)
    {
        $this->params_view
        \$data[\"$className\"] = \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update/\$".$className."->$primaryKey\";
        \$data[\"submitText\"] = \"Actualizar\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    function update(\$id)
    {
        $this->files
        \$this->DB->get_object_by_id(\"$className\", \$id)->merge_values(\$_POST)->update();
        \$this->Session->addFlashMessage(\"action\", \"El item fue actualizado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->Session->addFlashMessage(\"action\", \"El item fue eliminado!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);
    echo "<br> Controller \"".$this->controller_dir."/".ucwords($className)."_Controller.php\" creado! <br>";
    }
    
}

?>