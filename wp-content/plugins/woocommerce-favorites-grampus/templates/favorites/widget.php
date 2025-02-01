<?php
$favorites_active = WCFAVORITES()->count_items();
if($favorites_active)
{
?>
<a href="<?php echo esc_url( wc_get_favorites_url() ); ?>" class="widget-block <?php if($favorites_active > 0){ echo "active"; }?>" title="Избранное">
	<div class="icon">
		<?=file_get_contents(WCFAVORITES()->get_plugin_path().'/assets/svg/favorites-widget.svg')?>
	</div>
	<div class="text">Избранное</div>
</a>
<?php
}
else
{
?>
<div class="widget-block <?php if($favorites_active > 0){ echo "active"; }?>" title="Избранное">
	<div class="icon">
		<?=file_get_contents(WCFAVORITES()->get_plugin_path().'/assets/svg/favorites-widget.svg')?>
	</div>
	<div class="text">Избранное</div>
</div>
<?php
}