
        <div class='form_register simplebox' id='form_Backlog'>
            <script>
                jQuery(function($)
                {
                    $("#form_Backlog form").validate();
                });
            </script>
            
       	    <div class="titleh"><h3>Formulario Backlog</h3></div>
                            
            <div class="body">
                <form method='post' action='<?php echo ROOT_PATH ?>admin/Backlog/<?php echo $action ?>' enctype=''>
                    <input type='hidden' name='id_backlog' value='<?php  echo $Backlog->id_backlog ?>' />
                    <ul>
                        <li>
                            <label class='label_form'>id</label>
                            <input type='text' name='id_backlog' class='input_text required number' value='<?php echo $Backlog->id_backlog ?>' />
                        </li>
                        <li>
                            <label class='label_form'>date</label>
                            <input type='text' name='createat_backlog' class='input_text required date' value='<?php echo $Backlog->createat_backlog ?>' />
                        </li>
                        <li class='controls'>
                            <button type='submit'><?php echo $submitText ?></button>
                            <button type='button' onclick='history.back();'>Cancelar</button>
                        </li>            
                    </ul>
                </form>
            </div>
        </div>