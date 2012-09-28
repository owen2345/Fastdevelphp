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

class FD_Session
{
    private $User;
    protected $Connection;
    
    function FD_Session($Con)
    {
        $this->Connection = $Con;
        if(!isset($_SESSION["FD_flash_message"]))
            $_SESSION["FD_flash_message"] = array("new" => array(), "pass"=>array());
    }
    
    /**
     * Verify credentials
     * return true if it is ok, false if it is incorrect
     * 
    */
	function login($uName, $uPass)
	{	   
        $Usuario = $this->Connection->DB->get_object('company_user', "username='$uName'AND password='$uPass'");
        if($Usuario)
		{
			$this->startSession($Usuario);
			return true;
		}else
		{
			return false;
		}
	}
    
    function startSession($Usuario)
    {
        $_SESSION["UID_SESSION"]=$Usuario->id;
        $_SESSION['ULOGIN_SESSION'] = $Usuario->username;
        $_SESSION["FD_SESSION"] = true;
    }
    
    function getUser()
    {
        //echo $this->get_data("UID_SESSION");
        if($this->User)
            return $this->User;
        $this->User = $this->Connection->DB->get_object_by_id("company_user", $this->get_data("UID_SESSION"));
        return $this->User;
    }
	
    /**
     * verifica si estas logeado
     * return true/false
     * si $routeIndex existe y no esta logeado, redirecciona a $routeIndex
     * $publicNameFunctions = Arreglo de nombres de funciones que no requiren login (funciones publicas)
    */
	function check($routeIndex="", $publicNameFunctions = array())
	{
		//if(session_is_registered("account"))
        if(isset($_SESSION["FD_SESSION"]))
        {
            return true;   
        
        }else if(in_array(CONTROLLER_FUNCTION, $publicNameFunctions))
            return true;
        
		elseif($routeIndex)
        {
            $this->add_data(array("after_login"=>$_SERVER["REQUEST_URI"]));
            header("Location: ".ROOT_PATH.$routeIndex);
        }
        else
        {
            return false;
        }
			
	}
	
	function logout($routeIndex = "")
	{
        session_destroy();
        
		if($routeIndex)
			header("Location: ".ROOT_PATH.$routeIndex);		
        else
			header("Location: ".ROOT_PATH);
	}
    
    function unset_data($key)
    {
        unset($_SESSION[$key]);
    }
    
    function get_data($key)
    {
        return isset($_SESSION[$key])?$_SESSION[$key]:'';
    }
    
    function add_data($datas = array())
    {
        foreach($datas as $k => $d)
        {
            $_SESSION[$k] = $d;
        }
    }
    
    function save_toDB()
    {
        
    }
    
    function destroy()
    {
        session_regenerate_id(true);
        session_destroy();
    }
    
    /** Flash messages
     * $msg = message
     * $type = 0 => successful, 1 => fail, 2 => warning
    **/
    function addFlashMessage($key, $msg, $type = 0)
    {
        $_SESSION["FD_flash_message"]["new"][$key] = array("message"=>$msg, "type"=>$type);
    }
    
    function clearFlashMessage()
    {
        $_SESSION["FD_flash_message"]["new"] = $_SESSION["FD_flash_message"]["pass"] = array();
    }
    
    function updateFlashMessage()
    {
        $_SESSION["FD_flash_message"]["pass"] = $_SESSION["FD_flash_message"]["new"];
        $_SESSION["FD_flash_message"]["new"] = array();
    }

    /**
     * $attr: message | type
     * */
    function getFlashMessage($key, $attr='message')
    {
        if(isset($_SESSION["FD_flash_message"]["pass"][$key]))
            return $_SESSION["FD_flash_message"]["pass"][$key][$attr];
    }
    
    function getFlashMessages()
    {
        if(isset($_SESSION["FD_flash_message"]["pass"]))
            return $_SESSION["FD_flash_message"]["pass"];
        else
            array();
    }
}
?>