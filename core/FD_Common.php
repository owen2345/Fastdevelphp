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

class FD_Common
{   
    function FD_Common()
    {
        
    }
    
    function console()
    {
        if(ENABLE_CONSOLE == "false" || ENABLE_CONSOLE == "FALSE")
            dieFastDevel("La consola de Test esta Deshabilidado.");
        if(isset($_POST["instruccion"]) && $_POST["instruccion"])
            $this->executeConsole();
        elseif(isset($_POST["clearConsole"]) && $_POST["clearConsole"])
            $this->clearConsole();
        else
        {
            if(file_exists("../logs/consoleView.php"))
    	       include("../logs/consoleView.php");
            else
                dieFastDevel("No existe el siguiente archivo \"logs/consoleView.php\".");
        }
    }
    
    private function loadLogConsole()
    {
        //$consolePHP = fopen("../logs/console.php", "w+"); 
        $f = fopen("../logs/console.txt", "r");
        fpassthru($f);
        
        //include("../logs/console.php");
    }
	
    private function executeConsole()
    {
        $_POST["instruccion"] = str_replace("-_owen_-", "\"", $_POST["instruccion"]);
        ob_start();        
        $consolePHP = fopen("../logs/console.php", "w+");
        fwrite($consolePHP, "<?php ".$_POST["instruccion"]." ?>");
        include("../logs/console.php");        
        
        $fp = fopen("../logs/console.txt", "a+");
        fwrite($fp, "<br><br> echo('".$_POST["instruccion"]." <br>====><br>');\n ".ob_get_contents());
        $consolePHP = fopen("../logs/console.php", "w");
        fwrite($consolePHP, "");
        //fwrite($fp, "echo \"<br>\"; \n echo('".$_POST["instruccion"]." <br>====><br>');\n".$_POST["instruccion"]."\n echo \"<br><br>\";");
    }
    
    private function clearConsole()
    {
        if(file_exists("../logs/console.txt"))        
        {
            unlink("../logs/console.txt");
            $fp = fopen("../logs/console.txt", "a+");
            unlink("../logs/console.php");
            $fp = fopen("../logs/console.php", "a+");
        }
    }
    
    /**
     * sample: $this->FD_scaffold("user", array("login"=>"input", "nombres_u"=>"textarea"));
    
    function FD_scaffold($tableName = "", $datas, $simple = true, $module_name = null)
    {
        new FD_Scafold($tableName, $datas, $this->Connection, $simple, $module_name);
    }*/
}

?>