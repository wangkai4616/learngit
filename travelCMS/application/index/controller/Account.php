<?php
namespace app\index\controller;

use think\Controller;
use think\facade\Validate;
use think\captcha\Captcha;
use think\Db;

class Account extends Controller
{
    public function login()
    {
    	if(session('user')){
    		header('Location:/index/home/index.html');
    		exit();
    	}
    	return view();
    }

    //验证码
    public function verify()
    {
    	$config = ['length'=>3,'useNoise' => false];
    	$captcha = new Captcha($config);
    	return $captcha->entry();
    }

    //登录
	public function dologin()
	{
	   	$username = htmlspecialchars(trim(input('post.username')));
	   	$password = htmlspecialchars(trim(input('post.password')));
	   	$captcha  = htmlspecialchars(trim(input('post.captcha')));
	   	$data = array('username'=>$username,'password'=>$password,'captcha'=>$captcha);
	   	$result = $this->validate($data,['username|用户名'=>'require','password|密码'=>'require','captcha|验证码'=>'require|captcha']);
	   	//验证码错误
	   	if(true != $result){
	   		exit(json_encode(array('code'=>1,'msg'=>$result)));
	   	}

	   	//账号查询
	   	$res = Db::table('admins')->where(array('username'=>$username,'status'=>0))->find();
	   	if(!$res){
	   		exit(json_encode(array('code'=>2,'msg'=>'账号未注册或已被禁用')));
	   	}

	   	//密码校验
	   	if(md5($username.$password) !=	$res['password']){
	   		exit(json_encode(array('code'=>3,'msg'=>'密码错误,请重试')));
	   	}

	   	session('user',$res);
	   	//登陆成功
	   	exit(json_encode(array('code'=>0,'msg'=>'登陆成功')));
	}

	//退出登录
	public function logout()
	{
		if(NULL != session('user',NUll)){
			exit(json_encode(array('code'=>1,'msg'=>'退出失败，请重试')));
		}
		exit(json_encode(array('code'=>0,'msg'=>'退出成功！')));
	}
}
