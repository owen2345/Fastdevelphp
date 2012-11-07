<script>
    jQuery(function($)
    {
        $("#form_scaffold select[name='model']").change(function()
        {
            var select = $(this);
            /*$.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumns/"+select.val(), function(res)
            {
                $("#list_datas").html(res);
            });*/
            
            $.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumnsData/"+select.val()+"/1", function(res)
            {
                $("#params").children("tbody").html(res);
            });
        }).trigger("change");
        
        $("#add_param").click(function()
        {
            var select = $("#form_scaffold select[name='model']");
            $.get(ROOT_PATH+"fastdevelphp/ui_scaffold/getColumnsData/"+select.val()+"/1", function(res)
            {
                var res = $(res);
                var del = $("<a href='#' title='Delete param'>Del</a>").click(function(){    if(confirm("Are you sure?")) $(this).closest("tr").remove(); return false;    });
                res.children("td:first").append(del);
                $("#params").append(res);
            });
            return false;
        });
    });
</script>
<form id="form_scaffold" method="post" action="<?php echo ROOT_PATH ?>fastdevelphp/ui_scaffold/generate">    
    <ul class="main_list">
        <li class="st-form-line">
            <label class="st-labeltext">Model: </label>
            <?php echo  $this->Utility->createOptions(array("name"=>"model"), $Tables, null, null, false) ?>
        </li>
        <li class="st-form-line">
            <label class="st-labeltext">Module name: </label>
            <input type="text" name="module_name" />
        </li>
        <li class="st-form-line">
            <label class="st-labeltext">Type scaffold: </label>
            <?php echo $this->Utility->createOptions(array("name"=>"type_scaffold"), array("1"=>"Simple", "0"=>"Avanced"), 1) ?>
        </li>
        <!--<li class="">
            <label><strong>List Datas</strong>: </label>
            <div id="list_datas">
                
            </div>
        </li>-->
        <li>
            <div class="simplebox">
                <div class="titleh" style="z-index: 710;">
                	<h3>Params: </h3>
                </div>
            </div>
            <table id="params">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Field</th>
                        <th>Field Text</th>                        
                        <th>Type</th>
                        <th>Default/Values</th>
                        <th>Rules</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            <br />
            <a href="#" id="add_param" title="Add param">Add Param</a> 
        </li>
        <li class="controls">
            <br />
            <button type="submit">Submit</button>
        </li>
    </ul>
</form>