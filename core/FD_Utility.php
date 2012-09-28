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
    
    function verifyErrorFileUpload($error)
    {
        if(!$error)
            return "";
            
        switch($error)
		{
			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
        return $error;
    }
    
    /**
     * Crea un dropdown o combobox a partir de un arreglo asociativo
    */
	
	function createOptions($attrsElement = array(), $array_values = array(), $selectedKeyValue = "", $text_default = "Select an option")
	{
		$options = "<select ". $this->applyAttrs($attrsElement). ">";
        $options .= "<option value=''> $text_default </option>";  
		
        foreach($array_values as $key => $val)
		{
			$sel= $key == $selectedKeyValue ? " selected=''":"";
			$options .= "<option value='$key' $sel> $val </option>";
		}
		return $options. "</select>";
	}
	
    /**
     * Crea un dropdown o combobox a partir de un arreglo de objetos
    */
    
	function createOptionsObject($attrsElement = array(), $Objects, $attrObjectText, $selectedKeyValue = "", $text_default = "Selecciona una opci&oacute;n")
	{
		$options = "<select ". $this->applyAttrs($attrsElement). ">";
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
     * Crea un grupo de checkbox buttons a partir de un arreglo de objetos
    */
    
    function createGroupChecksObject($attrsPanelContenedor = array(), $Objects, $attrObjectText, $array_check_values = array())
	{
		$options = "<div>";
        
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
                $options .= "<input ". $this->applyAttrs($attrsPanelContenedor). " type='checkbox' ".(key_exists("name", $attrsPanelContenedor)?"":"name = '".$name_obj."[".$primary_key."_".$Obj->$primary_key."]'")." value='".$Obj->$primary_key."' $sel  /> 
				<span> ".$Obj->$attrObjectText1()." </span><br>";   
            }else
                $options .= "<input ". $this->applyAttrs($attrsPanelContenedor). " type='checkbox' ".(key_exists("name", $attrsPanelContenedor)?"":"name = '".$name_obj."[".$primary_key."_".$Obj->$primary_key."]'")." value='".$Obj->$primary_key."' $sel  />
				<span>  ".$Obj->$attrObjectText." </span><br>";
			 
		}
		return $options. "</div>";
	}
    
    /**
     * Crea un grupo de checkbox buttons a partir de un arreglo de objetos
    */
    
    function createGroupChecks($attrsPanelContenedor = array(), $name_check, $array_values = array(), $array_check_values = array())
	{
		$options = "<div ". $this->applyAttrs($attrsPanelContenedor). ">";
        foreach($array_values as $key => $val)
		{
			$sel= in_array($key, $array_check_values) ? " checked=''":"";
			$options .= "<input type='checkbox' name = '".$name_check."[".$key."]' value='".$key."' $sel  /> <span>  ".$val." </span><br>";
		}
        
		return $options. "</div>";
	}
    
    /**
     * Crea un grupo de radio buttons a partir de un arreglo de objetos
    */
    
	function createGroupRadios($attrsPanelContenedor = array(), $name_radio, $array_values = array(), $radio_check_value = "")
	{
		$options = "<div ". $this->applyAttrs($attrsPanelContenedor). ">";
        
        foreach($array_values as $key => $val)
		{
			$sel= $key == $radio_check_value ? " checked=''":"";
			$options .= "<input type='radio' name = '".$name_radio."' value='".$key."' $sel  /> <span>  ".$val." </span><br>";
		}
		return $options. "</div>";
	}
	
    /**
     * Crea un grupo de radio buttons a partir de un arreglo de objetos
    */
    
	function createGroupRadiosObject($attrsPanelContenedor = array(), $Objects, $attrObjectText, $radio_check_value = "")
	{
		$options = "<div ". $this->applyAttrs($attrsPanelContenedor). ">";
        
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
                $options .= "<input type='radio' name = '$primary_key' value='".$Obj->$primary_key."' $sel  /> 
				<span> ".$Obj->$attrObjectText1()." </span><br>";   
            }else
                $options .= "<input type='radio' name = '$primary_key' value='".$Obj->$primary_key."' $sel  />
				<span>  ".$Obj->$attrObjectText." </span><br>";
			 
		}
		return $options. "</div>";
	}
	
    /**
     * Requiere de la libreria ui.datepicker.js , ui.datepicker.css y jquery.js
    */
    
	function createCalendar($attributesInput=array())
	{
		$cal = "<input type='text' ". $this->applyAttrs($attributesInput). "/>";		
		$cal .= "<script> $(\"input[name='".$attributesInput["name"]."']\").addClass(\"calendar\").datepicker({dateFormat: 'dd-mm-yy'}); $(\"input[name='".$attributesInput["name"]."']\").datepicker($.datepicker.regional['es'])</script>";
		return $cal;
	}
    
    
    /**
     * filter an object that have the conditions $conditions.
     * Sample: array("name"=>"owen", ...) when name is attribute of each Object of $objects and owen can be the value of the attribute.
    */
    function filter_objects($objects, $conditions = array())
    {
        $res = array();
        foreach($objects as $object)
        {
            $cant_cond = count($conditions);
            foreach($conditions as $attr_cond=>$val_cond)
            {
                if($object->$attr_cond == $val_cond)
                    $cant_cond--;
            }
            if(!$cant_cond)
                array_push($res, $object);            
        }
        return $res;
    }
    
    /**
     * Crea un timeEntry 
     * Requiere: libreria jquery.timeentry.js y jquery.js
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
    
    function getRealTime($time)
    {
        return strtotime(date("Y-m-d H:i:s", $time) . " + ".TIME_DEFFERENCE." minutes");
    }
    
    function deleteArrayObjects($Objects = array())
    {
        foreach($Objects as $o)
        {
            $o->delete();
        }
    }
    
    /**
     * return array with sub arrays values, where each sub array contain the [0] = imageUploadName and [1] = imageResizeName, [2] = keyInputName
    */
    function uploadMultiplesFiles($names, $dirSave, $generateTumbnail = false, $input_title = "", $resizeW = 100, $resizeH = 100, $resizeMinW = 50, $resizeMinH = 50)
    {        
        $res = array();
        foreach($_FILES["$names"]["name"] as $key=>$val)
        {
            if(!$_FILES["$names"]["name"][$key])
                continue;
            $name = $dirSave.time()."-".$_FILES["$names"]["name"][$key];
            $name = preg_replace("/[^ A-Za-z0-9_|.|\/]/", "", $name);
            move_uploaded_file($_FILES["$names"]["tmp_name"][$key], $name);
            
            // Bajar la calidad de la imagen
            //$this->lowerQuality($name);
            
            if($generateTumbnail)
            {
                $resizeName = $this->resizeImageU($name, $resizeW, $resizeH, "tumbnail-");
                //$resizeName = $this->wideImageResize($name, $resizeW, $resizeH, "tumbnail-");
                $resizeNameS = $this->resizeImageU($name, $resizeMinW, $resizeMinH, "tumbnail-small-");
                //$resizeNameS = $this->wideImageResize($name, $resizeMinW, $resizeMinH, "tumbnail-small-");
            }
            else
            {
                $resizeNameS = "";
                $resizeName = "";
            }
            $name = str_replace("../images/", "", $name);
            array_push($res, array("name"=>$name, "medium"=>$resizeName, "small"=>$resizeNameS, "key"=>$key, "title"=>$_POST["$input_title"][$key]?$_POST["$input_title"][$key]:str_replace($dirSave, "", $name)));
        }
        return $res;
    }
    /**
     * $inputname: input form name
     * $dirSave: dir to upload the file
     * $support_types: type files for upload
     * $maximum: max file  size
     * if file exist, return an array(error= true/false, msg=string, file=filesaved_name)
     * if file not exist, return false
    */
    function uploadFile($inputname, $dirSave, $support_types = array('gif', 'jpg', 'jpeg', 'png'), $maximum = 500000)
    {
        return $this->upload_file($_FILES[$inputname]['name'], $_FILES[$inputname]['tmp_name'], $dirSave, $support_types, $maximum);
    }
    
    /**
     * $inputname: input form filename
     * $tmpname: input form tmpname
     * $dirSave: dir to upload the file
     * $support_types: type files for upload
     * $maximum: max file  size
     * if file exist, return an array(error= true/false, msg=string, file=filesaved_name)
     * if file not exist, return false
    */
    function upload_file($filename, $tmpname, $dirSave, $support_types = array('gif', 'jpg', 'jpeg', 'png'), $maximum = 500000)
    {
        $FD = getInstance();
        $Upload = $FD->loadLibrary("Upload");
        $res = array("error"=>false, "file"=>"", "msg"=>"");
        if($tmpname) 
        {
            $doc_name = time()."_".$filename;
            $Upload->SetFileName($doc_name);
            $Upload->SetTempName($tmpname);
            $Upload->SetUploadDirectory($dirSave); //Upload directory, this should be writable
            $Upload->SetValidExtensions($support_types); //Extensions that are allowed if none are set all extensions will be allowed.
            $Upload->SetMaximumFileSize($maximum); //Maximum file size in bytes, if this is not set, the value in your php.ini file will be the maximum value
            if(!$Upload->UploadFile())
            {
                $res["msg"] = $Upload->GetMessage();
                $res["error"] = true;
            }else
            {
                $res["file"] = $doc_name;
                $res["msg"] = "Document Saved.";
            }
        }else
        {
            return false;
        }
        return $res;
    }
    
    function lowerQuality($file)
    {
        $image = WideImage::load($file);
        
        $fileName = $this->getFileNameWithExtension($file);
        $dir = str_replace($fileName, "", $file);
        $saveName = $dir.$fileName;
        
        $ext = substr($file, -3);
        if(strtolower($ext) == 'jpg')
            $image->saveToFile($saveName, 85);
        if(strtolower($ext) == 'png')
            $image->saveToFile($saveName, 8);
        
    }
    
    function wideImageResize($file, $w, $h, $prefijo)
    {   
        $image = WideImage::load($file);
        $image_w = $image->getWidth();
        $image_h = $image->getHeight();
        
        $fileName = $this->getFileNameWithExtension($file);
        $dir = str_replace($fileName, "", $file);
        $resizeName = $dir.$prefijo.$fileName;
        
        if($image_w/$image_h > 1.05)
        {
            $cropped = $image->crop('center', 'center', ($image_w/$image_h)*$w, ($image_w/$image_h)*$h);
            $resized = $cropped->resize($w, $h, 'fill');
            $resized->saveToFile($resizeName);
        }
        elseif($image_h/$image_w > 1.05)
        {
            $cropped = $image->crop('center', 'center', ($image_h/$image_w)*$w, ($image_h/$image_w)*$h);
            $resized = $cropped->resize($w, $h, 'fill');
            $resized->saveToFile($resizeName);
        }
        else
        {
            $resized = $image->resize($w, $h);
            $resized->saveToFile($resizeName);
        }
        
    }
    
    function resizeImageU($file, $w, $h, $prefijo)
    {
        $image = new FD_ResizeImage();
        $image->load($file);
        $image->resize($w,$h);
        //$image->resizeToHeight($h);
        $fileName = $this->getFileNameWithExtension($file);
        $dir = str_replace($fileName, "", $file);
        $resizeName = $dir.$prefijo.$fileName;
        $image->save($resizeName);
        return  str_replace("../images/", "", $resizeName);
    }
    
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
                $pag .= "<li class=activ ><a href=\"".$url."/ $i\">$i</a></li>" ;
            else
                $pag .= "<li class=num ><a href=\"".$url."/ $i\">$i</a></li>" ;
        }
        
        if($start > 1)
        $prev = '<li class="previous">'."<a href='".$url."/".($current_page-1)."'> &nbsp;&nbsp;</a></li>";
        
        if( $to < $pages )
          $next = '<li class="next">'."<a  href='".$url."/".($current_page+1)."'>&nbsp;&nbsp;</a></li>";
        
        return '<ul id="pages">'    . $prev . $pag.$next. '</ul>';
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
	
	function viewDirectory($path)
	{
		$dir = opendir($path); 		
 		while ($elemento = readdir($dir))
		{
			echo $elemento."<br>";
		}
	}
    
    function embedFileName($file_name, $width = 320)
    {
        switch(strtolower($this->getExtensionFile($file_name)))
        {
            case "jpg":
            case "png":
            case "gif":
            case "bmp":
            case "jpeg":
                return "<img aklt='' src='".$file_name."'>";
            break;
            
            case "fla":
            case "fla":
            case "swf":
            case "avi":
            return '<object width="'.$width.'">
                        <param name="movie" value="'.$file_name.'">
                        <embed src="'.$file_name.'" width="'.$width.'">
                        </embed>
                    </object>';
            break;
            
            case '<object codebase="http://www.apple.com/qtactivex/qtplugin.cab" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" type="application/x-oleobject" height="40">
                        <param name="url" value="'.$file_name.'"/>
                        <embed src="'.$file_name.'" height="40" type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/"></embed> 
                    </object>':
            
            break;
            case "":
            
            break;
            case "":
            
            break;
            
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
                    ),
                    
        "ambientes_menu" => array(
                    "es"=>"AMBIENTES",
                    "ca"=>"AMBIENTS",
                    "en"=>"ATMOSPHERES"
                    ),
                    
        "catalogo_menu" => array(
                    "es"=>"CAT�LOGO",
                    "ca"=>"CAT�LEG",
                    "en"=>"CATALOG"
                    ),
                    
        "proyectos_menu" => array(
                    "es"=>"PROYECTOS",
                    "ca"=>"PROJECTES",
                    "en"=>"PROJECTS"
                    ),
                    
        "contacto_menu" => array(
                    "es"=>"CONTACTO",
                    "ca"=>"CONTACT",
                    "en"=>"CONTACT"
                    ),
        "msg_empty" => array(
                    "es"=>"No existen items",
                    "ca"=>"No existen items",
                    "en"=>"Empty items"
                    ),
        "loading" => array(
                    "es"=>"Cargando...",
                    "ca"=>"Carregant...",
                    "en"=>"Loading..."
                    ),
        "catalogo_label" => array(
                    "es"=>"Cat�logo",
                    "ca"=>"Cat�leg",
                    "en"=>"Catalog"
                    )
        )
        ;
        
        return $text[$key_parraf][$key_value];
    }
    
    function cutText($text, $quantity = 45)
    {
        $r = substr($text,0,strrpos(substr($text,0,$quantity)," "));
       
        if(strlen($text)>$quantity)
            return $r." ...";
        else
            return $text;
    }
    
    function sendEmail($to, $title, $content, $from="")
    {
    	if(mail($to, $title, $content, "From: $from \r\nContent-type: text/html\r\n"))
            return true;
        else
            return false;
    }
    
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
    * Convert an object to an array
    *
    * @param    object  $object The object to convert
    * @reeturn      array
    *
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

    function getYoutubeId($link = '')
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $matches);
        return $matches[0];

    }
    /**
     * Create a breadcrumb links
     * $current: current text
     * $Ancestors: ancestors links, format: array(link=>title)
    ***/
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
     * verify and fix path to file
     * return path fixed for exist file
     */
    function checkPath($path)
    {
        $dirs = explode("/", $path);
        $previous = $dirs[0];
        $dirs = array_slice($dirs, 1, count($dirs)-1);
        foreach($dirs as $dir)
        {
            if(file_exists($previous."/".$dir))
                $previous = $previous."/".$dir;
            elseif(file_exists($previous."/".ucwords($dir)))
                $previous = $previous."/".ucwords($dir);
            elseif(file_exists($previous."/".strtolower($dir)))
                $previous = $previous."/".strtolower($dir);
            elseif(file_exists($previous."/".strtoupper($dir)))
                $previous = $previous."/".strtoupper($dir);
            else
            {
                return false;
            }
        }
        return $previous;
    }
    
    /**
     * return a list of flash messages
    */
    function getFlashMessages()
    {
        $FD = getInstance();
        if(!count($FD->Session->getFlashMessages()))
            return;
        $res = "<ul class='flash_messages'>";
        foreach($FD->Session->getFlashMessages() as $FMessage)
        {
            $res = $res . "<li class='fm_".$FMessage["type"]."'>".$FMessage["message"]."</li>";
        }
        return $res."</ul>";
    }
    
}

?>