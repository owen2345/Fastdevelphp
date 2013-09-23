
        <div class='form_register' id='form_Retrasos'>
            <script>
                jQuery(function($)
                {
                    $("#form_Retrasos form").validate();
                });
            </script>
            <h2>Formulario Retrasos</h2>
            <form method='post' action='<?php echo ROOT_PATH ?>Retrasos/<?php echo $action ?>' enctype=''>
                <input type='hidden' name='id_retraso' value='<?php  echo $Retrasos->id_retraso ?>' />
                <ul>
                    <li>
                        <label class='label_form'>Usuario</label>
                        <?php echo $this->Utility->createOptionsObject(array("name"=>"id", "class"=>"input_select required"), $Company_users, 'name', $Retrasos->id); ?>
                    </li>
                    <li>
                        <label class='label_form'>Fecha</label>
                        <input type='text' name='date_retraso' class='input_text required, date' value='<?php echo $Retrasos->date_retraso ?>' />
                    </li>
                    <li>
                        <label class='label_form'>Tiempo de retraso</label>
                        <input type='text' name='time_retraso' class='input_text required, number' value='<?php echo $Retrasos->time_retraso ?>' />
                    </li>
                    <li>
                        <label class='label_form'>Comentarios</label>
                        <textarea name='descr_retraso' class='input_textarea ' ><?php echo $Retrasos->descr_retraso ?></textarea>
                    </li>
                    <li>
                        <label class='label_form'>Estado</label>
                        <?php echo $this->Utility->createOptions(array("name"=>"status_retraso", "class"=>"input_select required"), $Status_retrasos, $Retrasos->status_retraso) ?>
                    </li>
                    <li class='controls'>
                        <button type='submit'><?php echo $submitText ?></button>
                        <button type='button' onclick='history.back();'>Cancelar</button>
                    </li>            
                </ul>
            </form>
        </div>