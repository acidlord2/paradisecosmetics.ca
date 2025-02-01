<?php
class DeliveryPayBlock
{

	public function __construct()
	{
		add_action('acf/init', array($this, '_register'));
	}

	public function _register()
	{
		acf_register_block_type(
			array(
				'name'              => 'delivery-pay-block',
				'title'             => 'Block: pay and delivery',
				'description'       => 'Описание',
				// 'post_types'        => array(),
				'render_callback'   => array($this, '_render'),
				'category'          => 'theme-blocks',
				'icon'              => 'format-aside',
				'mode'              => 'edit',
				'align'             => 'wide',
				'supports'          => array(
					'align' => array('wide', 'full'),
					'mode'  => true,
				),
				'enqueue_assets'    => array($this, '_enqueue_assets'),
			)
		);
	}

	public function _enqueue_assets()
	{
		$tpath = wp_normalize_path(get_template_directory());
		$cpath = wp_normalize_path(__DIR__);
		$path = explode($tpath, $cpath)[1];
		wp_enqueue_style('theme/' . 'delivery-pay-block', GSE()::theme_uri() . $path . '/block.css', array(), 2);
		wp_enqueue_script('theme/' . 'delivery-pay-block', GSE()::theme_uri() . $path . '/block.js', array(), 2);
		return;
	}

	public function _render($block, $content = '', $is_preview = false)
	{
		$id = $block['id'];
		include 'render.php';
	}

}

return new DeliveryPayBlock();
