<?php
namespace controller;
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/6/22
// | Time  : 9:53
// +----------------------------------------------------------------------


use mini\Controller;
use mini\DB;
use model\User;

class Index extends Controller
{
    public function index()
    {
        $params['hosts'] = '192.168.234.130:9200';
        $elsearch = \Elasticsearch\ClientBuilder::create()->setHosts($params)->build();

        // 查询
        $q = [
            'index' => 'logs',
            'type'  => 'doc',
            'body'  => [
                'query' => [
                    'match' => [
                        'remark'    => '金额'
                    ]
                ],
                'sort'  => [
                    'id'    => [
                        'order'     => 'desc'
                    ]
                ]
            ],
            'from'  => 10,
            'size'  => 10
        ];
        $res = $elsearch->search($q);

        $this->assign('jack','luoxianjie');
        $this->display();
    }

    public function db()
    {
        $user = new User();
        $res = $user->where(['id'=>1])->find();

        dump($res);
    }

}