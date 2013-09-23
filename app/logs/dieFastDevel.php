<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Owen Peredo" />

	<title>FastDevelPHP</title>
    <style>
        .msg        { background: #FF9C3E; color: #fff; margin: auto auto; width: 550px; border: 1px solid #000; padding: 25px; }
        body        { background: #F5F3F3;  }
        h1          { text-align: center;  }
        .log        { margin-top: 20px; border: 1px solid #333; color: #333; font-style: italic;  }
    </style>
</head>

<body>
    <h1>FastDevelPHP - Error</h1>
    <div class="msg">
        <?php echo $msg ?>
        <div class="log">
            <?php echo FD_getDebug(); ?>
        </div>
    </div>

</body>
</html>