<?php
//搜索
// 1.订单号搜索
$data['order_number'] = (int)input('get.order_number');
$where = array();
$data['order_number'] && $where = 'order_number like "%'.$data['order_number'].'%"';
// $where = [];
// 2.其他搜索


//设置当前页显示条数
$data['pageSize'] = 1;
//设置当前页页码
$data['page'] = max(1,(int)input('get.page'));
$user_info = Db::table('user')->where($where)->paginate($data['pageSize']);
//获取用户列表
$user_res = $user_info->items();
foreach ($user_res as $key => $value) {
    $user_res[$key]['trip'] = Db::table('user_trip_list')->field('trip_time,trip')->where('uid',$value['id'])->select();
}

//获取页面信息
$data['total_num'] = $user_info->total();

//获取创建人
$admin_list = Db::table('admins')->field('id,username')->select();
//获取行程
$data['trips_list'] =  Db::table('trips')->field('trip_id,trip_name,trip_info')->select();

foreach ($user_res as $key => $value) {
    //修改创建人
    foreach ($admin_list as  $v_a) {
        if($v_a['id'] == $value['aid']){
            $user_res[$key]['aid'] = $v_a['username'];
        }
    }

    //修改状态
    switch ($value['status']) {
        case 0: $user_res[$key]['status']='已提交';break;
        case 1: $user_res[$key]['status']='草稿箱';break;
    }

    //修改器：行程
    foreach ($user_res[$key]['trip'] as $k_t => $v_t) {
        //
        foreach ($data['trips_list'] as $k => $v) {
            if($v_t['trip'] == $v['trip_id']){
                $user_res[$key]['trip'][$k_t]['trip'] = $v['trip_name'];
            }
        }
    }
}
$data['user'] = $user_res;


<h3>订单列表</h3><hr>
		<table class="layui-table">
		  <thead>
		    <tr>
		      <th>订单号</th>
		      <th>联系人</th>
		      <th>联系电话</th>
		      <th>创建日期</th>
		      <th>创建人</th>
		      <th>行程日期/线路</th>
		      <th>订单状态</th>
		      <th>操作</th>
		    </tr>
		  </thead>
		  <tbody>
		  	{volist name="user" id="vo"}
		    <tr>
		    	<td>{$vo.order_number}</td>
		    	<td>{$vo.userName}</td>
		    	<td>{$vo.iphone}</td>
		    	<td>{$vo.add_time}</td>
		    	<td>{$vo.aid}</td>
		    	<td>
			    	{volist name="$vo.trip" id="v"}
			    		<p>{$v.trip_time}<i>{$v.trip}</i></p>
			    	{/volist}
		    	</td>
		    	<td>{$vo.status}</td>
		    	<td>
		    		<button class="layui-btn layui-btn-xs" onclick="details({$vo.id})">查看</button>
		    		<button class="layui-btn layui-btn-xs layui-btn-warm" onclick="change({$vo.id})">变更</button>
		    		<button class="layui-btn layui-btn-xs layui-btn-danger" onclick="del({$vo.id})">删除</button>
		    	</td>
		    </tr>
		    {/volist}
		  </tbody>
		</table>
		<div id="paging"></div>

// 分页
laypage.render({
  			elem:'paging'
  			//数据总数
  			,count:{$total_num}
  			//当前页面大小
  			,limit:{$pageSize}
  			//当前页
  			,curr:{$page}
  			,jump:function(obj,frist){
    if(!frist){
        search(obj.curr);
    }
}
  		});

<input type="text" name="order_num" class="layui-input" value="{$order_number==0?'':$order_number}">