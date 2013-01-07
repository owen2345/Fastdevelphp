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

class FD_Utility
{	
	var $isLoadCalendar = false;
	
	function FD_Utility()
	{
	   
	}
    
    function getSizeFile($filepath)
    {
        if(file_exists($filepath))
            return round(filesize($filepath)/1024, 2);
        else
            return 0;
    }
    
    function getFileName($filepath)
    {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];        
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);        
        $lastdot = $pattern[count($pattern)-1][1];        
        $filename = basename(substr($string, 0, $lastdot-1));
        return $filename;
    }
    
    function getFileNameWithExtension($filepath)
    {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];        
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);        
        $lastdot = $pattern[count($pattern)-1][1];        
        $filename = basename(substr($string, 0, $lastdot-1));
        return $filename.".".$this->getExtensionFile($filepath);
    }
    
    function getFileNameWithExtensionWithPostfijo($filepath, $postfijo)
    {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];        
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);        
        $lastdot = $pattern[count($pattern)-1][1];        
        $filename = basename(substr($string, 0, $lastdot-1));
        return $filename.$postfijo.".".$this->getExtensionFile($filepath);
    }
    
    function getFileNameWithExtensionWithPrefijo($filepath, $prefijo)
    {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];        
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);        
        $lastdot = $pattern[count($pattern)-1][1];        
        $filename = basename(substr($string, 0, $lastdot-1));
        return $prefijo.$filename.".".$this->getExtensionFile($filepath);
    }
    
    function removeFile($path)
    {
        if(file_exists($path) && !is_dir($path))
            unlink($path);
    }
    
    function moveFile($file1,$file2, $unlinkFile=true)
    {
        $contentx =@file_get_contents($file1);
        $openedfile = fopen($file2, "w");
        fwrite($openedfile, $contentx);
        fclose($openedfile);
        if ($contentx === FALSE)
            $status=false;
        else 
            $status=true;
        if($unlinkFile)
            unlink($file11);
        return $status;
    }
    
    function getExtensionFile($file_name)
    {
        $fichero = $file_name;
        $extension = explode(".", $fichero) ;
        $extension = $extension[count($extension)-1];
        return $extension;
    }
        
    /**
     * $attrsElement: atributos para el dropdown: name=>"", class=>"", ..
     * $array_values: array de valores
     * $selectedKeyValue: key seleccionado
     * $text_default: texto por default
     * $default: si es true, muestra el texto por default
     * Retorna un dropdown con los parametros enviados
    */
	function createOptions($attrsElement = array(), $array_values = array(), $selectedKeyValue = "", $text_default = "Select an option", $default = true)
	{
		$options = "<select ". $this->applyAttrs($attrsElement). ">";
        if($default)
            $options .= "<option value=''> $text_default </option>";  
		
        foreach($array_values as $key => $val)
		{
			$sel= $key == $selectedKeyValue ? " selected=''":"";
			$options .= "<option value='$key' $sel> $val </option>";
		}
		return $options. "</select>";
	}
	
    /**
     * $attrsElement: atributos para el dropdown: name=>"", class=>"", ..
     * $Objects: array de objetos DB
     * $attrObjectText: nombre del atributo o nombre de funcion() a mostrar
     * $selectedKeyValue: key seleccionado
     * $text_default: texto por default
     * $default: si es true, muestra el texto por default
     * Retorna un dropdown con los parametros enviados
    */
	function createOptionsObject($attrsElement = array(), $Objects, $attrObjectText, $selectedKeyValue = "", $text_default = "Selecciona una opci&oacute;n", $default = true)
	{
		$options = "<select ". $this->applyAttrs($attrsElement). ">";
        if($default)
            $options .= "<option value=''> $text_default </option>";
        
		foreach($Objects as $Obj)
		{
			$table_name=get_class($Obj);
			$primary_key = $Obj->getPrimaryKey();
			$sel= $Obj->$primary_key == $selectedKeyValue ? " selected=''":"";
            if(strpos($attrObjectText, "("))
            {                
                $attrObjectText1 = explode("(", $attrObjectText);                
                $attrObjectText1 = $attrObjectText1[0];                
                $options .= "<option value='".$Obj->$primary_key."' $sel>".$Obj->$attrObjectText1()."</option>";   
            }else
                $options .= "<option value='".$Obj->$primary_key."' $sel>".$Obj->$attrObjectText."</option>";
			 
		}
		return $options. "</select>";
	}
    
    /**
     * $attrsElement: atributos para los checkbox: name=>"", class=>"", ..
     * $Objects: array de objetos DB
     * $attrObjectText: nombre del atributo o nombre de funcion() a mostrar
     * $array_check_values: array de keys marcados (checked)
     * Retorna un grupo de checkbox con los parametros enviados
    */
    function createGroupChecksObject($attrsElement = array(), $Objects, $attrObjectText, $array_check_values = array())
	{
	    $attrsElement["name"] = $attrsElement["name"]."[]";
        $attrsElement["type"] = "checkbox";
		$options = "<ul class='list_checkboxs'>";
		foreach($Objects as $Obj)
		{
			$table_name=get_class($Obj);
			$primary_key = $Obj->getPrimaryKey();
			$sel= in_array($Obj->$primary_key, $array_check_values) ? " checked=''":"";
            $name_obj = get_class($Obj);
			if(strpos($attrObjectText, "("))
            {   
                $attrObjectText1 = explode("\(", $attrObjectText);                
                $attrObjectText1 = $attrObjectText1[0];                
                $options .= "<li><label><input ". $this->applyAttrs($attrsElement). " value='".$Obj->$primary_key."' $sel  /> 
				<span> ".$Obj->$attrObjectText1()." </span></label></li>";
            }else
                $options .= "<li><label><input ". $this->applyAttrs($attrsElement). " value='".$Obj->$primary_key."' $sel  />
				<span>  ".$Obj->$attrObjectText." </span></label></li>";			 
		}
		return $options. "</ul>";
	}
    
    /**
     * $attrsElement: atributos para los checkbox: name=>"", class=>"", ..
     * $array_values: array de valores
     * $array_check_values: array de items seleccionados
     * Retorna grupo de checkbox con los parametros enviados
    */
    function createGroupChecks($attrsElement = array(), $array_values = array(), $array_check_values = array())
	{
	    $key = time().rand(9999, 9989);
		$options = "<ul class='list_checkboxs'>";
        if(in_array("name", $attrsElement))
            $attrsElement["name"] = $attrsElement["name"]."[]";
        else
            $attrsElement["name"] = $key."[]";
        $attrsElement["type"] = "checkbox";
        
        foreach($array_values as $key => $val)
		{
			$sel= in_array($key, $array_check_values) ? " checked=''":"";
			$options .= "<li><label><input ". $this->applyAttrs($attrsElement). " value='".$key."' $sel  /> <span>  ".$val." </span></label></li>";
		}        
		return $options. "</ul>";
	}
    
    /**
     * $attrsElement: atributos para los radio buttons: name=>"", class=>"", ..
     * $array_values: array de valores
     * $radio_check_value: key seleccionado
     * Retorna grupo de radio buttons con los parametros enviados
    */    
	function createGroupRadios($attrsElement = array(), $array_values = array(), $radio_check_value = "")
	{
		$options = "<ul class='list_radios'>";
        
        foreach($array_values as $key => $val)
		{
			$sel= $key == $radio_check_value ? " checked=''":"";
			$options .= "<li><label><input type='radio' ". $this->applyAttrs($attrsPanelContenedor). " value='".$key."' $sel  /> <span>  ".$val." </span></label></li>";
		}
		return $options. "</ul>";
	}
	
    /**
     * $attrsElement: atributos para los checkbox: name=>"", class=>"", ..
     * $Objects: array de objetos DB
     * $attrObjectText: nombre del atributo o nombre de funcion() a mostrar
     * $radio_check_value: key seleccionado (checked)
     * Retorna un grupo de radio buttons con los parametros enviados
    */
	function createGroupRadiosObject($attrsElement = array(), $Objects, $attrObjectText, $radio_check_value = "")
	{
		$options = "<ul class='list_radios'>";
        $attrsElement["type"] = "radio";
		foreach($Objects as $Obj)
		{
			$table_name=get_class($Obj);
			$primary_key = $Obj->getPrimaryKey();
			$sel= $Obj->$primary_key == $radio_check_value ? " checked=''":"";
            $name_obj = get_class($Obj);
			if(strpos($attrObjectText, "("))
            {   
                $attrObjectText1 = explode("\(", $attrObjectText);                
                $attrObjectText1 = $attrObjectText1[0];                
                $options .= "<li><label><input ". $this->applyAttrs($attrsElement). " value='".$Obj->$primary_key."' $sel  /> 
				<span> ".$Obj->$attrObjectText1()." </span></label></li>";   
            }else
                $options .= "<li><label><input ". $this->applyAttrs($attrsElement). " value='".$Obj->$primary_key."' $sel  />
				<span>  ".$Obj->$attrObjectText." </span></label></li>";
			 
		}
		return $options. "</ul>";
	}
	
    /**
     * crea un input de tipo calendario
     * DEPRECADO
    */    
	function createCalendar($attributesInput=array())
	{
		$cal = "<input type='text' ". $this->applyAttrs($attributesInput). "/>";		
		$cal .= "<script> $(\"input[name='".$attributesInput["name"]."']\").addClass(\"calendar\").datepicker({dateFormat: 'dd-mm-yy'}); $(\"input[name='".$attributesInput["name"]."']\").datepicker($.datepicker.regional['es'])</script>";
		return $cal;
	}
    
    /**
     * Crea un input de tipo timeEntry 
     * Requiere: libreria jquery.timeentry.js y jquery.js
     * DEPRECADO
    */
    
	function createTime($attributesInput=array())
	{
		$cal = "<input type='text' ". $this->applyAttrs($attributesInput). "/>";		
		$cal .= "<script> $(\"input[name='".$attributesInput["name"]."']\").timeEntry({spinnerImage: '".IMAGES_PATH."timeentry/spinnerDefault.png',ampmPrefix: ' '}).parent(\".timeEntry_wrap\").addClass('hidden'); 
		</script>";
		return $cal;
	}
	
	protected function applyAttrs($attrs)
	{
		$res = "";
		foreach($attrs as $attrN => $attrV)
		{
			$res .= $attrN . " = '" . $attrV . "'";
		}
		return $res;
	}
	
    /**
     * Establece un header de tipo latino
     */
	function setHeaderLatin()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
		header("Cache-Control: no-cache, must-revalidate" ); 
		header("Pragma: no-cache" );
		header("Content-type: text/html; charset=ISO-8859-1");
	}
    
    /**
     * $minutes: total minutes
     * return array("hour", "min");
     */
    function minutesToHours($minutes, $print = false)
    {
        $res = array("hour"=>floor($minutes/60), "min"=>$minutes%60);
        if($print)
            echo str_pad($res["hour"], "2", "0", STR_PAD_LEFT).":".str_pad($res["min"], "2", "0", STR_PAD_LEFT);
        else
            return $res;
    }
    
    /**
     * $date = YYYY-mm-dd hh:ii:ss
     * return total minutes from date
     */
    function getDateMinutes($date)
    {
        $to_time = strtotime($date);
        $from_time = strtotime(date("Y-m-d", strtotime($date))." 00:00:00");
        return round(abs($to_time - $from_time) / 60,2);
    }
    
    /**
     * $date_from = YYYY-mm-dd hh:ii:ss
     * $date_to = YYYY-mm-dd hh:ii:ss
     * return total minutes from range
     */
    function getDateRangeMinutes($date_from, $date_to)
    {
        $to_time = strtotime($date_from);
        $from_time = strtotime($date_to);
        return round(abs($to_time - $from_time) / 60,2);
    }
    
    /**
     * $date_from = YYYY-mm-dd hh:ii:ss
     * $date_to = YYYY-mm-dd hh:ii:ss
     * return array("hour", "min");
     */
    function getDateRangeHours($date_from, $date_to, $print = false)
    {
        $a = $this->getDateRangeMinutes($date_from, $date_to);
        $res = $this->minutesToHours($a, $print);
        if(!$print)
            return $res;
        
    }
    
    function setToFormatDate($date)
    {
        return date("F d, Y",strtotime($date));
    }
    
    function setToFormatDateTime($date)
    {
        return date("F d, Y g:i A ",strtotime($date));
    }
    
    function getFormatDate($date)
    {
        return date("Y-m-d",strtotime($date));
    }
    
    /**
     * Calcula el rango de fechas de una semana para una determinada fecha     
    */    
    function get_week_range($day='', $month='', $year='') 
    {
        // default empties to current values
        if (empty($day)) $day = date('d');
        if (empty($month)) $month = date('m');
        if (empty($year)) $year = date('Y');
        
        $weekday = date('w', mktime(0,0,0,$month, $day, $year));
        $sunday  = $day - $weekday + 1;//+1 for monday
        $start_week = date('Y-m-d', mktime(0,0,0,$month, $sunday, $year));
        $end_week   = date('Y-m-d', mktime(0,0,0,$month, $sunday+6, $year));
        if (!empty($start_week) && !empty($end_week)) {
            return array('start'=>$start_week, 'end'=>$end_week);
        }
        // otherwise there was an error :'(
        return false;
    }
    
    /**
     * format: yyyy-mm-dd
    */
    function get_weeks_range($start_date, $end_date) 
    {
        $res = array();
        $i = 0;
        while(strtotime("+1 week", strtotime($end_date)) > strtotime("+$i week", strtotime($start_date)))
        {
            $date = explode("-", date("d-m-Y", strtotime("+$i week", strtotime($start_date)) ) );
            $res[] = $this->get_week_range( $date[0], $date[1], $date[2] );            
            $i++;
        }        
        return $res;
    }
    
    /**
     * recupera el tiempo real, usado para arreglar el las horas en los servidores
     */
    function getRealTime($time)
    {
        return strtotime(date("Y-m-d H:i:s", $time) . " + ".TIME_DEFFERENCE." minutes");
    }
    
    /**
     * $inputname: campo input file del formulario
     * $dirSave: directorio en el que se va a guardar el archivo
     * $support_types: array con los formatos permitidos, si es null soporta todos los formatos
     * $maximum: numero en bytes como tamano maximo a subir, si es vacio soporta el maximo definido en php.ini
     * retorna: array(error= true/false, msg=string, file=filesaved_name), si el archivo no existe retorna FALSE
    */
    function uploadFile($inputname, $dirSave, $support_types = null, $maximum = null)
    {
        return $this->uploadFileByTmpname($_FILES[$inputname]['name'], $_FILES[$inputname]['tmp_name'], $dirSave, $support_types, $maximum);
    }
    
    /**
     * $filename: nombre con el que se va a guardar el archivo
     * $tmpname: tmpname del archivo a subir, ejm: $_FILES["mi_campo"]['tmp_name']
     * $dirSave: directorio en el que se va a guardar el archivo
     * $support_types: array con los formatos permitidos, si es null soporta todos los formatos
     * $maximum: numero en bytes como tamano maximo a subir, si es vacio soporta el maximo definido en php.ini
     * retorna: array(error= true/false, msg=string, file=filesaved_name), si el archivo no existe retorna FALSE
    */
    function uploadFileByTmpname($filename, $tmpname, $dirSave, $support_types = null, $maximum = null)
    {
        $FD = getInstance();
        $Upload = $FD->loadLibrary("FD_Upload");
        $res = array("error"=>true, "file"=>"", "msg"=>"File not uploaded.");
        if($tmpname) 
        {
            if(file_exists($dirSave.$filename))
            {
                for($ii=0; $ii<9999999; $ii++)
                {
                    if(!file_exists($dirSave.$ii."_".$filename))
                    {
                        $doc_name = $ii."_".$filename;
                        break;
                    }
                }
            }else
                $doc_name = $filename;
            
            $Upload->SetFileName($doc_name);
            $Upload->SetTempName($tmpname);
            $Upload->SetUploadDirectory($dirSave); //Upload directory, this should be writable
            $Upload->SetValidExtensions($support_types); //Extensions that are allowed if none are set all extensions will be allowed.
            $Upload->SetMaximumFileSize($maximum); //Maximum file size in bytes, if this is not set, the value in your php.ini file will be the maximum value
            if(!$Upload->UploadFile())
                $res["msg"] = $Upload->GetMessage();
            else
            {
                $res["error"] = false;
                $res["file"] = $doc_name;
                $res["msg"] = "Document Saved.";
            }
        }
        return $res;
    }
    
    /**
     * Crea un paginador con los datos enviados
     * $total_items: number - total items
     * $per_page: number - items por pagina
     * $current_page: pagina actual
     * $url: url de cada pagina
     * $items_show: number - total paginas a mostrar
     */   
    function create_paginator($total_items, $per_page = 4, $current_page = 1, $url, $items_show = 10)
    {
        if($total_items <= $per_page) 
            return "";
        
        if(!$current_page || !is_numeric($current_page))
            $current_page = 1;
        $prev = $next = $pag = "";
        $pages = ceil($total_items/$per_page);
        
        if($items_show%2) //impar
        {
            $paddingLeft = ceil($items_show/2);
            $paddingRight = $paddingLeft - 1; 
        
        }else //par        
            $paddingLeft = $paddingRight = $items_show/2;
        
               
        $start = $current_page - $paddingLeft;
        $to = $current_page + $paddingRight;
            
        if($start <= 1)
        {
            $start = 1;
            $to = $items_show;
            
        }elseif($to >= $pages)
        {
            $to = $pages;
            $start = $pages - $items_show+1;
        }
         
        if($to>$pages)
            $to = $pages;
        
        if( $to-$start == $items_show )
              $start++;

        for($i=$start; $i<=$to; $i++ )
        {   
            if($i==$current_page)
                $pag .= "<li style='float:left;' class='active' ><a href=\"".$url."/ $i\">$i</a></li>" ;
            else
                $pag .= "<li style='float:left;' class='num' ><a href=\"".$url."/ $i\">$i</a></li>" ;
        }
        
        if($start > 1)
        {
            $prev = '<li style=\'float:left;\' class="first">'."<a href='".$url."/1'> &lt;&lt;</a></li>";
            $prev .= '<li style=\'float:left;\' class="previous">'."<a href='".$url."/".($current_page-1)."'> &lt;</a></li>";
        }
        
        if( $to < $pages )
        {
          $next = '<li style=\'float:left;\' class="next">'."<a  href='".$url."/".($current_page+1)."'>&gt;</a></li>";
          $next .= '<li style=\'float:left;\' class="last">'."<a  href='".$url."/$pages'>&gt;&gt;</a></li>";
        }
          
        
        return '<ul id="pages" style=\'overflow:hidden; list-style: none;\'>'    . $prev . $pag.$next. '</ul>';
    }
	
	function viewDirectory($path)
	{
		$dir = opendir($path); 		
 		while ($elemento = readdir($dir))
		{
			echo $elemento."<br>";
		}
	}
    
    function languageText($key_parraf, $key_value = "")
    {
        if(!$key_value)
            $key_value = $_SESSION["language"];
        $text = array(
        "view_map" => array(
                    "es"=>"ver mapa",
                    "ca"=>"veure mapa",
                    "en"=>"view map"
                    ),
                    
        "mat_event" => array(
                    "es"=>"Alquiler material para eventos",
                    "ca"=>"Lloguer de material per a festes",
                    "en"=>"Event equipment and furniture for hire"
                    )
        );
        
        return $text[$key_parraf][$key_value];
    }
    
    /**
     * Corta un texto en una cantidad de caracteres
     * $text: texto completo
     * $quantity: cantidad de caracteres
     * return: (String) texto cortado - ideal para extraer resumen.
     */
    function cutText($text, $quantity = 45)
    {
        $r = substr($text,0,strrpos(substr($text,0,$quantity)," "));
       
        if(strlen($text)>$quantity)
            return $r." ...";
        else
            return $text;
    }
    
    function generateSlug($phrase, $maxLength)
    {
        $result = strtolower($phrase);
    
        $result = preg_replace("/[^a-z0-9\s-]/", "", $result);
        $result = trim(preg_replace("/[\s-]+/", " ", $result));
        $result = trim(substr($result, 0, $maxLength));
        $result = preg_replace("/\s/", "-", $result);
    
        return $result;
    }
    
    /**
     * Envia email en formato html
     * $to: para
     * $title: titulo del email
     * $content: contenido del email
     * $from: email del que envia (opcional)
     * retorna: true si a enviado bien, false si hubo error en el envio
     */
    function sendEmail($to, $title, $content, $from="")
    {
    	if(mail($to, $title, $content, "From: $from \r\nContent-type: text/html\r\n"))
            return true;
        else
            return false;
    }
    
    /**
     * Convierte un xml a array
     * retorna: un array con los valores del xml
     * $contents: contenido del xml 
     */
    function xml2array($contents, $get_attributes=1) 
    {
        if(!$contents) return array();
    
        if(!function_exists('xml_parser_create')) {
            return array();
        }
        $parser = xml_parser_create();
        xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
        xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
        xml_parse_into_struct( $parser, $contents, $xml_values );
        xml_parser_free( $parser );
    
        if(!$xml_values) return;
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = &$xml_array;
    
        foreach($xml_values as $data) {
            unset($attributes,$value);
            extract($data);
            $result = '';
            if($get_attributes) {
                $result = array();
                if(isset($value)) $result['value'] = $value;
    
                if(isset($attributes)) {
                    foreach($attributes as $attr => $val) {
                        if($get_attributes == 1) $result['attr'][$attr] = $val; 
                    }
                }
            } elseif(isset($value)) {
                $result = $value;
            }
            if($type == "open") {
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;
                    $current = &$current[$tag];
    
                } else { 
                    if(isset($current[$tag][0])) {
                        array_push($current[$tag], $result);
                    } else {
                        $current[$tag] = array($current[$tag],$result);
                    }
                    $last = count($current[$tag]) - 1;
                    $current = &$current[$tag][$last];
                }
            } elseif($type == "complete") { 
                if(!isset($current[$tag])) { 
                    $current[$tag] = $result;
                } else { 
                    if((is_array($current[$tag]) and $get_attributes == 0)
                            or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                        array_push($current[$tag],$result); 
                    } else { 
                        $current[$tag] = array($current[$tag],$result);
                    }
                }
            } elseif($type == 'close') {
                $current = &$parent[$level-1];
            }
        }
        return($xml_array);
    }
    
    /**
     * Retrieve all attributes from the shortcodes tag.
     * sample: id='1' a='b'
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     * @param string $text
     * @return array List of attributes and their value.
     */
    function shortcode_parse_atts($text) 
    {
    	$atts = array();
    	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
    	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
    		foreach ($match as $m) {
    			if (!empty($m[1]))
    				$atts[strtolower($m[1])] = stripcslashes($m[2]);
    			elseif (!empty($m[3]))
    				$atts[strtolower($m[3])] = stripcslashes($m[4]);
    			elseif (!empty($m[5]))
    				$atts[strtolower($m[5])] = stripcslashes($m[6]);
    			elseif (isset($m[7]) and strlen($m[7]))
    				$atts[] = stripcslashes($m[7]);
    			elseif (isset($m[8]))
    				$atts[] = stripcslashes($m[8]);
    		}
    	} else {
    		$atts = ltrim($text);
    	}
    	return $atts;
    }
    
    /**
    *
    * Convierte un objeto en Array
    * $object: Object DB
    * return:  array
    */
    function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map(array($this, "objectToArray"), $object );
    }
    
    /**
     * Obtiene el video ID de youtube url
     * $link: url del video de youtube
     * return: ID
     */
    function getYoutubeId($link = '')
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $matches);
        return $matches[0];

    }
    /**
     * crea un bradcrumb links
     * $current: current text
     * $Ancestors: ancestors links, format: array(link=>title)
     * $prefix_text: Texto de inicio
     */
    function breadcrumb($current, $Ancestors = array(), $prefix_text = "You are in: ")
    {
        $res = '';
        foreach($Ancestors as $link=>$key)
        {
            if(strpos($link, "http") === false)
                $link = ROOT_PATH . $link;
            $res .= "<a href='".$link."' title='".$key."'>".$key."</a> -> ";
        }
        echo "<div class='breadcrumb'>".$prefix_text . $res . "<span>$current<span/>"."</div>";
    }
    
    /** 
     * verifica y corrije la existencia de $path, tambien corrige problemas de mayusculas
     * $path: url de archivo o directorio
     * return: (String) path corregido si existe el $path, sino retorna False
     */
    function checkPath($path = null)
    {
        if(!$path)
            return $path;
            
        $custom = false;
        $res = "";
        $parts = explode("/", $path);
        if(count($parts) <= 1)
        {
            $custom = true;
            $parts = explode("/", "./".$path);
        }
            
            
        for($i=0; $i < count($parts); $i++)
        {
            $part = $parts[$i];
            if(is_dir($res.$part))
                $res = $res.$part."/";
            else
            {
                $res = $res = $res.$part;
                break;
            }
            if(!isset($parts[$i+1]))
                continue;
            $band = false;
            foreach(scandir($res) as $dir_file_name)
            {
                if(strtolower($parts[$i+1]) == strtolower($dir_file_name))
                {
                    $parts[$i+1] = $dir_file_name;
                    $band = true;
                }
            }
            
            if(!$band)
                return false;
        }
        
        if($custom)
            $res = str_replace("./", "", $res);
        return $res;
    }
    
    function getFileLink($file, $link, $show_icon = true, $show_name = true, $show_size = false)
    {
        if(!$this->checkPath($file))
            return "File not found.";
            
        $res = "<a target='_blank' href='$link' title='' >";
        $FD = getInstance();
        $FM = $FD->loadLibrary("FD_Fileicon");
        $FM->setFile($file);
        if($show_icon)
            $res = $res . $FM->displayIcon();
        if($show_name)
            $res = $res . $FM->getName();
        
        if($show_size)
            $res = $res . $FM->getSize();
        
        $res = $res . "</a>";
        return $res;
    }
    
    /**
     * Download file with same name into $dir 
     * return filename
     */
    function downloadFile($url, $dir)
    {
        $filename = explode("/", $url);
        $filename = $filename[count($filename) - 1];
        
        if(file_exists($dir.$filename))
        {
            for($ii=0; $ii<9999999; $ii++)
            {
                if(!file_exists($dir.$ii."_".$filename))
                {
                    $doc_name = $ii."_".$filename;
                    break;
                }
            }
        }else
            $doc_name = $filename;

        file_put_contents($dir.$doc_name, file_get_contents($url));
        return $doc_name;
    }
    
    /**
     * detect is source acceded browser is mobile
     */
    function isMobile()
    {
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
            return true;
    }
    
    /**
     * create a folder with permissions
     */
    function createFolder($path, $perms = 0755)
    {
        $old = umask(0);
        mkdir($path, $perms);
        //chmod($path, $perms);
        umask($old);
    }
    
    /**
     * delete file/folder and its contents
     * return true/false
     */
    function deleteFolder($path)
    {
        return is_file($path)?@unlink($path) :array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
    }
}

?>