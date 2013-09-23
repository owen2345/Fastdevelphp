<?php

/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@skylogix.net
 * @company Systems Development Company "Skylogix" srl.
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
        $this->controller_dir = CONTROLLERS_PATH;
        $this->view_dir = VIEWS_PATH;
        $this->simple = $simple;
        $this->Connection = $FD->Connection;
        $this->datas = $datas;
        $className = ucwords($tableName);
        $this->url_module = $className;
        $this->module_name = $module_name;
        $this->fields = $fields;
        if($module_name)
        {
            $this->controller_dir .= $module_name;
            $this->view_dir .= $module_name;
            if(!file_exists($this->controller_dir))
            {
                mkdir($this->controller_dir, 0777);
                echo "<div class=\"albox succesbox\"><b>Succes :</b>Folder \"$this->controller_dir/\" was created!</a></div>";
            }
                 
            if(!file_exists($this->view_dir))
            {
                mkdir($this->view_dir, 0777);
                echo "<div class=\"albox succesbox\"><b>Succes :</b>Folder \"$this->view_dir/\" was created!</a></div>";
            }
            $this->view_dir .= "/";
            $this->controller_dir .= "/";
            $this->url_module = "$module_name/".$this->url_module;              
        }
                
        /*****/        
        $this->FD_createViews_scaffold($className, $simple);
        
        if($simple)
            $this->FD_createController_scaffold_simple($className);
        else
            $this->FD_createController_scaffold($className);
        
        /**********/
        echo "<div class=\"albox succesbox\"><b>Succes :</b>Finished: <a href='".ROOT_PATH.$this->url_module."'>Preview</a></div>";
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
        if(!file_exists($this->view_dir."$className"))
        {
            $old = umask(0);
            mkdir($this->view_dir."$className", 0777);
            umask($old);
            echo "<div class=\"albox succesbox\"><b>Succes :</b>Folder \"$this->view_dir$className/\" was created!</a></div>";
        }
            
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
        if(file_exists($this->view_dir.$className.'/form.php'))        
        {
            echo "<div class=\"albox errorbox\"><b>Error : \"".$this->view_dir."$className/form.php\" view already exist</b> </div>";
            return;
        }
        
        $inputs = "";
        $encitype = "";        
        foreach($this->datas as $data_val)
        {
            $attrName = $data_val["fieldname"];
            $rules = "";
            if(isset($data_val["rule"]))
                $rules = implode(" ", $data_val["rule"]);
            //echo $rules."<br>";
            switch(strtolower($data_val["fieldtype"]))
            {
                case "textarea":
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["textarea"])."'";
                    $typeHtml = "<textarea name='$attrName' class='input_textarea $rules' ><?php echo \$".$className."->$attrName ?></textarea>";
                break;
                
                case "select":
                    $aux_name = $this->getVarName(ucwords($attrName)."s");
                    $val_attr = "\$$aux_name"."[$".$className."->$attrName]";
                    $aux = array();
                    foreach($data_val["select_val"] as $ki=>$vi)                    
                        $aux[] = " \"$ki\" => \"".str_replace("\"", '\"', $vi)."\"";
                    $this->params_view .= "
        \$data['$aux_name'] = array(".join(", ", $aux).");";
                    $typeHtml = "<?php echo \$this->Utility->createOptions(array(\"name\"=>\"$attrName\", \"class\"=>\"input_select $rules\"), \$$aux_name, \$".$className."->$attrName) ?>";
                break;
                
                case "select_object":                    
                    $aux_name = $this->getVarName(ucwords($data_val["model"])."s");
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["model"]."', $".$className."->$attrName)->".$data_val["attr_show"].":'No definido';";
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["model"]."', '".str_replace("'", "\'", $data_val["cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createOptionsObject(array(\"name\"=>\"$attrName\", \"class\"=>\"input_select $rules\"), \$$aux_name, '".$data_val["attr_show"]."', \$".$className."->$attrName); ?>";                    
                break;
                
                case "checkbox_object":
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["checkbox_model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["checkbox_model"]."', $".$className."->$attrName)->".$data_val["checkbox_attr_show"].":'No definido';";
                    $aux_name = $this->getVarName(ucwords($data_val["checkbox_model"])."s");
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["checkbox_model"]."', '".str_replace("'", "\'", $data_val["checkbox_cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createGroupChecksObject(array(\"name\"=>\"$attrName\", \"class\"=>\"input_checkbox $rules\"), \$$aux_name, '".$data_val["checkbox_attr_show"]."', array(\$".$className."->$attrName)); ?>";
                break;
                
                case "radio_object":
                    $val_attr = "\$this->DB->get_object_by_id('".$data_val["radio_model"]."', $".$className."->$attrName)?\$this->DB->get_object_by_id('".$data_val["radio_model"]."', $".$className."->$attrName)->".$data_val["radio_attr_show"].":'No definido';";
                    $aux_name = $this->getVarName(ucwords($data_val["radio_model"])."s");
                    $this->params_view .= "
        \$data['$aux_name'] = \$this->DB->get_objects('".$data_val["radio_model"]."', '".str_replace("'", "\'", $data_val["radio_cond_sql"])."');";
                    $typeHtml = "<?php echo \$this->Utility->createGroupRadiosObject(array(\"name\"=>\"$attrName\", \"class\"=>\"input_radio $rules\"), \$$aux_name, '".$data_val["radio_attr_show"]."', \$".$className."->$attrName); ?>";
                break;
                
                case "file":
                    $val_attr = "\$this->Utility->getFileLink(\"../uploads/\".$".$className."->$attrName, ROOT_PATH.\"uploads/\".$".$className."->$attrName)";
                    $encitype = "multipart/form-data";
                    
                    $typeHtml = "<input type='file' name='FD_$attrName' class='input_file $rules' />
                    <?php if($".$className."->$attrName): ?>
                        <label class='preview_select'><input type=\"checkbox\" name=\"FD_del_$attrName\" value=\"1\" /> Delete (<a target=\"_blank\" class=\"link_preview\" href=\"<?php echo ROOT_PATH.'uploads/'.$".$className."->$attrName ?>\" title=\"Preview\">Preview</a>)</label> 
                    <?php endif ?>";
                    $ftypes = implode("','", explode(",", str_replace(" ", "", $data_val["file"])));
                    $this->files .= "
        /*** upload file ****/                    
        \$res_file = \$this->Utility->uploadFile('FD_$attrName', '../uploads/', ".($ftypes?"array('$ftypes')":"null")."); //uploading file
        if(!\$res_file[\"error\"]) //on file uploaded
            \$this->Request->setParam_POST(\"$attrName\", \$res_file['file']);
        elseif(\$res_file['error_type'] != \"no_file\") //file error.
            \$this->Session->addFlashMessage(\"FD_$attrName\", \$res_file['msg'], 2);
        
        if(\$this->Request->getParam_POST(\"FD_del_$attrName\")) //unassigned file
        {
            \$this->Session->addFlashMessage(\"FD_$attrName\", \"Your file was unassigned. \");
            \$this->Request->setParam_POST(\"$attrName\", \"\");
        }
        /*** end upload file ****/
        
        ";
                                    
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
                        $typeHtml = "<?php echo \$this->Utility->createGroupChecks(array(\"name\"=>\"$attrName\", \"class\"=>\"input_checkbox $rules\"), \$$aux_name, array(\$".$className."->$attrName)) ?>";
                break;
                
                case "single_checkbox": //pending in form
                    $val_attr = "\$".$className."->$attrName?'Active':'Inactive'";
                    $typeHtml = "<input type='hidden' name='zoom_tablet' value=\"0\" />
                                <input type='checkbox' class='input_checkbox $rules' name='$attrName' value='1' <?php echo \$".$className."->$attrName?\"checked=''\":\"\" ?> />";
                break;
                
                case "radio":
                    $aux_name = $this->getVarName(ucwords($attrName)."s");
                    $val_attr = "\$$aux_name"."[$".$className."->$attrName]";
                    $aux = array();
                    foreach($data_val["radio_key"] as $ki=>$vi)                    
                        $aux[] = " \"$vi\" => \"".str_replace("\"", '\"', $data_val["radio_val"][$ki])."\"";
                    
                    $this->params_view .= "
        \$data['$aux_name'] = array(".join(", ", $aux).");";    
                    
                    $typeHtml = "<?php echo \$this->Utility->createGroupRadios(array(\"name\"=>\"$attrName\", \"class\"=>\"input_radio $rules\"), \$$aux_name, \$".$className."->$attrName) ?>";
                break;
                
                case "password":
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["password"])."'";
                    $typeHtml = "<input type='password' name='$attrName' class='input_password $rules' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
                
                case "text":
                default:
                    $val_attr = "\$".$className."->$attrName";
                    $this->default_values[] = "'$attrName' => '". str_replace("'", "\'", $data_val["text"])."'";
                    $typeHtml = "<input type='text' name='$attrName' class='input_text $rules' value='<?php echo \$".$className."->$attrName ?>' />";
                break;
            }
            
        $inputs .= "
                        <li>
                            <label class='label_form'>".$data_val["fieldtext"]."</label>
                            $typeHtml
                        </li>";
                    
            if($this->simple)
            {
                $this->row_titles .= "
                            <th>".$data_val["fieldtext"]."</th>";
                $this->row_body .= "
                                    <td><?php  echo $val_attr ?></td>";
            }else
            {
                $this->row_titles .= "
                            <th class='sorter <?php echo \$sort_order_by=='$attrName'?'sorted':'' ?>'> 
                                <a href='<?php echo ROOT_PATH ?>$this->url_module/lists/$attrName/<?php echo \$sort_dir=='ASC'?'DESC':'ASC' ?>/<?php echo \$current_pag?>'>".$data_val["fieldtext"]."</a>
                            </th>";
                $this->row_body .= "
                                    <td><?php  echo $val_attr ?></td>";
            }
            
        }
        
        $this->default_values = join(', ', $this->default_values);
        
        $fp = fopen($this->view_dir.$className.'/form.php',"a+");
        $old = umask(0);
        chmod($this->view_dir.$className.'/form.php', 0666);
        umask($old);
        
fwrite($fp,"
        <div class='form_register simplebox' id='form_$className'>
            <script>
                jQuery(function($)
                {
                    $(\"#form_$className form\").validate();
                });
            </script>
            
       	    <div class=\"titleh\"><h3>Formulario $className</h3></div>
                            
            <div class=\"body\">
                <form method='post' action='<?php echo ROOT_PATH ?>".$this->url_module."/<?php echo \$action ?>' enctype='$encitype'>
                    <input type='hidden' name='$primaryKey' value='<?php  echo \$".$className."->$primaryKey ?>' />
                    <ul>$inputs
                        <li class='controls'>
                            <button type='submit'><?php echo \$submitText ?></button>
                            <button type='button' onclick='history.back();'>Cancelar</button>
                        </li>            
                    </ul>
                </form>
            </div>
        </div>");
        fclose($fp);
        echo "<div class=\"albox succesbox\"><b>Succes :</b> View \"".$this->view_dir.'/'.$className."/form.php\" was created!</div>";
    }
    
    function FD_createListView($className)
    {
        $FD = getInstance();
        if(file_exists($this->view_dir.$className.'/index.php'))        
        {
            echo "<div class=\"albox errorbox\"><b>Error : \"".$this->view_dir."$className/index.php\" view already exist.</b> </div>";
            return;
        }
                
        //$primaryKey = $object->getPrimaryKey();
        $row_titles = $this->row_titles;
        $row_body = $this->row_body;
        $primaryKey = $FD->DB->getPrimaryKey($className);
                
        $fp = fopen($this->view_dir.$className.'/index.php',"a+");
        $old = umask(0);
        chmod($this->view_dir.$className.'/index.php', 0666);
        umask($old);
fwrite($fp," 
        <div class='panel_listado simplebox'>
            <div class=\"titleh\"><h3>List $className</h3></div>
            <div class=\"body\">
                <table id='listado_$className' class='tablesorter'>
                    <thead>
                        <tr>$row_titles
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  if(count(\$".$className."s)): ?>
                            <?php  foreach(\$".$className."s as \$".$className."): ?>
                                <tr>$row_body
                                    <td class='actions'>
                                        <a href='<?php echo ROOT_PATH ?>".$this->url_module."/edit/<?php echo \$".$className."->$primaryKey ?>' class='editar hg-yellow' title='Edit item'>Edit</a>
                                        <a href='<?php echo ROOT_PATH ?>".$this->url_module."/delete/<?php echo \$".$className."->$primaryKey ?>' title='Delete item' onclick=\"var d = confirm('Are you sure delete this item?'); return d;\" class='eliminar hg-red'>Delete</a>
                                    </td>
                                </tr>
                            <?php  endforeach ?>
                        <?php  else: ?>
                            <tr>
                                <td colspan='99'>Not found $className items.</td>
                            </tr>
                        <?php  endif ?>
                    </tbody>
                </table>
                <div class='panel_paginator'>
                    <?php echo \$this->Utility->create_paginator(\$total_items, \$per_page, \$current_pag, ROOT_PATH.\"".$this->url_module."/lists/\$sort_order_by/\$sort_dir\", 4); ?>
                </div>
                <div class='panel_controls padding20'>
                    <a id='btn_registrar' class='button-green' href='<?php echo ROOT_PATH ?>".$this->url_module."/create'>Create new</a>
                </div>
            </div>
        </div>
            ");
        fclose($fp);
        echo "<div class=\"albox succesbox\"><b>Succes :</b> View \"".$this->view_dir."/".$className."/index.php\" was created!</div>";
    }
    
    function FD_createListView_simple($className)
    {
        $FD = getInstance();
        if(file_exists($this->view_dir.$className.'/index.php'))        
        {
            echo "<div class=\"albox errorbox\"><b>Error : \"".$this->view_dir."$className/index.php\" view already exist.</b> </div>";
            return;
        }
        
        $row_titles = $this->row_titles;
        $row_body = $this->row_body;
        $primaryKey = $FD->DB->getPrimaryKey($className);
        
        $fp = fopen($this->view_dir.$className.'/index.php',"a+");
        $old = umask(0);
        chmod($this->view_dir.$className.'/index.php', 0666);
        umask($old);
fwrite($fp," 
        <div class='panel_listado simplebox'>
            <div class=\"titleh\"><h3>List $className</h3></div>
            <div class=\"body\">            
                <table id='listado_$className'>
                    <thead>
                        <tr>
                            $row_titles
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  if(count(\$".$className."s)): ?>
                            <?php  foreach(\$".$className."s as \$".$className."): ?>
                                <tr>$row_body
                                    <td class='actions'>
                                        <a href='<?php echo ROOT_PATH ?>".$this->url_module."/edit/<?php echo \$".$className."->$primaryKey ?>' class='editar hg-yellow' title='Edit item'>Edit</a>
                                        <a href='<?php echo ROOT_PATH ?>".$this->url_module."/delete/<?php echo \$".$className."->$primaryKey ?>' title='Delete item' onclick=\"var d = confirm('Are you sure delete this Item?'); return d;\" class='eliminar hg-red'>Delete</a>
                                    </td>
                                </tr>
                            <?php  endforeach ?>
                        <?php  else: ?>
                            <tr>
                                <td colspan='99'>Not found $className items</td>
                            </tr>
                        <?php  endif ?>
                    </tbody>
                </table>
                <div class='panel_controls padding20'>
                    <a id='btn_registrar' class='button-green' href='<?php echo ROOT_PATH ?>".$this->url_module."/create'>Create new</a>
                </div>
            </div>
        </div>
            ");
        fclose($fp);
        echo "<div class=\"albox succesbox\"><b>Succes :</b> View \"".$this->view_dir."/".$className."/index.php\" was created!</div>";
    }
    
    function FD_createController_scaffold($className)
    {
        $module_name = '';
        if($this->module_name)
            $module_name = $this->module_name.'/';
            
        if(file_exists($this->controller_dir.ucwords($className).'_Controller.php'))        
        {
            echo "<div class=\"albox errorbox\"><b>Error : \"".$this->controller_dir.$className."_Controller.php\" controller already exist.</b> </div>";
            return;
        }
        
        $object = $this->Connection->DB->create_object("$className");        
        $primaryKey = $object->getPrimaryKey();
        
        $fp = fopen($this->controller_dir.ucwords($className).'_Controller.php',"a+");
        $old = umask(0);
        chmod($this->controller_dir.ucwords($className).'_Controller.php', 0666);
        umask($old);
fwrite($fp,"<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@skylogix.net
 * @company Systems Development Company \"Skylogix\" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"fastdevelphp/backend\");
	}
    
    /**
    * default controller function
    **/
    function index()
    {
        \$this->lists();
    }
    
    /**
    * show list items with pagination and sortable
    * \$order_by: attribute name for order the list, default by '$primaryKey'
    * \$dir_order: direction order (ASC = ascendant, DESC = descendant), default ASC
    * \$page: current page to show, default 1
    **/
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
    
    /**
    * show create form view
    **/
    function create()
    {
        $this->params_view
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\", array($this->default_values));
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Register\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    /**
    * save information into database and then redirect to list items.
    **/
    function save()
    {
        $this->files
        \$this->DB->create_object(\"$className\", \$this->Request->getParams_POST())->save();
        \$this->Session->addFlashMessage(\"action\", \"Item saved!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    /**
    * show edit form view
    * \$id: id_$className (identifier value)
    **/
    function edit(\$id)
    {
        $this->params_view
        \$data[\"$className\"] = \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update/\$".$className."->$primaryKey\";
        \$data[\"submitText\"] = \"Update\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    /**
    * save changes in to database and then redirect to list items.
    * \$id: id_$className (identifier value)
    **/
    function update(\$id)
    {
        $this->files
        \$this->DB->get_object_by_id(\"$className\", \$id)->merge_values(\$this->Request->getParams_POST())->update();
        \$this->Session->addFlashMessage(\"action\", \"Item updated!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    /**
    * Delete information from database and then redirect to list items.
    * \$id: id_$className (identifier value)
    **/
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->Session->addFlashMessage(\"action\", \"Item deleted!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);
    echo "<div class=\"albox succesbox\"><b>Succes :</b> Controller \"".$this->controller_dir."/".ucwords($className)."_Controller.php\" was created!</div>";
    
    }
    
    function FD_createController_scaffold_simple($className)
    {
        $module_name = '';
        if($this->module_name)
            $module_name = $this->module_name.'/';
            
        if(file_exists($this->controller_dir.ucwords($className).'_Controller.php'))        
        {
            echo "<div class=\"albox errorbox\"><b>Error : \"".$this->controller_dir.$className."_Controller.php\" controller already exist.</b> </div>";
            return;
        }

        $object = $this->Connection->DB->create_object("$className");
        $primaryKey = $object->getPrimaryKey();

        $fp = fopen($this->controller_dir.ucwords($className).'_Controller.php',"a+");
        $old = umask(0);
        chmod($this->controller_dir.ucwords($className).'_Controller.php', 0666);
        umask($old);
fwrite($fp,"<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@skylogix.net
 * @company Systems Development Company \"Skylogix\" srl.
 * @license All rights reservate
 * @version 2.0
 * @copyright 2009
 */ 
class ".ucwords($className)."_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        \$this->useLayout(\"fastdevelphp/backend\");
	}
    
    /**
    * default controller function
    **/
    function index()
    {
        \$this->lists();
    }
    
    /**
    * show list items
    **/
    function lists()
    {
        $this->params_view
        \$data[\"".$className."s\"] = \$this->DB->get_objects(\"$className\");        
        \$this->loadView(\"$module_name$className/index\", \$data);
    }
    
    /**
    * show create form view
    **/
    function create()
    {
        $this->params_view
        \$data[\"$className\"] = \$this->DB->create_object(\"$className\", array($this->default_values));
        \$data[\"action\"] = \"save\";
        \$data[\"submitText\"] = \"Register\";        
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    /**
    * save information into database and then redirect to list items.
    **/
    function save()
    {
        $this->files
        \$this->DB->create_object(\"$className\", \$this->Request->getParams_POST())->save();
        \$this->Session->addFlashMessage(\"action\", \"Item saved!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    /**
    * show edit form view
    * \$id: id_$className (identifier value)
    **/
    function edit(\$id)
    {
        $this->params_view
        \$data[\"$className\"] = \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$data[\"action\"] = \"update/\$".$className."->$primaryKey\";
        \$data[\"submitText\"] = \"Update\";
        \$this->loadView(\"$module_name$className/form\", \$data);
    }
    
    /**
    * save changes in to database and then redirect to list items.
    * \$id: id_$className (identifier value)
    **/
    function update(\$id)
    {
        $this->files
        \$this->DB->get_object_by_id(\"$className\", \$id)->merge_values(\$this->Request->getParams_POST())->update();
        \$this->Session->addFlashMessage(\"action\", \"Item updated!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
    /**
    * Delete information from database and then redirect to list items.
    * \$id: id_$className (identifier value)
    **/
    function delete(\$id)
    {
        \$".$className." = \$this->DB->get_object_by_id(\"$className\", \$id);
        \$".$className."->delete();
        \$this->Session->addFlashMessage(\"action\", \"Item deleted!\", 0);
        \$this->redirect(\"".$this->url_module."\");
    }
    
}
?>");
    fclose($fp);
    
    echo "<div class=\"albox succesbox\"><b>Succes :</b> Controller \"".$this->controller_dir.ucwords($className)."_Controller.php\" was created!</div>";
    }
    
}

?>