<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo SITE_NAME ?></title>
	
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/reset.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/root.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/grid.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/typography.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/jquery-plugin-base.css" />
    
    <!--[if IE 7]>	  <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/ie7-style.css" />	<![endif]-->
    
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/toogle.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.tipsy.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.uniform.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/fullcalendar.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.prettyPhoto.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.core.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.slider.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.ui.accordion.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/validate.js"></script>
    <script type="text/javascript" charset="utf-8">
    	$(document).ready(function() 
        {
            $(".form_register h2").wrap("<div class='titleh' />");
            $(".panel_listado h1").wrap("<div class='simplebox'><div class='titleh'/></div>");
        });
    </script>
</head>
<body>

    <div class="wrapper">

	<!-- START HEADER -->
        <div id="header">    
        
        	<!-- logo -->
        	<div class="logo">	<a href="<?php echo ROOT_PATH ?>"><img src="<?php echo IMAGES_PATH ?>logo.png" width="112" height="35" alt="logo"/></a>	</div>
            
            
            <!-- profile box -->
            <div id="profilebox">
            	<a href="#" class="display">
                	<img src="<?php echo IMAGES_PATH ?>fastdevelphp/simple-profile-img.jpg" width="33" height="33" alt="profile"/>	<b>Logged in as</b>	<span>Administrator</span>
                </a>
                
                <div class="profilemenu">
                	<ul>
                    	<li><a href="#">Account Settings</a></li>
                    	<li><a href="#">Logout</a></li>
                    </ul>
                </div>
                
            </div>
            
            <div class="clear"></div>
        </div>
        <!-- END HEADER -->
            
        <!-- START MAIN -->
        <div id="main">
        
            <!-- START SIDEBAR -->
            <div id="sidebar">
            	
                <!-- start sidemenu -->
                <div id="sidemenu">
                	<ul>
                    	<li class="active"><a href="#"><img src="<?php echo IMAGES_PATH ?>fastdevelphp/icons/sidemenu/laptop.png" width="16" height="16" alt="icon"/>Dashboard</a></li>
                        <!-- start submenu with icon -->
                        <li class="subtitle">
                        	<a class="action tips-right" href="#" title="Submenu with icon"><img src="<?php echo IMAGES_PATH ?>fastdevelphp/icons/sidemenu/mail.png" width="16" height="16" alt="icon"/>Submenu<img src="<?php echo IMAGES_PATH ?>fastdevelphp/arrow-down.png" width="7" height="4" alt="arrow" class="arrow" /></a>
                        	<ul class="submenu">
                            <li><a href="#"><img src="<?php echo IMAGES_PATH ?>fastdevelphp/icons/sidemenu/magnify.png" width="16" height="16" alt="icon"/>Search File</a></li>
                            <li><a href="#"><img src="<?php echo IMAGES_PATH ?>fastdevelphp/icons/sidemenu/print.png" width="16" height="16" alt="icon"/>New Files	<span class="ballon">693</span>	</a></li>
                            <li><a href="#"><img src="<?php echo IMAGES_PATH ?>fastdevelphp/icons/sidemenu/trash.png" width="16" height="16" alt="icon"/>Others	<span class="ballon">4</span>	</a></li>
                            </ul>
                        </li>
                        <!-- end submenu with icon -->
                        
                    </ul>
                </div>
                <!-- end sidemenu -->
                
            </div>
            <!-- END SIDEBAR -->
            
            <!-- START PAGE -->
            <div id="page">
                <div class="albox informationbox">
                	<b>Information :</b> Please set your menu items in backend layout. 
                	<a href="#" class="close tips" title="close">close</a>
                </div>
                <?php $this->loadPartialView("fastdevelphp/session_messages") ?>
                <?php echo $contentView ?>
            </div>
            <!-- END PAGE -->
            
            <div class="clear"></div>
            </div>
            <!-- END MAIN -->
            
            <!-- START FOOTER -->
            <div id="footer">
            	<div class="left-column">Powered by <a href="http://skylogix.net">Skylogix</a> &copy; Copyright 2012 - All rights reserved.</div>
                <div class="right-column">Backend template <a href="http://www.fastdevelphp.sysdecom.com" target="_blank">Fastdevelphp</a></div>
            </div>
            <!-- END FOOTER -->
    </div>
</body>
</html>