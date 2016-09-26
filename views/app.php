<?php $content_view = $content_view ? $content_view : '404';?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	<title>List of tasks | Tasker</title>
	<link href="/css/styles.css" rel="stylesheet">
</head>
<body>
	<header top-bar green sticky top left right z3 shadow>
        <div inner>
        	<div logo big>
                <a href="/">
                    Tasker
                </a>
            </div>
            <ul user-block tar>
                <div dropdown on-hover d-ib>
                    <li tabindex="0" tal>
                        <?php if(!Auth::user()) {?>
                        <span username>Guest <span symbol>&#x25BC;</span></span>
                        <ul drop right>
                            <li><a href="/login">Login</a></li>
                            <li><a href="/register">Register</a></li>
                        </ul>
                        <?php } else { ?>
                        <span username><?php echo Auth::user()->name; ?><span symbol>&#x25BC;</span></span>
                        <ul drop right>
                            <li>
                            	<a href="/logout">Logout</a>
                            </li>
                        </ul>
                        <?php } ?>
                    </li>
                </div>
            </ul>
        </div>
    </header>
    <div wrapper>
        <?php include_once(_VIEWS_PATH.$content_view); ?>
    </div>
    <script src="/js/base.js"></script>
</body>
</html>