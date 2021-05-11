<?php
/**
 * @var string $code Error code
 * @var string $name Error name
 * @var string $message Error description
 */
$code = isset($code) ? $code : '';
$name = isset($name) ? $name : '';
$message = isset($message) ? $message : '';
?>
<div class="b-main">
	<section class="b-section">
		<div class="b-section-h">
			<div class="b-content">
				<h1><?php echo intval($code); ?> <?php echo HTML::chars($name) ?></h1>
				<p><?php echo $message; ?></p>
			</div>
		</div>
	</section>
</div>
