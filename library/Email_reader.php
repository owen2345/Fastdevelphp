<?php
class Email_reader
{
    var $host;
    var $user;
    var $pass;
    var $save_dir;
    function __construct($host, $user, $pass, $save_dir = './')
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->save_dir = $save_dir;
    }
    
	function getdecodevalue($message,$coding) 
    {
		switch($coding) {
			case 0:
			case 1:
//				$message = imap_8bit($message);
				$message = imap_base64($message);
				break;
			case 2:
				$message = imap_binary($message);
				break;
			case 3:
			case 5:
				$message = imap_base64($message);
				break;
			case 4:
				$message = imap_qprint($message);
				break;
		}
		return $message;
	}
    
    
    function getAttachs($index, $mid, $mbox = null)
    {
        $res = array();
        if(!$mbox)
            $mbox = imap_open ('{'.$this->host.'/notls}', $this->user, $this->pass) or die("can't connect: " . imap_last_error());
        $savedirpath = $this->save_dir;
    	$structure = imap_fetchstructure($mbox, $mid , FT_UID);
    	if (!isset($structure->parts)) return $res;
    
    	$parts = $structure->parts;
    	$fpos=2;
    	for($i = 1; $i < count($parts); $i++) 
        {
    		$message["pid"][$i] = ($i);
    		$part = $parts[$i];
    
    		if(isset($part->disposition) && strtoupper($part->disposition) == "ATTACHMENT") 
            {
    			$filename=$part->dparameters[0]->value;
    			$mege = imap_fetchbody($mbox,$index+1,$fpos);
                $res[] = $savedirpath.$filename;  
    			$fp=fopen($savedirpath.$filename, 'w');
    			$data=$this->getdecodevalue($mege,$part->type);	
    			fwrite($fp,$data);
    			fclose($fp);
    			$fpos+=1;
    		}
    	}
        return $res;
    }

	function getMails() 
    {
        $emails = array();
        $savedirpath = $this->save_dir;
		$nbattach = 0;
		$savedirpath = str_replace('\\', '/', $savedirpath);
		if (substr($savedirpath, strlen($savedirpath) - 1) != '/') {
			$savedirpath .= '/';
		}
		
		$mbox = imap_open ('{'.$this->host.'/notls}', $this->user, $this->pass) or die("can't connect: " . imap_last_error());
		
		$ids = imap_search($mbox, 'ALL', SE_UID);
		for ($jk = 0; $jk < count($ids); $jk++) 
        {
			$structure = imap_fetchstructure($mbox, $ids[$jk] , FT_UID);
            $email = array(
                    'index'     => $jk,
	                'mid'       => $ids[$jk],
	                'header'    => imap_headerinfo($mbox, $ids[$jk]),
	                'body'      => imap_body($mbox, $ids[$jk]),
	                'structure' => imap_fetchstructure($mbox, $ids[$jk]),
                    'attach' => $this->getAttachs($jk, $ids[$jk], $mbox)
	            );
            if ($delete_emails)
				imap_delete($mbox,$jk+1);
			
            $emails[] = $email;
		}
        if ($delete_emails)
			imap_expunge($mbox);
            
		imap_close($mbox);
		return $emails;
	}
}
?>