<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Owen Peredo" />

	<title>Console-FastDevelPHP</title>
    <style type="text/css">
<!--
	body                    { background: black; margin: 0px auto; color: white;  }
    .content_console        { border: 2px solid gray;   }
    #logs_console           { border: 1px solid gray; height: 400px; overflow: auto;  }
    #console                { background: black; width: 850px; height: 150px; color: white; }
    #console_wrapper, #controles_console        { margin: 0px auto; text-align: center;  }
    li.result               { list-style: none;  }
-->
</style>
<script src="<?php echo JS_PATH ?>jquery.js"></script>
<script>
    var ROOT_PATH = "<?php echo ROOT_PATH ?>";
    jQuery(function($)
    {
       $("#correr_console").click(function()
       {
          if($("#console").val())
              $.post(window.location.href, { instruccion :  $("#console").val().replace(/"/g, "-_owen_-")  }, function(res)
              {
                    $("#lista_logs").append("<li class ='instr'>"+$("#console").val()+"</li>");
                    $("#lista_logs").append("<li class ='result'>====><br>"+res+"<br><br></li>");
                    $("#console").val("");
                    $("#logs_console").scrollTop($("#logs_console")[0].scrollHeight);
              });  
       });
       
       $("#limpiar_console").click(function(){   if(confirm("Esta seguro de limpiar Consola???")) $.post(window.location.href, { clearConsole: true }, function(res){  $("#lista_logs").children("li").remove();      });   });
       
       $("#opciones_comun").change(function(){  $("#console").val($("#console").val()+$(this).val());  });
    });
</script>
</head>

<body>

    <div class="content_console">
        <div id="logs_console">
            <ul id="lista_logs">
                <li>INSTRUCCIONES ANTERIORES= <?php echo $this->loadLogConsole() ?></li>
            </ul>
        </div>
        <div id="console_wrapper">
            <textarea id="console"></textarea>
        </div>
        <div id="controles_console">
            <input type="button" id="correr_console" value="Correr" />
            <input type="button" id="limpiar_console" value="Limpiar" />            
            <select id="opciones_comun">
                <option value="">Seleccione una Opcion</option>
                <option value="$this->Connection->DB->get_objects_By_Sql();">get_objects_By_Sql</option>
                <option value="$this->Connection->DB->get_objects();">get_objects</option>
                <option value="$this->Connection->DB->get_object_by_id();">get_object_by_id</option>
                <option value="$this->Connection->DB->get_object();">get_object</option>
                <option value="$this->Connection->DB->create_object();">create_object</option>
                <option value="$this->Connection->execQuery();">execQuery</option>                
            </select>
        </div>

    </div>
    
</body>
</html>