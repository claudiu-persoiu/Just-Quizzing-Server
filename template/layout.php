<!DOCTYPE html>
<html manifest="cache.appcache">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="HandheldFriendly" content="true"/>
    <title><?php echo TITLE; ?></title>
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="css/frontend.css">
</head>
<body>
<div id="content">
    <div id="header">
        <div id="header-container">
            <h1><img src="images/header-image.png"><?php echo TITLE; ?></h1>
        </div>
    </div>
    <div id="body">
        <?php include($contentFile); ?>
    </div>
    <?php include('footer.php'); ?>
</div>
</body>
</html>