<?php


namespace app\index\controller;

use app\admins\controller\Base;
use think\Db;

class Admin extends Base
{
    public function admin_list()
    {
        //获取当前登录用户所属公司
        $admin_info = $this->_admin;
        $where = [];
        //非超级管理员只允许读取当前所在公司的员工
        if($admin_info['cid'] != 1){
            $where = ['cid','=',$admin_info['cid']];
        }
        $admin_info = Db::table('admins')->field('password',true)->where($where)->select();

        $company = Db::table('admin_company')->field('cid,company')->where($where)->select();
        foreach ($admin_info as $key=>$value){
            foreach ($company as $v){
                if($value['cid'] == $v['cid']){
                    $admin_info[$key]['cid'] = $v['company'];
                }
            }

            switch ($value['status']){
                case 0:$admin_info[$key]['status'] = '状态正常';break;
                case 1:$admin_info[$key]['status'] = '禁止登录';break;
            }
        }
        $this->assign('admin_info',$admin_info);
        return view();
    }

    public function admin_manage()
    {
        $aid = (int)input('get.aid');
        $admin = Db::table('admins')->field('id,status,rights')->where('id',$aid)->item();
        $jurisdiction = Db::table('admin_menus')->field('mid,pid,title')->select();

        $rights = json_decode($admin['rights']);

        $jurisdiction_list = [];
        //获取一级权限
        foreach ($jurisdiction as $value){
            $value['subordinate'] = [];
            if($value['pid'] === 0){
                array_push($jurisdiction_list,$value);
            }
        }

        foreach ($jurisdiction as $value){
            foreach ($jurisdiction_list as $k=>$v){
                if($v['mid'] === $value['pid']){
                    array_push($jurisdiction_list[$k]['subordinate'],$value);
                }
            }
        }

        $this->assign('aid',$aid);
        $this->assign('rights',$rights);
        $this->assign('status',$admin['status']);
        $this->assign('jurisdiction_list',$jurisdiction_list);
        return view();
    }

    //账户管理保存
    public function admin_manage_preserve(){
        $aid = $_POST['aid'];
        $status = isset($_POST['status']) ? 0:1;
        $rights = json_encode(array_keys($_POST['rights']));

        $res = Db::table('admins')->where('id',$aid)->update(['status'=>$status,'rights'=>$rights]);
        if(!$res){
            exit(json_encode(array('code'=>1,"message"=>"更新失败！，请重试")));
        }
        exit(json_encode(array('code'=>0,"message"=>"更新成功")));
    }

    //账号管理
    public function admin_account()
    {
        $admin = session('user');
        $admin = Db::table('admins')->field('id,username,admin_img')->where('id',$admin['id'])->item();
        $this->assign('admin',$admin);
        return view();
    }

    //头像上传
    public function admin_img(){
        $file = request()->file('file');
        if(!is_null($file)){
            $info = $file->validate(['size'=>20480,'ext'=>'jpg,png,gif'])->move('static/admin_img');
            if($info){
                $admin_image =  $info->getSaveName();

                $src = '/static/admin_img/'.$admin_image;
                exit(json_encode(array("code"=>0,"src"=>$src)));
            }else{
                exit(json_encode(array("code"=>1,"src"=>$file->getError())));
            }
        }
    }

    //管理保存
    public function admin_change(){
        $aid = trim(input('post.aid'));
        $old_pwd = trim(input('post.old_pwd'));
        $new_pwd = trim(input('post.new_pwd'));
        $admin_img = trim(input('post.admin_img'));

        $res = Db::table('admins')->field('username,password')->where('id',$aid)->item();
        	   	//密码校验
	   	if(md5($res['username'].$old_pwd) != $res['password']){
            exit(json_encode(array('code'=>3,'msg'=>'原始密码错误！')));
        }

	   	if(strlen($new_pwd) < 6 ){
            exit(json_encode(array('code'=>3,'msg'=>'新密码不能小于6位！')));
        }

	   	$res = Db::table('admins')->where('id',$aid)->update([
	   	    'password'=>md5($res['username'].$new_pwd)
            ,'admin_img'=>$admin_img
        ]);
        exit(json_encode(array('code'=>0,'msg'=>'保存成功！,重新登录生效')));
    }
}