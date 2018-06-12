<?php
namespace Weapp;
/**
 * Rest 基础控制器
*
*/
class BaseAction {
	protected $uuid = null;
	// 会员id
	protected $mid  = null;
	// 应用检测状态 0 未检测, 1 已获取单点信息，需要绑定账号，3 已登陆
	protected $_check_status = 0;
	// 请求方法映射
	protected $request_method_map = array();
	// 控制器允许请求方法
	protected $allow_methods = null;
	// 扩展头部信息
	protected $_ext_headers = array();

	/**
	 * 构造函数
	 * @access public
	 */
	public function __construct()
	{
		load('@.Interface.function');
		parent::__construct();
		// 检测类型
		if(false === stripos(C('REST_CONTENT_TYPE_LIST'), $this->_type)) {
			// 请求类型非法 则用默认请求类型
			$this->_type = C('REST_DEFAULT_TYPE');
		}

		// 请求方式检测
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if(!is_null($this->allow_methods)){
			$allow_methods = $this->allow_methods;
		}elseif (!empty($this->request_method_map)){
			$allow_methods = array_keys($this->request_method_map);
		}else{
			$allow_methods = C('REST_METHOD_LIST');
		}
		if(!is_array($allow_methods)){
			$allow_methods = explode(',', $allow_methods);
		}
		if (!in_array($method, $allow_methods)) {
			// 请求方式非法 禁止请求
			$this->response(null, 1005);
		}
	}

	/**
	 * 魔术方法 不存在方法的时候执行
	 * @access public
	 * @param string $method 方法名
	 * @param array $args 	   参数
	 * @return mixed
	 */
	public function __call($method,$args) {
		if( 0 === strcasecmp($method,ACTION_NAME)) {
			return $this->_empty($method,$args);
		}
		return parent::__call($method, $args);
	}

	/**
	 * 不存在的操作的时候执行
	 * @access protected
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	protected function _empty($method, $args) {
		if(method_exists($this, $this->_method.'_'.$method) ){
			$fun  =  $this->_method.'_'.$method;
		}else{
			if(!isset($this->request_method_map[$this->_method])){
				$this->response(null, 1004);
			}
			$path = strtolower(str_replace('//', '/', $_SERVER['PATH_INFO'].'/'));
			$len  = strlen(MODULE_NAME);
			if(substr($path, - ($len + 1), $len) != strtolower(MODULE_NAME)){
				$this->response(null, 1004);
			}
			$method = $this->request_method_map[$this->_method];
			if(method_exists($this, $method)){
				$fun  =  $method;
			}elseif (method_exists($this,$method.'_'.$this->_type)){
				$fun  =  $method.'_'.$this->_type;
			}else{
				$this->response(null, 1004);
			}
		}
		if(method_exists($this, '__check_api_secure')){
			$this->__check_api_secure($fun);
		}
		$this->$fun();
	}

	/**
	 * 重写响应
	 * @param mix 	 $data
	 * @param string $msg
	 * @param number $code
	 * @param string $type
	 */
	protected function response($data=null, $msg='',  $code=null)
	{
		if(is_numeric($msg) && is_null($code)){
			$code = $msg;
			$msg = '';
		}elseif (is_null($code)){
			$code = 2000;
		}
		$info = $this->getResponseInfo($code);
		$response = array();
		$response['code'] = $code;
		$response['info'] = $info[0];
		$response['msg'] = $msg ? $msg : $info[1];
		if(!is_null($data)){
			$response['data'] = $data;
		}
		if(C('REST_LOG_DEBUG') && $code != 2000){
			if(!is_dir(C('REST_LOG_PATH'))){
				mkdir(C('REST_LOG_PATH'));
			}
			$log = strtoupper($this->_method).' '.$_SERVER['REQUEST_URI'].'  '. $msg.' 请求参数: '.var_export($this->args(), true);
			$destination = C('REST_LOG_PATH').date('y_m_d').'.log';
			\Log::write($log, \Log::ERR, '', $destination);
		}
		// 检测类型
		if(false === stripos(C('REST_CONTENT_TYPE_LIST'), $this->_type)) {
			// 请求类型非法 则用默认请求类型
			$this->_type = C('REST_DEFAULT_TYPE');
		}
		parent::response($response, $this->_type, $info[2]);
	}

	/**
	 * 编码数据
	 * @access protected
	 * @param mixed $data 要返回的数据
	 * @param String $type 返回类型 JSON XML
	 * @return void
	 */
	protected function encodeData($data, $type='') {
		if(empty($data))  return '';
		if('json' == $type) {
			// 返回JSON数据格式到客户端 包含状态信息
			$data = json_encode($data);
		}elseif('xml' == $type){
			// 返回xml格式数据
			$data = rest_xml_encode($data, 'response');
		}elseif('php' == $type){
			$data = serialize($data);
		}// 默认直接输出
		// 扩展头部信息
		if(!empty($this->_ext_headers)){
			foreach ($this->_ext_headers as $header){
				if(empty($header[0])){
					continue;
				}
				header($header[0].': '.$header[1]);
			}
		}
		$this->setContentType($type);
		header('Content-Length: ' . strlen($data));
		return $data;
	}

	/**
	 * 获取响应信息
	 * @param number $code
	 */
	protected function getResponseInfo($code){
		$api_statuc_dict = C('API_STATUS_DICT');
		if(isset($api_statuc_dict[$code])){
			return $api_statuc_dict[$code];
		}else{
			return array('UNKNOW', '未知', 200);
		}
	}

	/**
	 * 检测验证登陆信息
	 */
	protected function check_auth()
	{
		if($this->_check_status & 1){
			return true;
		}
		if(isset($_SERVER['HTTP_AUTH'])){
			$uuid = htmlspecialchars(trim($_SERVER['HTTP_AUTH']));
		}else{
			$uuid = I('get.auth', '', 'trim,htmlspecialchars');
		}
		if(empty($uuid)){
			$this->response(null, 1000);
		}
		$WxappToken = M('MemberWxappToken');
		$token = $WxappToken->where(['token' => $uuid])->find();
		if(empty($token)){								// 无相关记录
			$this->response(null, 1001);
		}elseif($token['expire_time'] < NOW_TIME){		// 会话过期
			$WxappToken->where(['token' => $uuid])->delete();
			$this->response(null, 1001);
		}
		// 开启session
		$session_id = str_replace(array('-', ' '), '', $uuid);
		session_id($session_id);
		session_start();
		// 已绑定
		if(!empty($token['mid'])){
			$mid  = session('mid');
			if($mid != $token['mid']){
				$data = D('Member')->chkLogin($token['mid']);
				if(empty($data)){
					$this->response(null, 1003);
				}
				$mid = $data['mid'];
			}
			$this->mid = $mid;
			$status = 3;	// 已绑定登陆
		}else{
			$status = 1;	// 待绑定
		}
		$this->uuid = $uuid;
		$this->_check_status = $this->_check_status | $status;
	}
	
	/**
	 * 检测是否登陆
	 */
	protected function check_login(){
		$this->check_auth();
		if(!($this->_check_status & 3)){
			$this->response(null, 2001);
		}
		return true;
	}

	/**
	 * 检测过滤参数
	 * @param array|string $allow_args		允许的参数名
	 * @param array 		$args			用户提交的所有参数（选填）
	 */
	protected function check_filter_args($allow_args=null, $args=null){
		if(empty($allow_args)){
			return null;
		}
		if(is_null($args)){
			$args = $this->args();
		}
		$result = array();
		if(!is_null($allow_args) && !is_array($allow_args)){
			$allow_args = explode(',', $allow_args);
		}
		if($allow_args){
			foreach ($allow_args as $k){
				$k = trim($k);
				if(isset($args[$k])){
					$result[$k] = $args[$k];
				}
			}
			unset($args);
		}else{
			$result = & $args;
		}
		return $result;
	}

	/**
	 * 检测参数是否存在
	 * @param array|string $chk_args		检测的参数名
	 * @param array 		$args			用户提交的所有参数（选填）
	 * @param int			$type			检测类型，1必须输入，2存在不能为空
	 */
	protected function check_require_args($chk_args=null, $args=null, $type=1){
		if(empty($chk_args)){
			return true;
		}
		if(is_null($args)){
			$args = $this->args();
		}

		if(!is_null($chk_args) && !is_array($chk_args)){
			$chk_args = explode(',', $chk_args);
		}
		if($chk_args){
			foreach ($chk_args as $k){
				$k = trim($k);
				if($type == 1 && (!isset($args[$k]) || empty($args[$k]))){
					$this->response(null, '参数错误, 参数'.$k.'不能为空', 1003);
				}elseif($type == 2 && isset($args[$k]) && empty($args[$k])){
					$this->response(null, '参数错误, 参数'.$k.'不能为空', 1003);
				}
			}
		}
		return true;
	}

	/**
	 * 获取输入参数 支持过滤和默认值
	 * @param string $name			参数名称，不选默认为所有参数
	 * @param string $method		指定方法，不选默认为请求方法
	 * @param string $default		不存在的时候默认值
	 * @param mixed  $filter		参数过滤方法
	 */
	protected function args($name=null, $method=null, $default='', $filter=null){
		static $_inputs = array();
		if(is_null($method)){
			$method = $this->_method;
		}
		if(!isset($_inputs[$method])){
			switch(strtolower($method)) {
				case 'get': $input =& $_GET;break;
				case 'post':$input =& $_POST;break;
				case 'put':
				case 'delete':parse_str(file_get_contents('php://input'), $input);break;
				case 'request': $input =& $_REQUEST;break;
				default:
					return NULL;
			}
			$_inputs[$method] = $input;
		}else{
			$input = $_inputs[$method];
		}
		if(C('VAR_FILTERS')) {
			$_filters    =   explode(',',C('VAR_FILTERS'));
			foreach($_filters as $_filter){
				// 全局参数过滤
				array_walk_recursive($input, $_filter);
			}
		}
		if(empty($name)) { // 获取全部变量
			$data       =   $input;
			$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
			if($filters) {
				$filters    =   explode(',',$filters);
				foreach($filters as $filter){
					$data   =   array_map($filter, $data); // 参数过滤
				}
			}
		}elseif(isset($input[$name])) { // 取值操作
			$data       =	$input[$name];
			$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
			if($filters) {
				$filters    =   explode(',',$filters);
				foreach($filters as $filter){
					if(function_exists($filter)) {
						$data   =   is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
					}else{
						$data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
						if(false === $data) {
							return	 isset($default)?$default:NULL;
						}
					}
				}
			}
		}else{ // 变量默认值
			$data       =	 isset($default)?$default:NULL;
		}
		return $data;
	}
	
	/**
	 * 执行指定 module 中的 Action 操作
	 * @param string $name			Action资源地址
	 * @param string $action		Action操作方法
	 * @param mixed  $vars			方法参数
	 * @return mixed
	 */
	protected function exec_action($name, $action, $vars=null){
		// 页面缓存
		ob_start();
		ob_implicit_flush(0);
		$module =  A($name);
		$method =  new \ReflectionMethod($module, $action);
		try{
			if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
				// 非法操作
				throw new \ReflectionException();
			}
			//执行当前操作
			$method =   new \ReflectionMethod($module, $action);
			if($method->isPublic()) {
				$class  =   new \ReflectionClass($module);
				// 前置操作
				if($class->hasMethod('_before_'.$action)) {
					$before =   $class->getMethod('_before_'.$action);
					if($before->isPublic()) {
						$before->invoke($module);
					}
				}
				// URL参数绑定检测
				if($vars && $method->getNumberOfParameters() > 0){
					$params =  $method->getParameters();
					foreach ($params as $param){
						$name = $param->getName();
						if(isset($vars[$name])) {
							$args[] =  $vars[$name];
						}elseif($param->isDefaultValueAvailable()){
							$args[] = $param->getDefaultValue();
						}else{
							throw_exception(L('_PARAM_ERROR_').':'.$name);
						}
					}
					array_walk_recursive($args,'think_filter');
					$method->invokeArgs($module,$args);
				}else{
					$method->invoke($module);
				}
				// 后置操作
				if($class->hasMethod('_after_'.$action)) {
					$after =   $class->getMethod('_after_'.$action);
					if($after->isPublic()) {
						$after->invoke($module);
					}
				}
			}else{
				// 操作方法不是Public 抛出异常
				throw new \ReflectionException();
			}
		} catch (\ReflectionException $e) {
			// 方法调用发生异常后 引导到__call方法处理
			$method = new \ReflectionMethod($module,'__call');
			$method->invokeArgs($module,array($action,''));
		} catch (AjaxReturnHackException $e){
			return $e->params;
		}
		return ob_get_clean();
	}
	
	
}

/**
 * AjaxReturn Hack 异常
 */
class AjaxReturnHackException extends \Exception{
	public $params = null;
	
	public function __construct($params){
		$this->params = $params;
	}
}