<?php
class FD_Fileicon{
	var $filename;
	var $size;
	
	var $icons = array(
	
	// Microsoft Office
	'doc' => array('doc', 'Word Document'),
	'xls' => array('xls', 'Excel Spreadsheet'),
	'ppt' => array('ppt', 'PowerPoint Presentation'),
	'pps' => array('ppt', 'PowerPoint Presentation'),
	'pot' => array('ppt', 'PowerPoint Presentation'),

	'mdb' => array('access', 'Access Database'),
	'vsd' => array('visio', 'Visio Document'),
//	'xxxx' => array('project', 'Project Document'), 	// dont remember type...
	'rtf' => array('rtf', 'RTF File'),

	// XML
	'htm' => array('htm', 'HTML Document'),
	'html' => array('htm', 'HTML Document'),
	'xml' => array('xml', 'XML Document'),

	 // Images
	'jpg' => array('image', 'JPEG Image'),
	'jpe' => array('image', 'JPEG Image'),
	'jpeg' => array('image', 'JPEG Image'),
	'gif' => array('image', 'GIF Image'),
	'bmp' => array('image', 'Windows Bitmap Image'),
	'png' => array('image', 'PNG Image'),
	'tif' => array('image', 'TIFF Image'),
	'tiff' => array('image', 'TIFF Image'),
	
	// Audio
	'mp3' => array('audio', 'MP3 Audio'),
	'wma' => array('audio', 'WMA Audio'),
	'mid' => array('audio', 'MIDI Sequence'),
	'midi' => array('audio', 'MIDI Sequence'),
	'rmi' => array('audio', 'MIDI Sequence'),
	'au' => array('audio', 'AU Sound'),
	'snd' => array('audio', 'AU Sound'),

	// Video
	'mpeg' => array('video', 'MPEG Video'),
	'mpg' => array('video', 'MPEG Video'),
	'mpe' => array('video', 'MPEG Video'),
	'wmv' => array('video', 'Windows Media File'),
	'avi' => array('video', 'AVI Video'),
	
	// Archives
	'zip' => array('zip', 'ZIP Archive'),
	'rar' => array('zip', 'RAR Archive'),
	'cab' => array('zip', 'CAB Archive'),
	'gz' => array('zip', 'GZIP Archive'),
	'tar' => array('zip', 'TAR Archive'),
	'zip' => array('zip', 'ZIP Archive'),
	
	// OpenOffice
	'sdw' => array('oo-write', 'OpenOffice Writer document'),
	'sda' => array('oo-draw', 'OpenOffice Draw document'),
	'sdc' => array('oo-calc', 'OpenOffice Calc spreadsheet'),
	'sdd' => array('oo-impress', 'OpenOffice Impress presentation'),
	'sdp' => array('oo-impress', 'OpenOffice Impress presentation'),

	// Others
	'txt' => array('txt', 'Text Document'),	
	'js' => array('js', 'Javascript Document'),
	'dll' => array('binary', 'Binary File'),
	'pdf' => array('pdf', 'Adobe Acrobat Document'),
	'php' => array('php', 'PHP Script'),
	'ps' => array('ps', 'Postscript File'),
	'dvi' => array('dvi', 'DVI File'),
	'swf' => array('swf', 'Flash'),
	'chm' => array('chm', 'Compiled HTML Help'),

	// Unkown
	'default' => array('txt', 'Unkown Document'),
	);


	/**
	* Constructor of class
	* @param string $filename filename
	* @desc Constructor of class
	*/
	function setFile($filename){
		$this -> filename = $filename;
		$this -> size = filesize($this -> filename);
	}


	/**
	* Returns file size
	* @return string
	* @desc Returns file size
	*/	
	function getSize() {
        return $this -> evalSize($this -> size);
	}

	
	/**
	* Returns the timestamp of the last change
	* @return timestamp $timestamp The time of the last change as timestamp
	* @desc Returns the timestamp of the last change
	*/
	function getTime(){
		return fileatime($this -> filename);
	}

	/**
	* Returns the filename
	* @return string $filename The filename
	* @desc Returns the filename
	*/
	function getName(){
        $extension = explode("/", $this -> filename) ;
        $extension = $extension[count($extension)-1];
        return $extension;
	}

	/**
	* Returns user id of the file
	* @return string $user_id The user id of the file
	* @desc Returns user id of the file
	*/
	function getOwner(){
		return fileowner($this -> filename);
	}

	/**
	* Returns group id of the file
	* @return string $group_id The group id of the file
	* @desc Returns group id of the file
	*/
	function getGroup(){
		return filegroup($this -> filename);
	}

	/**
	* Returns the suffix of the file
	* @return string $suffix The suffix of the file. If no suffix exists FALSE will be returned
	* @desc Returns the suffix of the file
	*/
	function getType(){
		$file_array=explode(".",$this -> filename); // Splitting prefix and suffix of real filename
		$suffix=$file_array[count($file_array)-1]; // Returning file type
		if(strlen($suffix)>0){
			return $suffix;
		}else{
			return false;
		}
	}
	
	/**
	* Returns the size of the file
	* @param int $size
	* @return int
	* @desc Returns the size of the file
	*/	
	function evalSize($size) {
		if ($size >= 1073741824) return round($size / 1073741824 * 100) / 100 . " GB";
		elseif ($size >= 1048576) return round($size / 1048576 * 100) / 100 . " MB";
		elseif ($size >= 1024) return round($size / 1024 * 100) / 100 . " KB";
		else return $size . " BYTE";	
	}
	
	/**
	* Returns the icon
	* @desc Returns the icon of the file
	*/
	function getIcon() {
		$extension = $this -> getType();

		if (key_exists($extension, $this -> icons)) return $this -> icons[$extension];
		else return $this -> icons['default'];
	}
	
	/**
	* Display Icon
	* @return string $size
	* @desc Display Icon
	*/	
	function displayIcon() {
		$array = $this -> getIcon();
		return '<img src="' . ROOT_PATH ."images/fastdevelphp/file_icons/" . $array[0] . '.gif" alt="' . $array[1] . '" />';
	}

}
?>