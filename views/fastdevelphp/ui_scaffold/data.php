<?php $key = time().rand(867, 89834); ?>
<tr id="<?php echo $key ?>">
    <td></td>
    <td> 
        <?php echo $this->Utility->createOptions(array("name"=>"data[$key][fieldname]"), $Fields, null, null, false) ?>
    </td>
    <td>
        <input type="text" name="data[<?php echo $key?>][fieldtext]" />
    </td>
    <td>
        <?php echo $this->Utility->createOptions(array("name"=>"data[$key][fieldtype]", "class"=>"type"), $Types, null, null, false) ?>
    </td>
    <td class="values">
        <div class="select hidden">
            <table>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Key</th>
                        <th>Value</th>
                        <!--<th>Default</th>-->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            <input type="text" name="data[<?php echo $key?>][select_key][]" />
                        </td> 
                        <td>
                            <input type="text" name="data[<?php echo $key?>][select_val][]" />
                        </td>
                        <!--<td>
                            <input type="text" name="data[<?php echo $key?>][select_default]" />
                        </td>-->
                    </tr>
                </tbody>
            </table>
            <a title="Add Value" href="#" class="add_select_value">Add Value</a>
        </div>
        <div class="select_object hidden">
            <table>
                <tr>
                    <th>Model</th>
                    <th>Attribute to show</th>
                    <th>Condition SQL</th>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->Utility->createOptions(array("name"=>"data[$key][model]", "class"=>"model_object"), $Tables, null, null, false) ?>
                    </td>
                    <td class="attribute_show">
                        
                    </td>
                    <td>
                        <input name="data[<?php echo $key?>][cond_sql]" />
                    </td>
                </tr>
            </table>
        </div>
        <div class="radio_object hidden">
            <table>
                <tr>
                    <th>Model</th>
                    <th>Attribute to show</th>
                    <th>Condition SQL</th>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->Utility->createOptions(array("name"=>"data[$key][radio_model]", "class"=>"radio_model_object"), $Tables, null, null, false) ?>
                    </td>
                    <td class="attribute_show">
                        
                    </td>
                    <td>
                        <input name="data[<?php echo $key?>][radio_cond_sql]" />
                    </td>
                </tr>
            </table>
        </div>
        <div class="checkbox_object hidden">
            <table>
                <tr>
                    <th>Model</th>
                    <th>Attribute to show</th>
                    <th>Condition SQL</th>
                </tr>
                <tr>
                    <td>
                        <?php echo $this->Utility->createOptions(array("name"=>"data[$key][checkbox_model]", "class"=>"checkbox_model_object"), $Tables, null, null, false) ?>
                    </td>
                    <td class="attribute_show">
                        
                    </td>
                    <td>
                        <input name="data[<?php echo $key?>][checkbox_cond_sql]" />
                    </td>
                </tr>
            </table>
        </div>
        <div class="radio_list hidden">
            <table>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Key</th>
                        <th>Text</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            <input type="text" name="data[<?php echo $key?>][radio_key][]" />
                        </td> 
                        <td>
                            <input type="text" name="data[<?php echo $key?>][radio_val][]" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <a title="Add Value" href="#" class="add_radio_value">Add Option</a>
        </div>
        <div class="checkbox hidden">
            <table>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Key</th>
                        <th>Text</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            <input type="text" name="data[<?php echo $key?>][checkbox_key][]" />
                        </td> 
                        <td>
                            <input type="text" name="data[<?php echo $key?>][checkbox_val][]" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <a title="Add Value" href="#" class="add_checkbox_value">Add Option</a>
        </div>
        <div class="text hidden">
            <input name="data[<?php echo $key?>][text]" value="Here default value" />
        </div>
        <div class="password hidden">
            <input name="data[<?php echo $key?>][password]" value="Here default value" />
        </div>
        <div class="file hidden">
            <!--<input name="data[<?php echo $key?>][file]" />-->
            <span>No default available</span>
        </div>
        <div class="hidden_field hidden">
            <input name="data[<?php echo $key?>][hidden_field]" value="Here default value" />
        </div>
        <div class="textarea hidden">
            <input name="data[<?php echo $key?>][textarea]" value="Here default value" />
        </div>
    </td>
    <td>
        <ul>
            <li><label>Required <input type="checkbox" name="data[<?php echo $key?>][rule][]" value="required" /></label></li>
            <li><label>Number <input type="checkbox" name="data[<?php echo $key?>][rule][]" value="number" /></label></li>
            <li><label>Email <input type="checkbox" name="data[<?php echo $key?>][rule][]" value="email" /></label></li>
            <li><label>Url <input type="checkbox" name="data[<?php echo $key?>][rule][]" value="url" /></label></li>
            <li><label>Date <input type="checkbox" name="data[<?php echo $key?>][rule][]" value="date" /></label></li>
        </ul>
    </td>
    
    <script>
        var key = "<?php echo $key ?>";
        jQuery(function($)
        {
            var tr = $("#"+key);
            //select
            tr.find(".type").change(function()
            {
                var select = $(this);
                var values_td = tr.find("td.values");
                values_td.children("div").hide().filter("."+select.val()).show();
                
            }).trigger("change");
            
            tr.find(".add_select_value, .add_radio_value, .add_checkbox_value").click(function()
            {
                var table = $(this).prev("table");
                var new_tr = table.find("tbody tr:first").clone();
                var del = $("<a href='#' title='Delete value'>Del</a>").click(function(){    if(confirm("Are you sure?")) $(this).closest("tr").remove(); return false;    });
                new_tr.find("input").val("");
                new_tr.children("td:first").append(del);
                table.find("tbody").append(new_tr);
                return false;
            });
            
            //select object
            tr.find(".model_object").change(function()
            {
                var select = $(this);
                $.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumns_and_functions/"+select.val(), { name: "data[<?php echo $key?>][attr_show]" }, function(res)
                {
                    tr.find(".select_object .attribute_show").html(res);
                });
            }).trigger("change");
            
            //radio object
            tr.find(".radio_model_object").change(function()
            {
                var select = $(this);
                $.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumns_and_functions/"+select.val(), { name: "data[<?php echo $key?>][radio_attr_show]" }, function(res)
                {
                    tr.find(".radio_object .attribute_show").html(res);
                });
            }).trigger("change");
            
            //checkbox object
            tr.find(".checkbox_model_object").change(function()
            {
                var select = $(this);
                $.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumns_and_functions/"+select.val(), { name: "data[<?php echo $key?>][checkbox_attr_show]" }, function(res)
                {
                    tr.find(".checkbox_object .attribute_show").html(res);
                });
            }).trigger("change");
        });
    </script>
</tr>