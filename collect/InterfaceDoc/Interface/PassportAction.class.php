<?php
namespace Weapp;
/**
 * 账号通道
 * 
 */
class PassportAction extends BaseAction{
	
	/**
	 * 验证微信code信息
	 * 
	 * 获取微信 seesion_key
	 * 
	 * api: POST /passport/auth
	 *
	 */
	protected function post_auth(){
		$code = I('post.code', '', 'trim,htmlspecialchars');
		if(empty($code)){
			$this->response(null, 1002);
		}
		$url = C('JF_SSO.SSO_URL').'/Oauth/wxapplogin';
		$_POST['appname'] = 'hqpcb';
		try{
			$text = request($url, $_POST, 'POST', false);
			$res  = json_decode($text, true);
		}catch (Exception $ex){
			$res = array(
				'status' => 'failed',
				'msg' 	 => $ex->getMessage(),
			);
		}
		if($res['status'] == 'success'){
			$this->response($res['data']);
		}else{
			$code = isset($res['error_code']) ? $res['error_code'] : 1005;
			$this->response(null, $res['msg'], $code);
		}
	}
	
	/**
	 * 登陆验证
	 * api: POST /passport/login
	 */
	protected function post_login(){
		$this->check_login();
		$mid = session('mid');
		if(empty($mid)){
			$this->response(null, 1001);
		}
		$resp = array(
			'mid' 		=> session('mid'),
			'email'		=> session('email'),
			'username'	=> session('username'),
		);
		$this->response($resp);
	}
	
}