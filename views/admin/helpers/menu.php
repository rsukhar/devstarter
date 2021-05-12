<?php
/**
 * @var array $items [{url} => {title}]
 * @var string $active_item_url Если не задан, будет определен автоматически
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
	// Используем самый длинный URL, который подошел, т.к. URL с дефолтным действием может тоже подходить
	// /admin/shops/50/ ... /admin/shops/50/pricelists/
	$active_url_max_length = 0;
	foreach ($items as $item_url => $item_title)
	{
		if (strpos($current_uri, $item_url) === 0 AND strlen($item_url) > $active_url_max_length)
		{
			$active_item_url = $item_url;
			$active_url_max_length = strlen($item_url);
		}
	}
}
?>
<div class="b-menu pos_left">
	<?php foreach ($items as $item_url => $item_title): ?>
		<div class="b-menu-item<?php echo ($item_url == $active_item_url) ? ' active' : ''; ?>">
			<a href="<?php echo $item_url ?>"><span><?php echo $item_title ?></span></a>
		</div>
	<?php endforeach; ?>
</div>