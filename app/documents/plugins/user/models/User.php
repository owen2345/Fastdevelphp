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
class User extends FD_ManageModel
{	
    var $alias_of_atributes = array();
    var $fd_rules = array();
    var $fd_primary_key = 'id_user';
	
	var $id_user; 
	var $id_plan; 
	var $name_user; 
	var $lastname_user; 
	var $email_user; 
	var $pass_user; 
	var $role_user; 
	var $createat_user; 
	var $avatar_user; 
	var $code_user; 
	var $isverified_user; 
	
	function __construct($id_user = '', $id_plan = '', $name_user = '', $lastname_user = '', $email_user = '', $pass_user = '', $role_user = '', $createat_user = '', $avatar_user = '', $code_user = 0, $isverified_user = 0)
	{
		
		$this->id_user = $id_user;
		$this->id_plan = $id_plan;
		$this->name_user = $name_user;
		$this->lastname_user = $lastname_user;
		$this->email_user = $email_user;
		$this->pass_user = $pass_user;
		$this->role_user = $role_user;
		$this->createat_user = $createat_user;
		$this->avatar_user = $avatar_user;
		$this->code_user = $code_user;
		$this->isverified_user = $isverified_user;
	}
    
    function onSave()
    {
        $this->code_user = time();
    }
    
    function afterSave()
    {
        $this->sendVerify_email();
    }
    
    /**
     * send an email to User with his verify code.
     */
    function sendVerify_email()
    {
        $FD = getInstance();
        $link = ROOT_PATH."admin/verify/".$this->code_user;
        $FD->Utility->sendEmail($this->email_user, "Register account", "Your account was created successfully.<br>Please confirm your register with this <a href='$link'>link</a> or copy this link in your browser $link");
    }
    
    /**
     * check if user is already verified
     */
    function isVerified()
    {
        return $this->isverified_user;
    }
    
    /**
     * update status to verified
     */
    function setVerify()
    {
        $this->setAttr('isverified_user', 1)->update();
    }
}
?>