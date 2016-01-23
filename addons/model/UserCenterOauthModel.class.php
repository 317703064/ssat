<?php
class UserCenterOauthModel{
	
 	private $url = 'http://usercenter.satpasser.com/index.php?app=api&mod=LoginAuth';

	//private $url = 'http://localhost/usercenter/index.php?app=api&mod=LoginAuth';
	
	private $defpost = 'secret_key=00C47E90AADF99C42DA7E632807822FD&app_from=sat&type=mobile';
	
	
	public function login( $login , $password ){
		$curlPost = '&login='.$login.'&password='.$password;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=authorize_account');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function register( $mobile , $code , $password ){
		$curlPost = '&account='.$mobile.'&mobile='.$mobile.'&code='.$code.'&password='.$password;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=register_account');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function send_register_code( $mobile ){
		$curlPost = '&mobile='.$mobile;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=send_register_code');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function send_findpw_code( $mobile ){
		$curlPost = '&mobile='.$mobile;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=send_find_password_code');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function save_findpw( $mobile , $password , $code ){
		$curlPost = '&mobile='.$mobile.'&password='.$password.'&code='.$code;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=save_find_password');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function auth_token( $oauth_token , $oauth_token_secret ){
		$curlPost = '&oauth_token='.$oauth_token.'&oauth_token_secret='.$oauth_token_secret;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=auth_token');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function update_password( $mobile , $password , $oldpassword ){
		$curlPost = '&mobile='.$mobile.'&password='.$password.'&old_password='.$oldpassword;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=update_password');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
	public function authorize_logout( $oauth_token , $oauth_token_secret ){
		$curlPost = '&oauth_token='.$oauth_token.'&oauth_token_secret='.$oauth_token_secret;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.'&act=authorize_logout');//要访问的地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
		curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->defpost.$curlPost);//POST提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
		$output = curl_exec($ch);//执行并获取数据
		curl_close($ch);
		return json_decode( $output );
	}
	
}
?>