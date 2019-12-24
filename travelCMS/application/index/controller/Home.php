<?php

namespace app\index\controller;

use app\admins\controller\Base;
use think\Db;
use think\facade\View;

class Home extends Base
{
	public function index()
	{

		$res = Db::table('admin_menus')->where('ishidden',0)->order('ord','desc')->select();
		$menus = [];
		foreach ($res as $key => $value) {
			$menus[$value['mid']] = $value;
		}
		//&在PHP中是引用的意思，两个变量同时指向同一个内容，内容改变两个变量都改变
		foreach ($menus as $value) {
			if($value['pid'] != 0){
				$menus[$value['pid']]['children'][] = $value;
				unset($menus[$value['mid']]);
			}
		}
		//获取登录信息
		$admin = $this->_admin;
		//获取公司
        $company = Db::table('admin_company')->field('company')->where('cid',$admin['cid'])->find();
		$this->assign('menus',$menus);
		$this->assign('company',$company['company']);
		return $this->fetch();
	}

	public function welcome()
	{
		return view();
	}
}