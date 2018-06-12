<?php
namespace Weapp;
/**
 * 客户服务相关接口
 */

class ServiceAction extends BaseAction
{
    public function _initialize(){
        $this->check_login();
    }

    /**
     * 投诉列表
     *
     * api: GET /service/complaint
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_complaint()
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
        $map['mid']= session('mid');
        $map['status'] = 1;
        $model = M('MemberComplaints');

        import('ORG.Util.Page');
        $total = $model->where($map)->count();


        $list = $model->where($map)->order('time '.$order)->limit($offset,$limit)->select();
        foreach ($list as &$value) {
            $value['subject'] = RemoveXSS($value['subject']);
            $value['reply'] = RemoveXSS($value['reply']);
        }
        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'order'     => $order,
            'list'      => $list,
            'total'     => $total
        ];
        $this->response($data);
    }

    /**
     * 提交投诉
     *
     * api: POST /service/complaint
     *
     * @param oid      订单号
     * @param subject  投诉主题
     * @param content  投诉内容
     */
    protected function post_complaint()
    {

        $oid = I('post.oid',0,'intval');
        $subject= I('post.subject','','trim,htmlspecialchars');
        $content = I('post.content','','trim,htmlspecialchars');

        if(empty($oid)||empty($subject)||empty($content)){
            $this->response(null, '无效请求参数', 1002);
        }

        $data = $this->exec_action('Account/Service', 'newcomplaints');
        if($data['status'] == 1){
            $msg  = '提交成功';
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
     * 投诉详情
     *
     * api: GET /service/complaint_detail
     *
     * @param id      投诉id主键
     */
    protected function get_complaint_detail()
    {
        $id = I('get.id', 0, 'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $map['mid'] = session('mid');
        $map['complaint_id'] = $id;

        $complaint = M('MemberComplaints')->where($map)->find();

        $this->response($complaint);
    }

    /**
     * 删除投诉
     *
     * api: GET /service/complaint_delete
     *
     * @param id      投诉id主键
     */
    protected function get_complaint_delete()
    {
        $id = I('get.id', 0, 'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $model = M('MemberComplaints');
        $map = ['mid'   => session('mid'),'complaint_id'  => $id];
        $complain = $model->where($map)->find();

        if (!$complain) {
            $this->response(null, '投诉信息不存在', 1002);
        } elseif($complain['reply'] != NULL) {
            $model->where($map)->setField('status',0);
            $this->response(null, '删除成功', 2000);
        } else {
            $model->where($map)->delete();
            $this->response(null, '删除成功', 2000);
        }

    }


    /**
     * 生产稿列表
     *
     * api: GET /service/pcbfile
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_pcbfile()
    {
        $offset = I('get.offset', 0, 'intval');
        $limit 	= I('get.limit', 10, 'intval');
        $order  = I('get.order', 'desc', 'trim,strtolower');
        $state = I('request.status', 0, 'intval');
        $keyword = I('get.keyword', '');
        if($limit <= 0 || $limit > 100){
            $limit = 10;
        }
        if($offset < 0){
            $offset = 0;
        }
        if($order != 'asc'){
            $order = 'desc';
        }

        $pre = C('DB_PREFIX');
        $Order = M('Order');
        $apply_subfile_endtime = C('APPLY_SUBFILE_ENDTIME');
        if(!is_numeric($apply_subfile_endtime)){
            $apply_subfile_endtime = strtotime($apply_subfile_endtime);
        }

        $map = array();
        $map['t1.type'] = array('IN', array(1, 4));
        $map['t1.mid'] = session('mid');
        $map['t1.status'] = get_StatusMap('YFK,AQR');
        $map['t2.live'] = 1;
        $map['t1.ordertime'] = array('gt', $apply_subfile_endtime);

        if($state == 1){ 			// 未申请
            $map['t3.id'] = array('exp', 'IS NULL');
        }elseif ($state == 2){		// 已完成
            $map['t3.id'] = array('exp', 'IS NOT NULL');
            $map['t3.stenfile'] = array('neq', '');
        }elseif ($state == 3){		// 待制作
            $map['t3.id'] = array('exp', 'IS NOT NULL');
            $map['t3.stenfile'] = array('eq', '');
        }

        if (!empty($keyword) && !is_numeric($keyword)) {
            $map['t1.pcbfile'] = array('like','%'.$keyword.'%');
        }elseif (!empty($keyword) && is_numeric($keyword)) {
            $map['_complex'] = array(
                array("t1.id" => $keyword),
                array("t1.pcbfile" => array('like','%'.$keyword.'%')),
                '_logic' => 'OR',
            );
        }

        $total = $Order->alias('t1')->where($map)->join("{$pre}order_profile AS t2 ON t1.id = t2.id")
            ->join("{$pre}order_subfile_apply AS t3 ON t1.id = t3.id")
            ->where($map)
            ->count('t1.id');

        $list = $Order->alias('t1')->where($map)->join("{$pre}order_profile AS t2 ON t1.id = t2.id")
            ->join("{$pre}order_subfile_apply AS t3 ON t1.id = t3.id")
            ->where($map)
            ->field('t1.id,t1.ordertime,t1.pcbfile,t3.applytime,t3.uploadtime,t1.returnid,t3.stenfile as subfile')
            ->order('t1.ordertime '.$order)
            ->limit($offset,$limit)
            ->select();
        foreach ($list as &$row){
            if(!empty($row['subfile'])){
                $row['state'] = 2;
            }elseif(empty($row['applytime'])){
                $row['state'] = 1;
            }else{
                $row['state'] = 3;
            }
        }
        $state_dict = array(
            1 => '未申请',
            2 => '已完成',
            3 => '待制作',
        );

        $data = [
            'states'    => $state_dict,
            'status'    => $state,
            'offset'    => $offset,
            'limit'     => $limit,
            'order'     => $order,
            'list'      => $list,
            'total'     => $total,
            'keyword'   => $keyword
        ];

        $this->response($data);
    }

    /**
     * 申请生产稿
     *
     * api: POST /service/pcbfile_apply
     *
     * @param id      生产稿id主键
     */
    protected function post_pcbfile_apply()
    {
        $id = I('post.id',0,'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $data = $this->exec_action('Member/Order', 'applysubfile');
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

}
