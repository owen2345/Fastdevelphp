    <script>
        jQuery(function($)
        {
            $("#loginform form").each(function(){   $(this).validate();   });
            $("#loginform .form_login .forgot").click(function()
            {
                $(this).closest("form").hide();
                $("#loginform .form_forgot").show();
                return false;
            });
            
            $("#loginform .form_forgot").hide().find(".cancel").click(function()
            {
                $("#loginform .form_login").show();
                $(this).closest("form").hide();
                return false;
            });
        });
    </script>
    <div class="loginform" id="loginform">
    	<div class="title"> Login form</div>
        <div class="body">
            <div class="albox informationbox">
            	<b>Information :</b> Please adapt to your needs. 
            	<a href="#" class="close tips" title="close">close</a>
            </div>
            <?php $this->loadPartialView("fastdevelphp/session_messages") ?>
            <form class="form_login" method="post" action="<?php echo ROOT_PATH ?>admin/check_login">
                <p>
                    <label class="log-lab">Email</label>
                    <input name="email" type="text" class="email required login-input-user" value=""/>
                </p>
                <p>
                    <label class="log-lab">Password</label>
                    <input name="password" type="password" class="required login-input-pass" value=""/>
                </p>
                <p>
                    <button type="submit" name="button" class="button">Login</button>
                    <button type="button" name="button" onclick="history.back()" class="button">Cancel</button>
                </p>
                <p>
                    <a class="forgot" href="#" title="Forgot password">Forgot password</a>
                </p>
            </form>
            
            <form class="form_forgot" method="post" action="<?php echo ROOT_PATH ?>admin/forgot">
                <p>
                    <label class="log-lab">Email</label>
                    <input name="email" type="text" class="email required login-input-user" value=""/>
                </p>
                <p>
                    <button type="submit" class="button">Send</button>
                    <button type="button" class="button cancel">Cancel</button>
                </p>
            </form>
        </div>
    </div>