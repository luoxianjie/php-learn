<?php
namespace Weapp;
/**
 * 默认控制器
 *
 */
class IndexAction extends BaseAction{
	
	/**
	 * 首页幻灯片
	 *
	 * api: GET /index/banner
	 */
	protected function get_banner(){
		$data = array(
			array(
				'img' => 'https://t.elecfans.com/Public/Upload/ComImg/20170104/586c5880aa0a9.png!375!150',
				'url' => '',
			),	
			array(
				'img' => 'https://t.elecfans.com/Public/Upload/ComImg/20161028/581326f2b59a5.jpg!375!150',
				'url' => '',
			),
			array(
				'img' => 'https://t.elecfans.com/Public/Upload/ComImg/20161019/5807444e273df.jpg!375!150',
				'url' => '',
			),
			array(
				'img' => 'https://t.elecfans.com/Public/Upload/ComImg/20161010/57fb6c4659d13.jpg!375!150',
				'url' => '',
			),
			array(
				'img' => 'https://t.elecfans.com/Public/Upload/ComImg/20160913/57d7baf8685f6.jpg!375!150',
				'url' => '',
			),
		);
		$this->response($data);
	}
}