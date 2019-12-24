<?php

namespace app\admins\controller;

use think\Controller;
use think\Db;

class Base extends Controller
{
	protected function initialize() 
	{
		//获取当前登录信息
		$this->_admin = session('user');

		//验证登录情况,未登录跳转到登录界面
		if(!$this->_admin){
			header('Location:/index/account/login.html');
			exit;
		}

		//如果不是超级管理员需要验证权限，超级管理员不需要

        $admin  = $this->_admin;
        $groups = Db::table('admins')->where('id',$admin['id'])->item();
			$rights = json_decode($groups['rights']);
			$request = request()->controller();
			$method = request()->action();
			$res = Db::table('admin_menus')->where(array('controller'=>$request,'method'=>$method))->item();
			if(!$res){
				$this->request_error('对不起，您访问的页面不存在');
			}
			if($res['status']>0){
				$this->request_error('对不起，您访问的页面已被禁用');
			}
			if(!in_array($res['mid'],$rights)){
				$this->request_error('对不起，您无权访问');
			}
//		}
	}


	protected function request_error($msg)
	{
		if(request()->isAjax()){
			exit(json_encode(array('code'=>1,'msg'=>$msg)));
		}
		exit($msg);
	}


	public function testMeth($obj)
	{
		echo '<pre>';
		dump($obj);
	}
}