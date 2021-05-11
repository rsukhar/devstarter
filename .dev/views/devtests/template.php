<?php
/**
 * @var string $content
 */
?>
<!DOCTYPE html>
<!--[if IE 7 ]>		<html id="nojs" class="ie7" lang="ru-RU"> <![endif]-->
<!--[if IE 8 ]>		<html id="nojs" class="ie8" lang="ru-RU"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html id="nojs" lang="ru-RU"> <!--<![endif]-->

<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> | Devtests</title>
	<?php echo Assets::instance()->output(2, TRUE, FALSE) ?>
</head>
<body>
<?php echo $content;?>
<?php echo Assets::instance()->output(0, FALSE, TRUE) ?>
<?php echo View::factory('profiler/stats'); ?>
</body>
</html>
