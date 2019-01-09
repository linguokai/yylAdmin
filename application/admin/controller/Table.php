<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Session;
use think\Db;

class Table extends Common
{
    public function table()
    {
        if ($this->request->isAJAX()) {
            // 分页
            $page = $this->request->param('page/s');
            $limit = $this->request->param('limit/s');

            // 条件
            $type = $this->request->param('type/s');
            $is_frame = $this->request->param('is_frame/s');
            $field = $this->request->param('field/s');
            $field_val = $this->request->param('field_val/s');
            $time_type = $this->request->param('time_type/s');//日期类型
            $time_date = $this->request->param('time_date/s');//日期选择
            if ($type) {
                $where[] = ['type', '=', $type];
            }
            if ($is_frame) {
                $where[] = ['is_frame', '=', $is_frame];
            }
            if ($field_val) {
                $where[] = [$field, 'like' ,'%'.$field_val.'%'];
            }
            if ($time_date) {
                $time_date = explode(' - ', $time_date);
                $start_time = strtotime($time_date[0].' 00:00:00');
                $end_time   = strtotime($time_date[1].' 23:59:59');
                $where[] = [$time_type, ['>=', $start_time], ['<=', $end_time], 'and'];
            }
            $where[] = ['is_dele', '=', 1];//1正常-1删除

            // 排序
            $order_field = $this->request->param('order_field/s'); // 排序字段
            $order_type = $this->request->param('order_type/s'); // 排序方式
            if ($order_type) {
                $order = [$order_field=>$order_type];
            } else {
                $order = ['sort'=>'desc','id'=>'desc'];
            }

            $table = Db::name('news');
            // 数据分页
            $data = $table
                ->where($where)
                ->field('id,title,author,keywords,type,look,sort,is_dele,is_frame,create_time,update_time')
                ->page($page,$limit)
                ->order($order)
                ->select(); 
            $count = $table->where($where)->count();// 查询总记录数

            if ($data) {
                $type_arr = array('图文','链接','图集');//新闻类型
                foreach ($data as $k => $v) {
                    $data[$k]['type'] = $type_arr[$v['type']-1];
                    $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                    $data[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                }

                $res['code'] = 0;
                $res['count'] = $count;
                $res['data'] = $data;
            } else {
                $res['code'] = 0;
            }

            return json($res);
        }
    	
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {
        return $this->fetch();
    }

    public function dele()
    {
        return $this->fetch();
    }
}