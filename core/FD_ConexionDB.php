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
	
	class FD_ConexionDB
	{		
		private $results_query;
		private $connection;
		var $foreing_kes=array();
		function FD_ConexionDB()
		{
			fd_log("\n\n--------------------VISITED AT: ".date("Y-m-d H:i:s")."--------------------", true);
            if(USE_DB != "false" && USE_DB != "FALSE")
            {
                if(!$this->connection = mysql_connect(HOST_DB,USER_DB,PSSWD_DB))
    				dieFastDevel("an error has occurred into connection db: " . mysql_error());
    			if(!mysql_select_db(NAME_DB))
    				dieFastDevel("La Base de Datos :\"".NAME_DB."\" no existe.<br> Por Favor verifique que esta exista.");			
    			$this->retractDB(NAME_DB, PREFIX_TABLE_IGNORE);    			
            }            
		}
        		
		function execQuery($query, $show_query = false, $skypeLog = false)
		{
            if(!$skypeLog)
                fd_log($query);
            if(USE_DB == "false" || USE_DB == "FALSE")
                dieFastDevel("No puede realizar conexiones a MYSQL, debido a que tiene configurado trabajar sin Base de Datos.<br>
                Para modificar la configuracion, refierase a la verificacion de fd_config.php");
			if(!$this->results_query=mysql_query($query))
            {
                $e="Se ha producido un error al ejecutar la consulta: <br> $query <br> 
				<b>Error generado</b>: ".mysql_error()." <br><b>Detalles</b>: <br>";
                $e .= FD_getDebug();
				dieFastDevel($e);
            }
            
            if($show_query)
                echo $query;
		}
		
		function getArrayResultsQuery()
		{
			$array_results=array();
			for ($i=0; $i <mysql_num_rows($this->results_query); $i++)
  			{
     			array_push($array_results,mysql_fetch_row($this->results_query));			
 			}
			return $array_results; 
		}
		
		function getResultsQuery()
		{
			$array_results=array();
			for ($i=0; $i <mysql_num_rows($this->results_query); $i++)
  			{
     			array_push($array_results,mysql_fetch_assoc($this->results_query));			
 			}
			return $array_results;			
		}
		
		function getNewID($tabla,$columna)
		{
            $consulta="SELECT IFNULL(MAX(".$columna."),0)+1 as idn FROM ".strtolower($tabla);
            $qq = mysql_query($consulta);
            $aa = mysql_fetch_assoc($qq);
            return $aa["idn"]?$aa["idn"]:"NULL";
		}
		
		function getKeysTable($table_name)
		{
			$this->execQuery("SHOW COLUMNS FROM ".strtolower($table_name), false, true);
			$fields = $this->getResultsQuery();			
			$primary_keys=array();
            $hasPrimaryKey = false;
			foreach($fields as $field)
			{
				$field["Field"]=strtolower($field["Field"]);
				if($field["Key"] == "PRI")
				{
				    $hasPrimaryKey = true;
					array_push($primary_keys,$field["Field"]);
                    break;
				}
			}
            if(!$hasPrimaryKey)
                dieFastDevel("No existe Primary Key para la tabla $table_name.<br><br>Por favor verifique la tabla $tabla_name en la Base de Datos");
			return $primary_keys;
		}
		
function retractDB($db, $prefix_ignore = "")
{
	$this->execQuery("SHOW TABLES FROM ".strtolower($db), false, true);
	$tables=$this->getResultsQuery();
	foreach($tables as $table)
	{
	    if($prefix_ignore && strpos(strtolower(current($table)), strtolower($prefix_ignore)) === 0)
            continue;
		$this->foreing_kes[ucwords(current($table))]=$this->getKeysTable(ucwords(current($table)));
		if(!file_exists(MODELS_PATH.ucwords(current($table)). '.php'))
		{
			$this->execQuery("SHOW COLUMNS FROM ".strtolower(current($table)), false, true);
			$fields = $this->getResultsQuery();
			$textFields = "";
			$textFieldsEqual = "";
			$textFieldsConstruct = "";			
			foreach($fields as $field)
			{
				//print_r($field);echo "<br>";
				$field["Field"]=strtolower($field["Field"]);
				if($textFieldsConstruct)
					$textFieldsConstruct.=", ";
                if(strtoupper($field["Default"]) == "NULL")
                    $textFieldsConstruct.= "\$".$field["Field"]." = null";
				else
                    $textFieldsConstruct.= "\$".$field["Field"]." = ''";				
				$textFields.= "
	var \$".$field["Field"]."; ";
				$textFieldsEqual.= "
		\$this->".$field["Field"]." = \$".$field["Field"].";";
			}
			
$fp = fopen(MODELS_PATH.ucwords(current($table)). '.php',"a+");
fwrite($fp,"<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company \"Sysdecom\" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */ 
class ".ucwords(current($table))." extends FD_ManageModel
{	
    var \$alias_of_atributes = array();
    var \$fd_rules = array();
	$textFields
	
	function ".ucwords(current($table))."($textFieldsConstruct)
	{
		$textFieldsEqual
	}
}
?>");
fclose($fp);
		}
	}
}
		
	}


?>