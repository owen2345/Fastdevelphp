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
        var $tables = array();
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
        
        /**
         * Ejecuta una consulta SQL
         * $query: consulta sql
         * $show_query: opcion para imprimir el sql
         * $skypeLog: deprecado
         * */
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
		
        /**
         * retorna: un arreglo con keys numerales de la consulta realizada por execQuery()
         * */
		function getArrayResultsQuery()
		{
			$array_results=array();
			for ($i=0; $i <mysql_num_rows($this->results_query); $i++)
  			{
     			array_push($array_results,mysql_fetch_row($this->results_query));			
 			}
			return $array_results; 
		}
		
        /**
         * retorna: la respuesta a la consulta realizada por execQuery()
         * */
		function getResultsQuery()
		{
			$array_results=array();
			for ($i=0; $i <mysql_num_rows($this->results_query); $i++)
  			{
     			array_push($array_results,mysql_fetch_assoc($this->results_query));			
 			}
			return $array_results;			
		}
		
        /**
         * deprecado
         * */
		function getNewID($tabla,$columna)
		{
            $consulta="SELECT IFNULL(MAX(".$columna."),0)+1 as idn FROM ".strtolower($tabla);
            $qq = mysql_query($consulta);
            $aa = mysql_fetch_assoc($qq);
            return $aa["idn"]?$aa["idn"]:"NULL";
		}
		
        /**
         * deprecado
         * */
		function getKeysTable($table_name)
		{
            $table_name = ucwords($table_name);
            if(key_exists($table_name, $this->foreing_kes))
                return $this->foreing_kes[$table_name];
                
			$this->execQuery("SHOW COLUMNS FROM ".strtolower($table_name), false, true);
			$fields = $this->getResultsQuery();
            $primary_key = "";
			foreach($fields as $field)
			{
				$field["Field"]=strtolower($field["Field"]);
				if($field["Key"] == "PRI")
				{
				    $this->foreing_kes[$table_name] = $primary_key = $field["Field"];				    
                    break;
				}
			}
            
            if(!$primary_key)
                dieFastDevel("No existe Primary Key para la tabla $table_name.<br><br>Por favor verifique la tabla $tabla_name en la Base de Datos o Asigne fd_primary_key en el modelo.");
                
			return $primary_key;
		}
        
        /**
         * retorna: un array() con las tablas de la base de datos actual
         **/
        function getDatabaseTables()
        {
            $res = array();
            $this->execQuery("SHOW TABLES FROM ".strtolower(NAME_DB), false, true);
	        $tables=$this->getResultsQuery();
            foreach($tables as $table)
            {
                if(PREFIX_TABLE_IGNORE && strpos(strtolower(current($table)), strtolower(PREFIX_TABLE_IGNORE)) === 0)
                    continue;
                $res[] = ucwords(current($table));
            }
            return $res;
        }
        
        /**
         * $table: nombre de la tabla
         * retorna: un array() con las columnas de la tabla $table
         **/
        function getFieldsTable($table = null)
        {
            $this->execQuery("SHOW COLUMNS FROM ".strtolower($table), false, true);
			return $this->getResultsQuery();
        }
		
/**
 * genera los modelos de las tablas de la base de datos $db ignorando las tablas que tengan como prefijo $prefix_ignore
 * $db: nombre de la base de datos
 * $prefix_ignore: string prefijo
 * retorna: null
 **/
function retractDB($db, $prefix_ignore = "")
{
	$tables = $this->getDatabaseTables();
	foreach($tables as $table)
	{
        $this->tables[] = strtolower($table);
		if(!file_exists(MODELS_PATH.$table. '.php'))
		{
            $primary_key = $this->getKeysTable($table);
			$fields = $this->getFieldsTable($table);
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
				elseif(is_numeric($field["Default"]))
                    $textFieldsConstruct.= "\$".$field["Field"]." = ".$field["Default"];
                else
                    $textFieldsConstruct.= "\$".$field["Field"]." = ''";
                                				
				$textFields.= "
	var \$".$field["Field"]."; ";
				$textFieldsEqual.= "
		\$this->".$field["Field"]." = \$".$field["Field"].";";
			}
			
$fp = fopen(MODELS_PATH.$table. '.php',"a+");
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
class ".$table." extends FD_ManageModel
{	
    var \$alias_of_atributes = array();
    var \$fd_rules = array();
    var \$fd_primary_key = '$primary_key';
	$textFields
	
	function __construct($textFieldsConstruct)
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