<?php
namespace Weapp;
/**
 * 订单相关接口
 */

class OrderAction extends BaseAction{
	
	public function _initialize(){
		$this->check_login();
	}
	
	/**
	 * 订单列表
	 * 
	 * api: GET /order/list
	 * 
	 * @param offset	 	数据偏移量 （默认 0）
	 * @param limit  		数据返回限制条数（默认10，最大100）
	 * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
	 */
	protected function get_list(){
		$Order  = D('Order');
		$offset = I('get.offset', 0, 'intval');
		$limit 	= I('get.limit', 10, 'intval');
		$order  = I('get.order', 'desc', 'trim,strtolower');
		if($limit <= 0 || $limit > 100){
			$limit = 10;
		}
		if($offset < 0){
			$offset = 0;
		}
		if($order != 'asc'){
			$order = 'desc';
		}
		$joins = [
			['order_profile', 'P'],
		];
		$map = [
			'O.mid' 	=> $this->mid,
			'O.type'	=> ['neq', 3],
			'P.live'	=> 1,
		];
		$field = "O.id,O.amount,O.orderstyle,O.bwidth,O.blength,O.bheight,O.iste,O.status,O.ordertime,O.cid,O.pcbfile,
				 O.subfile,O.units,O.layoutx,O.layouty,O.bcount,O.type,O.units,O.sidedirection,O.sidewidth,O.blayer,O.webpay";
		$count 	= $Order->getOrderCount($map, $joins);
		$list 	= $Order->getOrderList($map, $offset, $limit, $joins, $field, 'O.id '.$order);
		if(!is_array($list)){
			$list = array();
		}
		$site_url = C('SITE_URL');
		foreach ($list as $k => $row){
			$row['pcbfile'] 	= $site_url.$row['pcbfile'];
			$row['file_name'] 	= sbasename($row['pcbfile']);
			$row['order_date'] 	= date('Y-m-d H:i:s', $row['ordertime']);
			$row['order_type'] 	= C('ORDER_TYPE.'.$row['type']);
			$row['status_desc'] = get_order_status($row['status'], true,  $row);
			$row['status_code']	= get_order_status($row['status'], false, $row);
			$row['units_text']  = C('ORDER_UNITS.'.$row['units']);
			unset($row['ordertime'], $row['type'], $row['status'], $row['iste'], $row['cid']);
			$list[$k] = $row;
		}
		
		$resp = array(
			'total'  => intval($count),
			'offset' => $offset,
			'limit'  => $limit,
			'order'  => $order,
			'list'	 => $list,
		);
		$this->response($resp);
	}
	
	/**
	 * 删除订单
	 * 
	 * api: GET /order/delete
	 * 
	 * @param id	 	订单ID，多个订单使用逗号分隔
	 */
	protected function get_delete(){
		$id   = I('get.id', '', 'trim,htmlspecialchars');
		if(empty($id)){
			$this->response(null, '无效请求参数 id', 1002);
		}
		$data = $this->exec_action('Account/Order', 'del');
		if($data['status'] == 1){
			$msg  = '删除成功';
			$code = 2000;
		}else{
			if(isset($data['info'])){
				$msg  = $data['info'];
				$code = 1007;
			}else{
				$msg = '系统错误请稍后再试';
				$code = 1005;
			}
		}
		$this->response(null, $msg, $code);
	}

    /**
     * 订单详情
     *
     * api: GET /order/detail
     *
     * @param id	 	订单ID
     */
	protected function get_detail()
    {
        $id = I('get.id',0,'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }
        $where = ['id'=>$id];
        $pre = C('DB_PREFIX');

        foreach ($where as $k => $v){
            $where["{$pre}order.".$k] = $v;
            unset($where[$k]);
        }

        $info = M('Order')->join("{$pre}order_profile ON {$pre}order.id = {$pre}order_profile.id")
            ->where($where)->find();

        if(empty($info)){
            $this->response(null, '指定订单不存在', 1012);
        }

        if($info['live'] != 1){
            $this->response(null, '指定订单已删除!', 1016);
        }

        // 过滤字段值
        $detail_fields 	= 'bwidth,blength,blayer,lineweight,bcount,pbnum,bheight,color,charcolor,spray,cover,
				sidedirection,sidewidth,test,deltime,copper,pcbfile,sh,fh,testpoint,layoutx,layouty,note,returnid,
				vias,bankong,impendance,bga,blind,zknum,cjarea,cutnum,cjh,cjarea,weight,insidecopper';
        $order_detail   = $this->check_filter_args($detail_fields, $info);

        if(!empty($order_detail['pcbfile'])){
            $order_detail['pcbfile'] = C('REST_SITE_URL').$order_detail['pcbfile'];
        }

        $receive_fields = 'orderman,ordertel,recevman,recevtel,address';
        $receive 		= $this->check_filter_args($receive_fields, $info);

        $invoice		= array(
            'invoice_type'	=> $info['invoice'],
            'invoice_title'	=> $info['invoicetop'],
        );

        $express 		= array(
            'express'		=> $info['express'],
            'shipping_name'	=> $info['wl'],
            'shipping_no'	=> $info['wlid'],
            'shipping_date'	=> $info['shippingtime'] ? date('Y-m-d H:i:s', $info['shippingtime']) : '',
        );

        // 获取订单费用明细
        $map			= array('id' => $info['id']);
        $order_fee		= M('OrderFee')->where($map)->find();

        if(!is_array($order_fee)){
            $order_fee 	= array();
        }else{
            unset($order_fee['id']);
        }

        // 获取订单关联的子订单
        $where = array('parentid' => $info['id']);
        $append_orders = M('OrderProfile')->where($where)->field('id AS order_id')->select();
        if(!is_array($append_orders)){
            $append_orders 	= array();
        }

        // 对内容中存在的图片地址没有域名的加上域名
        $info['result'] = preg_replace('/(<img[^<>]+)src=["\']([^"\':]+)["\']/', '\\1src="'.C('REST_SITE_URL').'\\2"', $info['result']);

        // 重量
        if(empty($order_detail['weight'])){
            import('@.Util.Valuation');
            $area = $order_detail['blength'] * $order_detail['bwidth'] * $order_detail['bcount'];
            $order_detail['weight'] = \Valuation::getWeight($area, $order_detail['bheight']);
        }

        // 面积
        $order_detail['area'] = intval($order_detail['bcount'] * $order_detail['bwidth'] * $order_detail['blength']);

        // 是否为补差价
        $isappend = $info['type'] == 5 ? true : false;
        if(!$isappend && $info['iste'] && $info['cid'] == 0){
            $isappend = true;
        }
        $response = array(
            'order_id' 		=> $info['id'],
            'status_desc'	=> rest_order_status($info['status'], $isappend),
            'status_code'	=> rest_order_status($info['status'], $isappend, false, false),
            'pay_type'		=> $info['paystyle'],
            'pay_amount'	=> floatval($info['amount']),
            'pay_date'		=> $info['paytime'] ? date('Y-m-d H:i:s', $info['paytime']) : '',
            'score' 		=> $info['score'],
            'bonus'    		=> floatval($info['bonus']),
            'discount_amount' => floatval($info['discountfee']),
            'order_type'	=> C('ORDER_TYPE.'.$info['type']),
            'order_style'	=> $info['orderstyle'],
            'order_amount'	=> order_total_amount($info),							// 订单金额，应付金额
            'total_amount'  => $info['webpay'],										// 产品金额
            'order_date'	=> date('Y-m-d H:i:s', $info['ordertime']),
            'order_result'	=> $info['result'],
            'order_auditor' => get_auditor_contact($info['auditor'], ''),
            'order_detail'	=> $order_detail,
            'order_fee'		=> $order_fee,
            'receive'		=> $receive,
            'invoice'		=> $invoice,
            'express'		=> $express,
            'append_orders'	=> $append_orders,
        );
        $this->response($response);
    }


    /**
     * 取消订单
     *
     * api: POST /order/cancel
     *
     * @param id	  integer 订单ID
     * @param reason  string  选择的原因
     * @param content string  其他原因
     */
    protected function post_cancel()
    {
        $id = I('post.id',0,'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $data = $this->exec_action('Account/Order', 'cancel');
        if($data['status'] == 1){
            $msg  = '取消成功';
            $code = 2000;
        }else{
            if(isset($data['info'])){
                $msg  = $data['info'];
                $code = 1007;
            }else{
                $msg = '系统错误请稍后再试';
                $code = 1005;
            }
        }
        $this->response(null, $msg, $code);
    }

    /**
     * 返单列表
     *
     * api: GET /order/fandan
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_fandan()
    {
        $Order = D('Order');
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $order  = I('get.order', 'desc', 'trim,strtolower');
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }
        if($order != 'asc'){
            $order = 'desc';
        }

        $map = array();
        $map['O.mid'] = session('mid');
        $map['O.status'] = get_StatusMap('YFK');
        $map['O.subfile'] = array('neq', '');
        $map['S.id'] = array('exp', 'IS NOT NULL');
        $map['S.mi'] = array('gt', 0);
        $map['P.live'] = 1;
        $map['O.type'] = array('neq', 3);

        $joins = [
            ['order_profile','P'],
            ['order_step','S']
        ];

        $field = "O.id,O.amount,O.orderstyle,O.bwidth,O.blength,O.bheight,O.iste,O.status,O.ordertime,O.cid,O.pcbfile,
				 O.subfile,O.units,O.layoutx,O.layouty,O.bcount,O.type,O.units,O.sidedirection,O.sidewidth,O.blayer,O.webpay,S.mi";

        $count 	= $Order->getOrderCount($map, $joins);
        $list 	= $Order->getOrderList($map, $offset, $limit, $joins, $field, 'O.id '.$order);

        if(!is_array($list)){
            $list = array();
        }
        $site_url = C('SITE_URL');
        foreach ($list as $k => $row){
            $row['pcbfile'] 	= $site_url.$row['pcbfile'];
            $row['file_name'] 	= sbasename($row['pcbfile']);
            $row['order_date'] 	= date('Y-m-d H:i:s', $row['ordertime']);
            $row['order_type'] 	= C('ORDER_TYPE.'.$row['type']);
            $row['status_desc'] = get_order_status($row['status'], true,  $row);
            $row['status_code']	= get_order_status($row['status'], false, $row);
            $row['units_text']  = C('ORDER_UNITS.'.$row['units']);
            unset($row['ordertime'], $row['type'], $row['status'], $row['iste'], $row['cid']);
            $list[$k] = $row;
        }

        $resp = array(
            'total'  => intval($count),
            'offset' => $offset,
            'limit'  => $limit,
            'order'  => $order,
            'list'	 => $list,
        );
        $this->response($resp);
    }

    /**
     * 返单详情
     *
     * api: GET /order/fandan_detail
     *
     * @param id	integer	订单id
     */
    protected function get_fandan_detail()
    {
        $id = I('get.id',0,'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }
        $map = [
            't1.id'     => $id,
            't1.mid'    => session('mid')
        ];
        $order = M('Order')
            ->alias('t1')
            ->join(C('DB_PREFIX').'order_profile AS t2 ON t1.id = t2.id')
            ->where($map)
            ->find();
        if(empty($order)){
            $this->response(null, '订单不存在或者该订单不能返单', 1007);
        }

        //是否可以变更交货形式
        $allow_modifymakeup = false;
        if($order['units'] == 1 && $order['layoutx'] == 1 && $order['layouty'] == 1 && $order['pbnum'] == 1){
            $allow_modifymakeup = true;
        }
        if( $order['units'] !=1 && $order['layoutx'] == 1 && $order['layouty'] == 1 ){
            $order['units'] = 3;
        }

        $order['sidedirection_cn'] = '无';
        if( $order['sidedirection'] == 'X' ){
            $order['sidedirection_cn'] = '上下';
        }
        if( $order['sidedirection'] == 'Y' ){
            $order['sidedirection_cn'] = '左右';
        }
        if( $order['sidedirection'] == 'XY' ){
            $order['sidedirection_cn'] = '四边';
        }
        $data = [
            'allow_modifymakeup'    => $allow_modifymakeup,
            'order'                 => $order
        ];
        $this->response($data);
    }

    /**
     * 退款列表
     *
     * api: GET /order/refund
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_refund()
    {
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $order  = I('get.order', 'desc', 'trim,strtolower');
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }
        if($order != 'asc'){
            $order = 'desc';
        }

        $map = ['mid'=>session('mid')];
        $total = M('OrderRefund')->where($map)->count();

        $list = M('OrderRefund')
            ->where($map)
            ->limit($offset,$limit)
            ->order('time '.$order)
            ->select();

        $data = array(
            'total'  => intval($total),
            'offset' => $offset,
            'limit'  => $limit,
            'order'  => $order,
            'list'	 => $list,
        );

        $this->response($data);
    }


    /**
     * 订单收货地址
     *
     * api: GET /order/address
     *
     * @param id	 	订单id
     */
    protected function get_address()
    {
        $id = I('get.id', 0, 'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }
        $map = [
            'id'    => $id,
            'mid'   => session('mid')
        ];
        $address = M('Order')->where($map)->field('recevman,recevtel,address,orderman,ordertel')
            ->find();

        $this->response($address);
    }


    /**
     * 修改订单收货地址
     *
     * api: POST /order/address
     *
     * @param id	 	    订单id
     * @param orderman	 	下单联系人
     * @param ordertel	 	下单联系人手机
     * @param recevman	 	收货人
     * @param recevtel	 	收货人手机
     * @param address	 	收货人地址
     */
    protected function post_address()
    {
        $id = I('post.id', 0, 'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $data = $this->exec_action('Account/Order','updateadd');
        if($data['status'] == 1){
            $msg  = '修改成功';
            $code = 2000;
        }else{
            if(isset($data['info'])){
                $msg  = $data['info'];
                $code = 1007;
            }else{
                $msg = '系统错误请稍后再试';
                $code = 1005;
            }
        }
        $this->response(null, $msg, $code);

    }


	
}