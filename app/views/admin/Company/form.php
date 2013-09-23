
        <div class='form_register simplebox' id='form_Company'>
            <script>
                jQuery(function($)
                {
                    $("#form_Company form").validate();
                });
            </script>
            
       	    <div class="titleh"><h3>Formulario Company</h3></div>
                            
            <div class="body">
                <form method='post' action='<?php echo ROOT_PATH ?>admin/Company/<?php echo $action ?>' enctype=''>
                    <input type='hidden' name='id' value='<?php  echo $Company->id ?>' />
                    <ul>
                    <li>
                        <label class='label_form'>Name</label>
                        <input type='text' name='name' class='input_text required' value='<?php echo $Company->name ?>' />
                    </li>
                    <li>
                        <label class='label_form'>Email</label>
                        <input type='text' name='email' class='input_text required email' value='<?php echo $Company->email ?>' />
                    </li>
                    <li>
                        <label class='label_form'>Address</label>
                        <textarea name='address' class='input_textarea ' ><?php echo $Company->address ?></textarea>
                    </li>
                    <li>
                        <label class='label_form'>Phone</label>
                        <input type='text' name='phone' class='input_text required number' value='<?php echo $Company->phone ?>' />
                    </li>
                    <li>
                        <label class='label_form'>Status</label>
                        <?php echo $this->Utility->createOptions(array("name"=>"enabled", "class"=>"input_select required"), $Enableds, $Company->enabled) ?>
                    </li>
                        <li class='controls'>
                            <button type='submit'><?php echo $submitText ?></button>
                            <button type='button' onclick='history.back();'>Cancelar</button>
                        </li>            
                    </ul>
                </form>
            </div>
        </div>