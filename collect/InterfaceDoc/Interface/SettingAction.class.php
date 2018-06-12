<?php
namespace Weapp;
/**
 * 设置相关接口
 */

class SettingAction extends BaseAction
{
    public function _initialize(){
        $this->check_login();
    }

    /**
     * 地址列表
     *
     * api: GET /setting/address
     *
     * @param offset	 	数据偏移量 （默认 0）
     * @param limit  		数据返回限制条数（默认10，最大100）
     * @param order	 		数据排序（默认 ASC 正序，可选 DESC 倒序）
     */
    protected function get_address()
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

        $mid = session('mid');
        $map = array('mid' => $mid);
        $total = M('MemberAddress')->where($map)->count();
        $list = M('MemberAddress')->where($map)->order('`default` '.$order)->select();
        foreach ($list as $key => &$value) {
            $value['province_name'] = get_region_name($value['province']);
            $value['city_name'] = get_region_name($value['city'], 2);
            $value['district_name'] = get_region_name($value['district'], 3);
        }
        $member = M('Member')->where($map)->field('realname,mobile')->find();

        $data = [
            'offset'    => $offset,
            'limit'     => $limit,
            'order'     => $order,
            'list'      => $list,
            'total'     => $total,
            'member'    => $member
        ];

        $this->response($data);
    }

    /**
     * 添加地址
     *
     * api: POST /setting/address
     *
     * @param orderman     下单人
     * @param ordertel     下单人电话
     * @param recevman     收货人
     * @param recevtel     电话
     * @param province     region_id
     * @param city         region_id
     * @param district     街道地址
     * @param extaddress   详细地址
     * @param shipping_id  快递
     * @param default  设为默认
     */
    protected function post_address()
    {
        $data = $this->exec_action('Account/Setting','insertadd');
        if($data['status'] == 1){
            $msg  = '添加成功';
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
     * 删除地址
     *
     * api: GET /setting/address_delete
     *
     * @param id	地址id
     */
    protected function get_address_delete()
    {
        $id = I('get.id',0,'intval');
        if(empty($id)){
            $this->response(null, '无效请求参数 id', 1002);
        }

        $model = M('MemberAddress');
        $map = [
            'mid'   => session('mid'),
            'id'    => $id
        ];
        $result = $model->where($map)->delete();
        if ($result === FALSE) {
            $this->response(null, '删除失败', 1007);
        } else{
            $this->response(null, '删除成功', 2000);
        }
    }

    /**
     * 获取个人资料
     *
     * api: GET /setting/profile
     *
     */
    protected function get_profile()
    {
        $pre = C('DB_PREFIX');
        $mid = session('mid');
        $where = array('t1.mid' => $mid);
        $info = M('Member')->alias('t1')->join("{$pre}member_profile AS t2 ON t1.mid = t2.mid")
            ->where($where)->find();
        $info['industry'] = explode(',', $info['industry']);
        $map = array(
            'op_type' => array('IN', array(1, 2 ,3)),
        );
        $regs_options = D('RegsOptions')->getOptions($map);

        $data = [
            'info'      => $info,
            'options'   => $regs_options
        ];
        $this->response($data);
    }

    /**
     * 编辑个人资料
     *
     * api: POST /setting/profile
     *
     * @param realname   姓名
     * @param mobile     手机
     * @param telephone  固定电话
     * @param qq         qq
     * @param company_type        企业类型
     * @param job_type            工作性质
     * @param industry[]  array   应用领域(多选，数组格式)
     */
    protected function post_profile()
    {
        $_POST['mid'] = session('mid');
        $data = $this->exec_action('Account/Setting','editinfo');
        if($data['status'] == 1){
            $msg  = '编辑成功';
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