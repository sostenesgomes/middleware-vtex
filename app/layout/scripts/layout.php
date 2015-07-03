<html>
	<head>
	    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	    <title><?php echo Config::get('title')?></title>
        <?php
            echo Template::loadCSS();
        ?>
	</head>
	
	<body>
	
		<?php
			# mostra o conteúdo da página
			echo Template::exibeTemplate();
		?>

        <?php
            echo Template::loadJSFooter();
        ?>
	</body>
</html>