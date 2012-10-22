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
class Home_Controller extends FD_Management
{	
	function __construct()
	{
        parent::__construct();
        $this->useLayout("public");
	}
    
    function index()
    {
        $datas = array();
        $this->loadView("home", $datas);
    }
}
	
?>