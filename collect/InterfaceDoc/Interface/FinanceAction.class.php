<?php
namespace Weapp;
/**
 * 财务相关接口
 */

class FinanceAction extends BaseAction
{
    public function _initialize(){
        $this->check_login();
    }

    /**
     * 余额列表
     *
     * api: GET /finance/balance
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     * @param type          筛选类型，如0:全部,1:充值 2:退款 3:余额支付4:提现
     * @param begintime     起始时间  可选
     * @param endtime       结束时间  可选
     */
    protected function get_balance()
    {
        $mid = session('mid');
        $begintime = strtotime(I('get.begintime'));
        $endtime = strtotime(I('get.endtime').' 23:59:59');
        $type = I('get.type','','trim');
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
        if ($begintime > $endtime) {
            $this->response(null, '截止时间不能大于起始时间。', 1007);
        } elseif ($begintime>time()){
            $this->response(null, '\'起始时间不能大于当前时间。', 1007);
        } else {
            if (empty($begintime)) {
                $begintime = 0;
            }
            if (empty($endtime)) {
                $endtime = time();
            }
            $map['mid'] = $mid;
            $map['time'] = array('between',array($begintime,$endtime));
            $map['status'] = 1;

            if (empty($type) || $type=='全部') {
                $type = '全部';
            } else {
                $map['type'] = $type;
            }

            $model = D('MemberBalance');
            import('ORG.Util.Page');
            $total = $model->where($map)->count();

            unset($map['type']);
            $list = $model->where($map)->order('time '.$order)->select();

            // 充值与退款
            $map['time'] = array('elt',$list[0]['time']);
            $map['type'] = array('in',$model->getTypeClass(0));
            $income = $model->where($map)->sum('money');
            $income = $income==NULL ? 0 : $income;

            // 支付与提现
            $map['type'] = array('in',$model->getTypeClass(1));
            $expend = $model->where($map)->sum('money');
            $expend = $expend==NULL ? 0 : $expend;

            // 余额
            $sum = $income-$expend;
            $types = $model->getTypeClass(1);
            foreach ($list as $key => &$value) {
                $value['money'] = (float)$value['money'];
                if (in_array($value['type'], $types)) {
                    $value['money'] /= -1;
                }

                if ($key == 0) {
                    $value['sum'] = $sum;
                }
                if(isset($list[$key+1])){
                    $list[$key+1]['sum'] = $value['sum'] - $value['money'];
                }
            }

            // 根据查询条件清除
            foreach ($list as $key => &$value) {
                if ($type!='全部' && $value['type']!=$type ) {
                    unset($list[$key]);
                }
            }

            // 分页
            $list = array_slice($list, $offset, $limit);
        }

        $data = [
            'types'     => $types,
            'begintime' => $begintime,
            'endtime'   => $endtime,
            'type'      => $type,
            'list'      => $list,
            'offset'    => $offset,
            'limit'     => $limit,
            'order'     => $order,
            'total'     => $total
        ];

        $this->response($data);
    }


    /**
     * 提现列表
     *
     * api: GET /finance/earn
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_earn()
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

        $map['mid'] = session('mid');
        $total = M('MemberBank')->where($map)->count();
        $list = M('MemberBank')->where($map)->order('time '.$order)->limit($offset,$limit)->select();


        //获取省份
        $map['region_type'] = 1;
        $map['region_status'] = 1;
        $option = M('Region')->field('region_name,region_id')->where($map)->select();

        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'order'     => $order,
            'total'     => $total,
            'option'    => $option,
            'list'      => $list
        ];

        $this->response($data);
    }

    /**
     * 提现操作
     *
     * api: POST /finance/cashout
     *
     * @param realname	 	收款人
     * @param bnum   	 	账号
     * @param province      开户行省
     * @param city          开户行城市
     * @param bank          开户行详情
     * @param money         提现金额
     * @param note          备注
     */
    protected function post_cashout()
    {
        $_POST['checkman'] = '';
        $data = $this->exec_action('Member/Finance','cashout');
        if($data['status'] == 1){
            $msg  = '申请成功';
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
     * 获取省
     *
     * api: GET /finance/province
     *
     */
    protected function get_province()
    {
        $map['region_type'] = 1;
        $map['region_status'] = 1;
        $option = M('Region')->field('region_name,region_id')->where($map)->select();

        $this->response($option);
    }

    /**
     * 获取市
     *
     * api: GET /finance/city
     *
     * @param province 省id
     */
    protected function get_city()
    {
        $map['parent_id'] = I('get.province', 0, 'intval');
        $map['region_type'] = 2;
        $map['region_status'] = 1;
        $option = M('Region')->field('region_name,region_id')->where($map)->select();

        $this->response($option);
    }


    /**
     * 发票列表
     *
     * api: GET /finance/invoice
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param type          发票类型  0:全部，1:增值发票，2:普通电子发票，3:不需要发票
     * @param status        发票状态  0:全部，1:未开票，2:待审核，3:待开票，4:已开票，5:作废
     */
    protected function get_invoice()
    {
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $type 	= I('get.type', 0, 'intval');
        $status = I('get.status', 0, 'intval');
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }

        $MemberInvoice = D('MemberInvoice');

        $map = array('mid' => session('mid'));
        $total = (integer)$MemberInvoice->getInvCount($map, $type, $status);
        $list = $MemberInvoice->getInvList($map, $type, $status, $offset, $limit);

        !is_array($list) && $list = [];

        // 可开票金额
        $invoice = $MemberInvoice->getInvRemainAmount(session('mid'), $type);

        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'type'      => $type,
            'status'    => $status,
            'invoice'   => $invoice,
            'list'      => $list,
            'total'     => $total
        ];

        $this->response($data);
    }


    /**
     * 发票详情
     *
     * api: GET /finance/invoice_detail
     *
     * @param id	 发票id
     */
    protected function get_invoice_detail()
    {
        $id = I('get.id', 0, 'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }
        $Invoice = D('Invoice');
        $Order   = D('Order');
        $info    = $Order->where(['id' => $id, 'mid' => session('mid')])->find();
        if(empty($info)){
            $this->response(null, '指定订单不存在', 1007);
        }
        $data 	 = $Invoice->getInvoiceByOid($info['id']);
        if(empty($data)){
            if($info['invoice'] == '普票' && ($info['status'] & C('ORDER_STATUS.YFK'))){
                $invinfo = D('OrderExtends')->getInvInfo($id);
                $info['make_invoice_amount'] = $info['amount'];
                $data =  [
                    'id' 		=> $info['id'],
                    'oids'		=> [$info['id']],
                    'invoice'	=> '普票',
                    'invoicetype' => '普票(电子)',
                    'invinfo'   => [
                        'type'		=> '普通发票(电子)',
                        'title' 	=> $info['invoicetop'],
                        'taxnum'	=> $info['taxnumber'],
                        'content'	=> '电子线路板',
                        'amount'	=> round($info['amount'], 2),
                        'tel'		=> $invinfo['tel'],
                        'address'	=> $invinfo['address'],
                        'bank'		=> $invinfo['bank'],
                        'account'	=> $invinfo['account'],
                    ],
                    'invlist'   => [],			// 申请列表
                    'orderlist' => [$info],		// 订单列表
                    'invoices'	=> null,		// 已开票列表
                ];
            }else{
                $this->response(null, $Invoice->getError(), 1007);
            }
        }

        $this->response($data);
    }


    /**
     * 优惠券列表
     *
     * api: GET /finance/bonus
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param type	 		类型 0:全部，1:未使用，2:已使用，3:已过期
     */
    protected function get_bonus()
    {
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $type   = I('get.type', 0, 'intval');
        if(!in_array($type, [0, 1, 2, 3])){
            $type = 0;
        }
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }

        $MemberBonus = D('MemberBonus');

        $map 	= ['mid' => session('mid'), '_type' => $type];
        $total  = $MemberBonus->getCount($map);
        $list 	= $MemberBonus->getList($map, $offset, $limit);
        $stat   = $MemberBonus->getMemberStats($map['mid']);
        !is_array($list) && $list = [];

        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'type'      => $type,
            'total'     => $total,
            'list'      => $list,
            'stat'      => $stat
        ];

        $this->response($data);
    }


    /**
     * 积分列表
     *
     * api: GET /finance/score
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param type	 		类型 0:全部，1:获取记录，2:扣除记录
     */
    protected function get_score()
    {
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $type   = I('get.type', 1, 'intval');
        if(!in_array($type, [0, 1, 2])){
            $type = 0;
        }
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }

        $pre = C('DB_PREFIX');
        $model = D('MemberScore');

        $map['t1.mid'] = session('mid');
        // 获取即将过期的积分
        list($log_id, $free_score, $change_time) = get_free_surplus_score(session('mid'));
        $expiretime  = strtotime('+6 month', $change_time);
        $nowtime = NOW_TIME;
        $endtime = NOW_TIME + 2592000;
        $overduescore = $expiretime <= $endtime && $expiretime > $nowtime ? $free_score : 0;
        $where = array('mid' => session('mid'));
        $where['id'] = array('gt', intval($log_id));
        $where['type'] = array('IN', array(1, 2));
        $where['_string'] = "UNIX_TIMESTAMP(TIMESTAMPADD(MONTH, 6, FROM_UNIXTIME(`time`))) <= {$endtime} AND UNIX_TIMESTAMP(TIMESTAMPADD(MONTH, 6, FROM_UNIXTIME(`time`))) > {$nowtime}";
        $score = $model->where($where)->sum('score');
        if(!is_null($score)){
            $overduescore += $score;
        }

        // 获取指定类别列表
        $typeclass = $model->getTypeClass();
        if($type == 2){
            $map['t1.type'] = array('IN', $typeclass[1]);
        }elseif($type == 1){
            $map['t1.type'] = array('IN', $typeclass[0]);
        }

        $total = $model->alias('t1')->where($map)->count();
        $list = $model->alias('t1')->join("{$pre}order AS t2 ON t2.id = t1.oid")
            ->where($map)
            ->field('t1.id,t1.oid,t1.type,t1.score,t1.money,t1.time,t2.orderstyle,t2.paytime')
            ->order('t1.id DESC')
            ->select();

        foreach ($list as $key => &$value) {
            $value['stype'] = in_array($value['type'], $typeclass[0]) ? 1 : 2;
            $value['score'] = (float)$value['score'];
            $value['expire'] = strtotime('+6 month', $value['time']);
            if($value['stype'] == 2){
                if($value['type'] == 5){
                    $value['state'] = '已过期';
                }else{
                    $value['state'] = '已使用';
                }
            }else{
                $value['remain'] = intval(($value['expire'] - NOW_TIME) / 86400);
                if($value['remain'] <= 0){
                    $value['remain_day'] = '-';
                    $value['state'] = '已过期';
                }else{
                    $value['remain_day'] = $value['remain'].'天';
                    $value['state'] = $value['remain'] <= 30 ? '即将过期' : '获取';
                }
                if($value['id'] < $log_id  && $value['remain'] > 0){
                    $value['state'] = '已使用';
                }elseif($value['id'] == $log_id && $value['remain'] > 0){
                    if($free_score > 0){
                        $value['state'] = '使用'.($value['score'] - $free_score);
                    }else{
                        $value['state'] = '已使用';
                    }
                }
            }
        }
        // 分页
        $list = array_slice($list, $offset, $limit);

        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'type'      => $type,
            'total'     => $total,
            'overduescore'  => $overduescore,
            'list'      => $list
        ];

        $this->response($data);
    }




}