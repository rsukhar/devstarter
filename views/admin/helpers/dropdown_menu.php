<?php
/**
 * @var array $items [{url} => {title}]
 * @var string $active_item_url Если не задан, будет определен автоматически
 * @var bool $preserve_params Сохранять ли по возможности следующие уровни вложенности URL и GET-параметры?
 * @var string $style
 */
if (empty($items))
{
	return;
}
$current_uri = $_SERVER['REQUEST_URI'];
if ( ! isset($active_item_url))
{
	$active_item_url = array_key_first($items);
	foreach ($items as $item_url => $item_title)
	{
		if (strpos($current_uri, $item_url) === 0)
		{
			$active_item_url = $item_url;
			break;
		}
	}
}
if (isset($preserve_params) AND $preserve_params AND strpos($current_uri, $item_url) === 0)
{
	$params = substr($current_uri, strlen($item_url));
	$active_item_url = $current_uri;
	$items_with_params = [];
	foreach ($items as $item_url => $item_title)
	{
		$items_with_params[$item_url.$params] = $item_title;
	}
	$items = $items_with_params;
}
?>
<div class="b-menu-item<?php echo (count($items) > 1) ? ' has_dropdown' : '' ?><?php echo ($active_item_url === $current_uri) ? ' is-active' : ''?><?php echo isset($style) ? ' style_'.$style : '' ?>">
	<a href="<?php echo $active_item_url ?>"><span><?php echo Arr::get($items, $active_item_url) ?></span></a>
	<?php if (count($items) > 1): ?>
		<div class="b-menu-list">
			<?php foreach ($items as $item_url => $item_title): ?>
				<a href="<?php echo $item_url ?>"><span><?php echo $item_title ?></span></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>