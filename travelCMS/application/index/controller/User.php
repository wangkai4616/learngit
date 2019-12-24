<?php

namespace app\index\controller;

use app\admins\controller\Base;
use think\exception\DbException;
use think\facade\View;
use think\Db;
use think\Validate;

class User extends Base
{
	public function user_create(){
		$this->assign('title','创建订单');
		$data['trips_list'] =  Db::table('trips')->select();
		$data['user_traffic'] = Db::table('user_traffic')->select();
		return view('',$data);
	}

	public function user_storage(){
		$data = [];
		$data['aid'] = (int)input('post.aid');
		$data['userName'] = trim(input('post.userName'));
		$data['iphone'] = trim(input('post.iphone'));
		$data['meal'] = (int)input('post.meal');
		$data['hotel'] = (int)input('post.hotel');
		$data['arrive_traffic'] = (int)input('post.arrive_traffic');
		$data['return_traffic'] = (int)input('post.return_traffic');

		//验证规则
		$rule = [
			'aid|创建者'	         => 'require|integer',
			'userName|联系人姓名' => 'require|max:25|chsAlpha',
			'iphone|联系方式'     => 'require|mobile',
			'meal|是否含餐'	     => 'integer',
			'hotel|酒店标准'	     => 'integer',
			'arrive_traffic|抵达交通'   => 'integer',
			'return_traffic|返回交通'   => 'integer',
		];

		$repeat_select = "SELECT `order_number` FROM `user` WHERE `iphone`=". $data['iphone'];
		$result = Db::query($repeat_select);
		if(FALSE != $result){
			exit(json_encode(array('code'=>15,'msg'=>'当前订单所填联系电话已有订单，请检查')));
		}

		//验证非数组型数据
		$validate = new Validate;
		$validate->rule($rule);
		if(!$validate->check($data)){
			$msg = $validate->getError();
			exit(json_encode(array('code'=>1,'msg'=>$msg)));
		};

//1.行程信息验证
		//接送信息
		$data['arrive_time'] = trim(input('post.arrive_time'))?trim(input('post.arrive_time')):'0';
		$data['return_time'] = trim(input('post.return_time'))?trim(input('post.return_time')):'0';
		$data['arrive_info'] = trim(input('post.arrive_info'))?trim(input('post.arrive_info')):'0';
		$data['return_info'] = trim(input('post.return_info'))?trim(input('post.return_info')):'0';


		//获取行程时间
		$trip_time = input('post.trip_time');
		// 时间格式校验 正则
		$patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])(\:(0?[0-9]|[1-5][0-9]))?)?$/";

		//行程时间输入验证
		foreach ($trip_time as $key => $value) {
			if($value == ""){
				exit(json_encode(array('code'=>3,'msg'=>'行程时间不能为空请填写')));
			}
			//时间格式校验
			if (!preg_match($patten, $value)) {
				exit(json_encode(array('code'=>4,'msg'=>'日期格式不正确,请检查')));
			}
		}

		$trip_start_time =min($trip_time);
		//行程格式校验
		$trip_list = input('post.trip');
		foreach ($trip_list as $value) {
			if($value == ""){
				exit(json_encode(array('code'=>5,'msg'=>'行程不能为空请选择')));
			}
			if(!preg_match("/^[1-9][0-9]*$/" ,$value)){
				exit(json_encode(array('code'=>6,'msg'=>'行程格式不正确')));
			}
		}

		//对应校验
		if(count($trip_time) != count($trip_list)){
			exit(json_encode(array('code'=>20,'msg'=>'请正确使用该功能，如若发现违规操作，账号永久封禁！')));
		}

		//删除北二当天在西安的住宿
		$northTwo_date = '';
		foreach ($trip_time as $key => $value) {
			if((int)$trip_list[$key] == 4){
				//获取北二时间
				$northTwo_date = $value;
			}
		}
		//当选择非不含住或已定时必须填写房型
		$hotel_time = input('post.hotel_time');
		$hotel_type['stand_house'] = (int)input('post.stand_house');
		$hotel_type['big_house'] = (int)input('post.big_house');
		$hotel_type['three_house'] = (int)input('post.three_house');
		$hotel_type['parent_house'] = (int)input('post.parent_house');

		if($data['hotel'] != 0){
			if($data['hotel'] !=4){
				//住宿时间
				if(!$hotel_time){
					exit(json_encode(array('code'=>2,'msg'=>"当选择不是\"不含住\"或者\"已定时\"必须填写住宿时间")));
				}

				foreach ($hotel_time as $key => $value) {
					if (!preg_match($patten, $value)) {
						exit(json_encode(array('code'=>4,'msg'=>'住宿日期格式不正确,请检查')));
					}

					//当行程中出现北二时，删除当天住宿日期
					if($value == $northTwo_date){
						 unset($hotel_time[$key]); 
					}
				}

				foreach ($hotel_type as $key => $value) {
					if($value == 0){
						unset($hotel_type[$key]);
					}
				}

				if(count($hotel_type) == 0){
					exit(json_encode(array('code'=>2,'msg'=>"当选择不是\"不含住\"或者\"已定时\"必须填写房间类型")));
				}
			}
		}


		//验证费用信息 不能为空

		$user_info = [];
		$user_info['adult'] = (int)input('post.adult');
		$user_info['student'] = (int)input('post.student');
		$user_info['children'] = (int)input('post.children');
		if($user_info['adult']+$user_info['student']+$user_info['children']== 0){
			exit(json_encode(array('code'=>7,'msg'=>"请填写游客费用信息")));
		}
		
		//过滤并验证游客姓名
		$user_name = input('post.user_name');
		foreach ($user_name as $value) {
			$value = htmlentities(trim($value));
			if($value == ""){
				exit(json_encode(array('code'=>8,'msg'=>'游客名字不能为空，请填写')));
			}
		}

		//身份证格式校验
		$user_id = input('post.user_id');

	    //游客身份证为空校验
		foreach ($user_id as $value) {
			$value = htmlentities(trim($value));
			if($value == ""){
				exit(json_encode(array('code'=>9,'msg'=>'游客身份证号不能为空，请填写')));
			}
			if(!preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/',$value)){
				exit(json_encode(array('code'=>10,'msg'=>'游客身份证号不正确，请检查')));
			}	

		}

		//转义备注信息
	    $remarks =trim(input('post.remarks'));


		//获取当前时间
		$add_time = date("Y-m-d H:i:s",time());

		//转换为JSON字符串
		$hotel_time = implode(',',$hotel_time);
		$hotel_type = json_encode($hotel_type);
		$user_info = json_encode($user_info);
		$user_name = json_encode($user_name);
		$user_id = json_encode($user_id);

		//随机生成订单号
		do{
			$order_number = mt_rand(1000,999999);
			$sql = "SELECT `order_number` FROM user WHERE `order_number`= ".$order_number;
			$result = Db::query($sql);
		}while($result != FALSE);

		//从SESSION 中获取创建者信息
		$admin = $this->_admin;

		$user = [
			'userName'=>$data['userName']
			,'iphone'=>$data['iphone']
			,'add_time'=>$add_time
			,'aid'=>$data['aid']
			,'order_number'=>$order_number
            ,'trip_start_time'=>$trip_start_time
            ,'cid'=>$admin['cid']
		];
		$user_trip = [
			'arrive_traffic'=>$data['arrive_traffic']
			,'arrive_time'=>$data['arrive_time']
			,'arrive_info'=>$data['arrive_info']
			,'return_traffic'=>$data['return_traffic']
			,'return_time'=>$data['return_time']
			,'return_info'=>$data['return_info']
			,'meal'=>$data['meal']
			,'hotel'=>$data['hotel']
			,'hotel_type'=>$hotel_type
			,'hotel_time'=>$hotel_time
			,'user_info'=>$user_info
			,'remarks'=>$remarks
		];
		$user_list = [
			'user_name'=>$user_name
			,'user_id'=>$user_id
		];

		Db::startTrans();
		try{
			$id = Db::table('user')->insertGetId($user);
			$user_trip['uid'] = $id;
			$user_list['uid'] = $id;
			
			//拼接
			$trip = [];
			foreach ($trip_time as $key => $value) {
				array_push($trip,['uid'=>$id,'trip_time'=>$value,'trip'=>$trip_list[$key]]);
			}
			Db::table('user_trip_list')->data($trip)->insertAll();
			Db::table('user_trip')->insert($user_trip);
			$result = Db::table('user_list')->insert($user_list);
			 Db::commit();
		}catch (Exception $e) {
			 Db::rollback();
			 echo $e->getMessage();
    		 exit(json_encode(array('code'=>12,'msg'=>'未知错误导致创建失败,请重试')));
		}
		exit(json_encode(array('code'=>0,'msg'=>'创建成功！')));				
	}



	public  function user_list()
    {
		$this->assign('title','订单列表');
		return view();
	}

	//动态表格数据
	public function user_list_info()
    {
        $page = (int)input('get.page');
        $limit = (int)input('get.limit');
        $order_number = trim(input('get.order_number'));
        $userName= trim(input('get.userName'));
        $iphone= trim(input('get.iphone'));
        $order_create_time= trim(input('get.order_create_time'));
        $order_end_time= trim(input('get.order_end_time'));
        $create_time= trim(input('get.create_time'));
        $end_time= trim(input('get.end_time'));

        $search_condition = [];
        if(!empty($order_number)){
            array_push($search_condition,['order_number','=',$order_number]);
        }

        if(!empty($userName)){
            array_push($search_condition,['userName','=',$userName]);
        }

        if(!empty($iphone)){
            array_push($search_condition,['iphone','Like','%'.$iphone.'%']);
        }

        if(!empty($order_create_time)){
            if(empty($order_end_time)){
                array_push($search_condition,['add_time','>=',$order_create_time]);
            }else{
                array_push($search_condition,['add_time','between time',[$order_create_time,$order_end_time]]);
            }
        }

        if(!empty($create_time)){
            if(empty($end_time)){
                array_push($search_condition,['trip_start_time','>=',$create_time]);
            }else{
                array_push($search_condition,['trip_start_time','between time',[$create_time,$end_time]]);
            }
        }

        $admin = $this->_admin;
        $where = [
            'status'=>0,
            'cid'=>$admin['cid']
        ];

        $data = [];
        $result = Db::table('user')->where($where)->where($search_condition)->order('add_time','desc')->paginate($limit);
        $user_info  = $result->items();
        $admins = Db::table('admins')->field('id,username')->select();
        foreach ($user_info as $key=>$value){
            switch ($value['status']){
                case 0:$user_info[$key]['status'] = '已提交';break;
                case 1:$user_info[$key]['status'] = '未提交';break;
            }

            foreach ($admins as $k=>$v){
                if($value['aid'] == $v['id'])
                {
                    $user_info[$key]['aid'] = $v['username'];
                }
            }
        }

        $data['code'] = 0;
        $data['msg'] = "";
        $data['count'] = $result->total();
        $data['data'] = $user_info;
//        使用 return 统一报错
        exit(json_encode($data));
    }

	//详细信息查看
	public function order_show(){
        $uid = (int)input('get.uid');
        $admin = $this->_admin;

        //获取用户基本信息
        $user = Db::table('user')->where(array('id'=>$uid,'cid'=>$admin['cid']))->find();
        //获取具体信息
        $user_trip = Db::table('user_trip')->where(array('uid'=>$user['id']))->find();
        //获取行程列表
        $user_trip_list = Db::table('user_trip_list')->where(array('uid'=>$user['id']))->select();

        //创建者查询
        $admins = Db::table('admins')->field('username')->where('id',$user['aid'])->find();
        $user['aid'] = $admins['username'];

        //$user_trip改造，需要修改 arrive_traffic，return_traffic，meal，hotel，user_info
        $traffic = Db::table('user_traffic')->field('id,traffic')->select();
        foreach ($traffic as $value){
            if($user_trip['arrive_traffic'] === $value['id']){
                $user_trip['arrive_traffic'] = $value['traffic'];
            }
            if($user_trip['return_traffic'] === $value['id']){
                $user_trip['return_traffic'] = $value['traffic'];
            }
        }

        switch ($user_trip['meal']){
            case 0:$user_trip['meal'] = '全程不含餐';break;
            case 1:$user_trip['meal'] = '全程含餐';break;
        }

        if($user_trip['hotel'] === 1){
            $user_trip['hotel_type']= json_decode($user_trip['hotel_type'],true);
            $user_trip['user_info']= json_decode($user_trip['user_info'],true);
            foreach($user_trip['user_info'] as $key=>$value){
                if($value === 0){
                    unset($user_trip['user_info'][$key]);
                }
            }
        }

        //获取所哟行程
        $trips = Db::table('trips')->field('trip_id,trip_name,trip_info')->select();
        foreach ($user_trip_list as $key=>$value){
            foreach ($trips as $v){
                if($value['trip'] == $v['trip_id']){
                    $user_trip_list[$key]['trip'] = $v['trip_name'];
                }
            }
        }

        $user_list = Db::table('user_list')->where('uid',$admin['id'])->select();
        //行程信息
        $data['user'] = $user;
        $data['user_trip'] = $user_trip;
        $data['user_trip_list'] = $user_trip_list;
        $data['user_list'] = $user_list;

	    return view('',$data);
	}

	//订单变更
	public function order_change(){
		
	}

	//删除订单
	public function order_del(){
        $uid = (int)input('post.uid');
        $res = Db::table('user')->where('id', $uid)->update(['status'=>1]);
        if($res){
            exit(json_encode(array('code'=>0,'msg'=>'删除成功!')));
        }
        exit(json_encode(array('code'=>1,'msg'=>'删除失败!')));
	}
}