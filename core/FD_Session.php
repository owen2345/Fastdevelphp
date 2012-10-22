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
     * Inicia la session con los datos del usuario $Usuario
     * $Usuario: objeto DB
     * retorna: null
     * NOTA: adaptar consulta de acuerdo a la base de datos
     */
    function startSession($User)
    {
        $_SESSION["UID_SESSION"]=$User->id;
        $_SESSION['ULOGIN_SESSION'] = $User->username;
        $_SESSION["FD_SESSION"] = true;
    }
    
    /**
     * Verifica credenciales enviadas
     * $uName: username
     * $uPass: password
     * Retorna: true si se a logeado bien, false si a fallado
     * NOTA: adaptar consulta de acuerdo a la base de datos 
    */
	function login($uName, $uPass)
	{	   
        $User = $this->Connection->DB->get_object('user', "username='$uName'AND password='$uPass'");
        if($User)
		{
			$this->startSession($User);
			return true;
		}else
		{
			return false;
		}
	}
    
    /**
     * obtiene el usuario de la session
     * $Usuario: objeto DB
     * retorna: null
     * NOTA: adaptar consulta de acuerdo a la base de datos
     */
    function getUser()
    {
        if($this->User)
            return $this->User;
        $this->User = $this->Connection->DB->get_object_by_id("user", $this->get_data("UID_SESSION"));
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
	
    /**
     * Destruye la session
     * redirecciona a ROOT_PATH/$routeIndex
     */
	function logout($routeIndex = "")
	{
        session_destroy();
        
		if($routeIndex)
			header("Location: ".ROOT_PATH.$routeIndex);		
        else
			header("Location: ".ROOT_PATH);
	}
    
    /**
     * elimina un valor de la session
     */
    function unset_data($key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * retorna: el valor de $key en la session
     */
    function get_data($key)
    {
        return isset($_SESSION[$key])?$_SESSION[$key]:'';
    }
    
    /**
     * agrega valores en la session
     * Ejm: $datas("name"=>"owen", "app"=>"peredo")
     */
    function add_data($datas = array())
    {
        foreach($datas as $k => $d)
        {
            $_SESSION[$k] = $d;
        }
    }
         
    /** 
     * Agrega un nuevo flashmessage
     * $key: nombre del identificador
     * $msg: valor o mensaje
     * $type: number = 0 => successful, 1 => fail, 2 => warning
    **/
    function addFlashMessage($key, $msg, $type = 0)
    {
        $_SESSION["FD_flash_message"]["new"][$key] = array("message"=>$msg, "type"=>$type);
    }
    
    /**
     * elimina el actual flashmessage
     * DEPRECADO
     */
    function clearFlashMessage()
    {
        $_SESSION["FD_flash_message"]["new"] = $_SESSION["FD_flash_message"]["pass"] = array();
    }
    
    /**
     * actualiza el actual flashmessage
     * DEPRECADO
     */
    function updateFlashMessage()
    {
        $_SESSION["FD_flash_message"]["pass"] = $_SESSION["FD_flash_message"]["new"];
        $_SESSION["FD_flash_message"]["new"] = array();
    }

    /**
     * obtiene el flashmessage con identificador $key
     * $key: identificador del mensaje
     * $attr: valor a retornar, puede ser: 
     *      message = retorna el texto del mensaje
     *      key = retorna el tipo de mensaje
     *      null = el mensaje completo
     * retorna: el tipo definido en $attr
     **/
    function getFlashMessage($key, $attr='message')
    {
        if(isset($_SESSION["FD_flash_message"]["pass"][$key]))
            return $_SESSION["FD_flash_message"]["pass"][$key][$attr];
    }
    
    /**
     * retorna todos los flashmessages
     */
    function getFlashMessages()
    {
        if(isset($_SESSION["FD_flash_message"]["pass"]))
            return $_SESSION["FD_flash_message"]["pass"];
        else
            array();
    }
    
    /**
     * imprime todos los flashmessages
    */
    function printFlashMessages()
    {
        $FD = getInstance();
        if(!count($this->getFlashMessages()))
            return;
        $res = "<ul class='flash_messages'>";
        foreach($this->getFlashMessages() as $FMessage)
        {
            $res = $res . "<li class='fm_".$FMessage["type"]."'>".$FMessage["message"]."</li>";
        }
        echo $res."</ul>";
    }
}
?>