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
class Admin_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("fastdevelphp/login");
	}
    
    function login()
    {
        $this->loadView("login");
    }
    
    function logout()
    {
        $this->Session->logout("admin/login");
    }
    
    function check_login()
    {
        if($this->Session->login($this->Request->getParam_POST("email"), $this->Request->getParam_POST("password")))
        {
            $User = $this->Session->getUser();
            if($User->isverified_user == 0)
            {
                $link = ROOT_PATH."admin/resend_code/".$User->email_user;
                $this->Session->addFlashMessage("login", "Please check your email to active your account or <a href='$link'>Send again here.</a>");
                $this->redirect("admin", "login");
                return;
            }
            
            if($this->Session->get_data("after_login"))
                $this->redirect($this->Session->get_data("after_login"));
            else
                $this->redirect("admin", "dashboard");
                
        }else
        {
            $this->Session->addFlashMessage("login", "Please check your datas");
            $this->redirect("admin", "login");
        }
    }
    
    /**
     * forgot your password
    */
    function forgot()
    {
        $User = $this->DB->get_object_by_attribute("user", "email_user", $this->Request->getParam_POST("email"));
        if($User)
        {
            $this->Utility->sendEmail($this->Request->getParam_POST("email"), "Forgot your password", "You have requested forgot your password.<br>Password: ".$User->pass_user);
            $this->Session->addFlashMessage("forgot", "Your password was sended to ".$this->Request->getParam_POST("email").".", 0);
        }else
            $this->Session->addFlashMessage("forgot", "This email is not registered.", 1);
        $this->redirect("admin", "login");
    }
    
    function resend_code($email_user)
    {
        $User = $this->DB->get_object_by_attribute("user", "email_user", $this->Request->getParam_GET("email"));
        if($User)
        {
            $User->sendVerify_email();
            $this->Session->addFlashMessage("verify", "The email was sended to your email, please check it.");
        }else
        {
            $this->Session->addFlashMessage("verify", "Error on send the verify code.", 1);
        }
        $this->redirect("admin", "login");
    }
    
    function verify($code)
    {
        $User = $this->DB->get_object_by_attribute("user", "code_user", $code);
        if($User)
        {
            if($User->isVerified())
                $this->Session->addFlashMessage("verify", "You have already verified your account.", 2);
            else
            {
                $User->setVerify();
                $this->Session->addFlashMessage("verify", "Your account was verified successfully.");
            }
            
        }else
        {
            $this->Session->addFlashMessage("verify", "Incorrect verify code.", 1);
        }
        
        $this->redirect("admin", "login");
    }
}
?>