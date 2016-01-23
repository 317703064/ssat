<?php
class OauthApi extends Api{

/********** 登录注销 **********/

	/**
	 * 认证方法 --using
	 * @param varchar login 手机号或用户名
	 * @param varchar password 密码
	 * @return array 状态+提示
	 */
	public function authorize(){
		$_REQUEST = array_merge($_GET,$_POST);
		if(!empty($_REQUEST['login']) && !empty($_REQUEST['password'])){
			
			$username = addslashes($_REQUEST['login']);
			$password = addslashes($_REQUEST['password']);
			$outhdata = model( 'UserCenterOauth' )->login( $username , $password );
			if( $outhdata->status == 1 ){
// 				$map = "(login = '{$username}' or uname='{$username}') AND is_del=0";
				
				//根据帐号获取用户信息
// 				$user = model('User')->where($map)->field('uid,password,login_salt,is_audit,is_active')->find();
				//判断用户名密码是否正确
				// 			if($user && md5(md5($password).$user['login_salt']) == $user['password']){
				//如果未激活提示未激活
				/* if($user['is_audit']!=1){
				 return array('status'=>0,'msg'=>'您的帐号尚未通过审核');
				}
				if($user['is_active']!=1){
				return array('status'=>0,'msg'=>'您的帐号尚未激活,请进入邮箱激活');
				} */
				//记录token
				// 				if( $login = D('')->table(C('DB_PREFIX').'login')->where("uid=".$user['uid']." AND type='location'")->find() ){
				$data['oauth_token']         = $outhdata->data->oauth_token;
				$data['oauth_token_secret']  = $outhdata->data->oauth_token_secret;
				$data['uid']                 = $outhdata->data->uid;
				if( !M('User')->where('uid='.$data['uid'])->find() ){
					//同步用户
					$userdata[ 'uid' ] = $data[ 'uid' ];
					$userdata[ 'login' ] = $outhdata->data->mobile;
					$userdata[ 'uname' ] = $outhdata->data->mobile;
					$userdata[ 'email' ] = $outhdata->data->email;
					M('User')->add($userdata);
				}
				// 				}else{
				// 					$data['oauth_token']         = getOAuthToken($user['uid']);
				// 					$data['oauth_token_secret']  = getOAuthTokenSecret();
				// 					$data['uid']                 = $user['uid'];
				// 					$savedata['type']            = 'location';
				// 					$savedata = array_merge($savedata,$data);
				// 					D('')->table(C('DB_PREFIX').'login')->add($savedata);
				// 				}
				$data['status'] = '1';
				return $data;
			} else {
				return array('status'=>'0','msg'=>'用户名或密码错误');
			}
    	}else{
    		return array('status'=>'0','msg'=>'用户名或密码不能为空');
    	}
	}

	/**
	 * 注销帐号，刷新token --using
	 * @param varchar login 手机号或用户名
	 * @return array 状态+提示
	 */
	public function logout(){
// 		$login = $this->data['login'];
// 		//判断帐号类型
//     	if($this->isValidPhone($login)){
//     		$map['login'] = $login;
//     	}else{
//     		$map['uname'] = $login;
//     	}
// 		//判断密码是否正确
// 		$user = model('User')->where($map)->field('uid')->find();
		$resdata = model( 'UserCenterOauth' )->authorize_logout( $this->data[ 'oauth_token' ] , $this->data[ 'oauth_token_secret' ] );
		if($resdata->status==1){
			return array('status'=>'1','msg'=>'退出成功');
		}else{
			return array('status'=>'0','msg'=>'退出失败');
		}
	}

/********找回密码*********/

	/**
	 * 发送短信验证码找回密码 --using
	 * @param varchar login 手机号或用户名
	 * @return array 状态+提示
	 */
	public function send_findpwd_code(){
		$login = t ( $this->data['login'] );
// 		if ( !$this->isValidPhone($login) ){
// 			$login = model( 'User' )->where( "login='".$login."'" )->getField('login');
// 			if ( !$login ){
// 				return array('status'=>0,'msg'=>'该帐号尚未注册');
// 			}
// 		}
// 		$smsDao = model('Sms');
// 		$status = $smsDao->sendPasswordCode( $login );
		$res = model( 'UserCenterOauth' )->send_findpw_code( $login );
		if( $res->status ){
			$msg = '发送成功！';
		}else{
			$msg = $res->info;
		}
		$return = array( 'status'=>$res->status , 'msg'=>$msg );
		return $return;
	}

	/**
	 * 判断重置密码验证码是否正确 --using
	 * @param varchar login 手机号或用户名
	 * @param varchar code 验证码
	 * @return array 状态值+提示信息
	 */
	public function check_password_code(){
		$login = t ( $this->data['login'] );
		if ( !$this->isValidPhone($login) ){
			$login = model( 'User' )->where( "login='".$login."'" )->getField('login');
			if ( !$login ){
				return array('status'=>'0','msg'=>'该帐号尚未注册');
			}
		}
		$code = intval ( $this->data['code'] );
		$smsDao = model('Sms');
		if ( $smsDao->checkPasswordCode( $login , $code ) ){
			return array('status'=>'1','msg'=>'验证通过');
		}else{
			return array('status'=>'0', 'msg'=>'验证码错误');
		}
	}

	/**
	 * 重置密码 --using
	 * @param varchar login 手机号或用户名
	 * @param varchar pwd 新密码
	 * @param varchar code 验证码
	 * @return array 状态+提示
	 */
	public function save_user_pwd(){
		$login = t ( $this->data['login'] );
// 		if ( !$this->isValidPhone($login) ){
// 			$login = model( 'User' )->where( "login='".$login."'" )->getField('login');
// 			if ( !$login ){
// 				return array('status'=>'0','msg'=>'该帐号尚未注册');
// 			}
// 		}
		$code = intval ( $this->data['code'] );
		$pwd = t( $this->data['pwd'] );
		//密码验证
// 		if(!model('Register')->isValidPasswordNoRepeat($pwd)){
// 			$msg = model('Register')->getLastError();
// 			$return = array('status'=>'0', 'msg'=>$msg);
// 			return $return;
// 		}
		
		$res = model( 'UserCenterOauth' )->save_findpw( $login , $pwd , $code );
		if( $res->status == 1 ){
			$return = array( 'status'=>'1', 'msg'=>'修改成功' );
		} else {
			$return = array( 'status'=>'0', 'msg'=>$res->info );
		}
		
// 		$smsDao = model('Sms');
// 		if ( $smsDao->checkPasswordCode( $login , $code ) ){
// 			if ( $smsDao->sendPassword( $login , $pwd ) !== false ){
// 				$return = array('status'=>'1', 'msg'=>'修改成功');
// 			} else {
// 				$return = array('status'=>'0', 'msg'=>'修改失败');
// 			}
// 		}else{
// 			$msg = $smsDao->getError();
// 			$return = array('status'=>'0', 'msg'=>$msg);
// 		}
		return $return;
	}
	

/********** 注册 **********/

	/**
	 * 发送注册验证码 --using
	 * @param varchar phone 手机号
	 * @return array 状态值+提示信息
	 */
	public function send_register_code(){
		$phone = t( $this->data['phone'] );
// 		$phone = '18675501664';
		if(!$phone) return array('status'=>0,'msg'=>'请输入手机号');
		$from = 'mobile';

// 		$regmodel = model('Register');
// 		if($phone && !$regmodel->isValidPhone($phone)) {
// 			$msg = $regmodel->getLastError();
// 			$return = array('status'=>0, 'msg'=>$msg);
// 			return $return;
// 		}
// 		$smsModel = model( 'Sms' );
// 		$res = $smsModel->sendRegisterCode( $phone , $from );
		$res = model( 'UserCenterOauth' )->send_register_code( $phone );
		if ( $res->status == 1 ){
			$data['status'] = '1';
			$data['msg'] = '发送成功！';
		} else {
			$data['status'] = '0';
			$data['msg'] = $res->info;
		}
		return $data;
	}

	/**
	 * 判断手机注册验证码是否正确 --using
	 * @param varchar phone 手机号
	 * @param varchar regCode 验证码
	 * @return array 状态值+提示信息
	 */
	public function check_register_code(){
		$phone = t($this->data['phone']);
		$regCode = intval($this->data['regCode']);

		if ( !model('Sms')->checkRegisterCode( $phone , $regCode ) ){
			$return = array('status'=>'0', 'msg'=>'验证码错误');
		}else{
			$return = array('status'=>'1', 'msg'=>'验证通过');
		}
		return $return;
	}

	/**
	 * 注册上传头像 --using
	 * @return array 状态值+提示信息
	 */
	public function register_upload_avatar(){
		$dAvatar = model('Avatar');
		$res = $dAvatar->upload(true);
		return $res;
	}

	/**
	 * 注册帐号 --using
	 * @param varchar phone 手机号
	 * @param varchar regCode 验证码
	 * @param varchar uname 用户名
	 * @param varchar password 密码
	 * @param integer sex 性别 1-男 2-女
	 * @param varchar avatar_url 头像地址
	 * @param integer avatar_width 头像宽度
	 * @param integer avatar_height 头像高度
	 * @return array 状态值+提示信息
	 */
	public function register(){
		$regmodel = model('Register');
		$registerConfig = model('Xdata')->get('admin_Config:register');
		
		$phone = t( $this->data['login'] );
		$regCode = t($this->data['regCode']);
		$uname = t( $this->data['uname'] );
		$sex = intval( $this->data['sex'] );
		$password = t($this->data['password']);
		$avatar['picurl'] = $this->data['avatar_url'];
		$avatar['picwidth'] = intval($this->data['avatar_width']);
		$avatar['picheight'] = intval($this->data['avatar_height']);

		$usercenter = model( 'UserCenterOauth' )->register( $phone , $regCode , $password );
		if( $usercenter->status == 1 ){
			$uid = $usercenter->data->uid;
		} else {
			$return = array('status'=>'0', 'msg'=>$usercenter->info);
			return $return;
		}
		
		//手机号验证
// 		if ( !model('Sms')->checkRegisterCode( $phone , $regCode ) ){
// 			$return = array('status'=>'0', 'msg'=>'验证码错误');
// 		}
// 		if(!$regmodel->isValidPhone($phone)){
// 			$msg = $regmodel->getLastError();
// 			$return = array('status'=>'0', 'msg'=>$msg);
// 			return $return;
// 		}
// 		//头像验证
// 		if($avatar['picurl'] && $avatar['picwidth'] && $avatar['picheight']){
// 			//code
// 		}else{
// 			$required = $this->registerConfig['personal_required'];
// 			if(in_array('face', $required)) return array('status'=>0, 'msg'=>'请上传头像');
// 		}
		//用户名验证
// 		if(!$regmodel->isValidName($uname)) {
// 			$msg = $regmodel->getLastError();
// 			$return = array('status'=>0, 'msg'=>$msg);
// 			return $return;
// 		}
// 		//密码验证
// 		if(!$regmodel->isValidPasswordNoRepeat($password)){
// 			$msg = $regmodel->getLastError();
// 			$return = array('status'=>0, 'msg'=>$msg);
// 			return $return;
// 		}
		//开始注册
		$login_salt = rand(11111, 99999);
		$map['uname'] = $uname;
		$map['sex'] = $sex;
		$map['login_salt'] = $login_salt;
		$map['password'] = md5(md5($password).$login_salt);
		$map['login'] = $this->data['login'] = $phone;
		$map['ctime'] = time();
		// $map['is_audit'] = $registerConfig['register_audit'] ? 0 : 1;
		$map['is_audit'] = 1;
		$map['is_active'] = 1; //手机端不需要激活
		$map['is_init'] = 1; //手机端不需要初始化步骤
		$map['uid'] = $uid;
// 		$map['first_letter'] = getFirstLetter($uname);
// 		if ( preg_match('/[\x7f-\xff]+/', $map['uname'] ) ){	//如果包含中文将中文翻译成拼音
// 			$map['search_key'] = $map['uname'].' '.model('PinYin')->Pinyin( $map['uname'] ); 
// 		}else{
// 			$map['search_key'] = $map['uname'];
// 		}
		
		model('User')->add($map);
		if ( $uid ){
			//第三方登录数据写入
// 			if(isset($this->data['type'])){
// 				$other['oauth_token']         = addslashes($this->data['access_token']);
// 				$other['oauth_token_secret']  = addslashes($this->data['refresh_token']);
// 				$other['type']                = addslashes($this->data['type']);
// 				$other['type_uid']            = addslashes($this->data['type_uid']);
// 				$other['uid']                 = $uid;
// 				M('login')->add($other);
// 			}
			$edata[ 'uid' ] = $uid;
			$edata[ 'time' ] = t( $this->data[ 'exam_time' ] );
			M( 'user_exam' )->add( $edata );
// 			// 添加至默认的用户组
// 			$userGroup = empty($registerConfig['default_user_group']) ? C('DEFAULT_GROUP_ID') : $registerConfig['default_user_group'];
// 			model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));
// 			// 添加双向关注用户
// 			$eachFollow = $registerConfig['each_follow'];
// 			if(!empty($eachFollow)) {
// 				model('Follow')->eachDoFollow($uid, $eachFollow);
// 			}
// 			// 添加默认关注用户
// 			$defaultFollow = $registerConfig['default_follow'];
// 			$defaultFollow = array_diff(explode(',', $defaultFollow), explode(',', $eachFollow));
// 			if(!empty($defaultFollow)) {
// 				model('Follow')->bulkDoFollow($uid, $defaultFollow);
// 			}

			//保存头像
			if($avatar['picurl'] && $avatar['picwidth'] && $avatar['picheight']){
				$dAvatar = model('Avatar');
				$dAvatar->init($uid);
				$data['picurl'] = $avatar['picurl'];
				$data['picwidth'] = $avatar['picwidth'];
				$scaling = 5;
				$data['w'] = $avatar['picwidth'] * $scaling;
				$data['h'] = $avatar['picheight'] * $scaling;
				$data['x1'] = $data['y1'] = 0;
				$data['x2'] = $data['w'];
				$data['y2'] = $data['h'];
				$dAvatar->dosave($data, true);
			}

// 			if($map['is_audit']==1){
// 				return $this->authorize();
// 				$return = array('status'=>1, 'msg'=>'注册成功', 'need_audit'=>0);
// 			}else{
				$return = array('status'=>1, 'msg'=>'注册成功，请等待审核', 'need_audit'=>1);
// 			}
			
			return $return;
		} else {
			$return = array('status'=>'0', 'msg'=>'注册失败');
			return $return;
		}
	}
	
	function test123(){
		return  M('sms')->order('ID desc')->find();
	}

	/**
	 * 记录或获取第三方登录接口获取到的信息 --using
	 * @param varchar type 帐号类型
	 * @param varchar type_uid 第三方用户标识
	 * @param varchar access_token 第三方access token
	 * @param varchar refresh_token 第三方refresh token（选填，根据第三方返回值）
	 * @param varchar expire_in 过期时间（选填，根据第三方返回值）
	 * @return array 状态+提示信息/数据
	 */
	public function get_other_login_info(){
		$type = addslashes($this->data['type']);
		$type_uid = addslashes($this->data['type_uid']);
		$access_token = addslashes($this->data['access_token']);
		$refresh_token = addslashes($this->data['refresh_token']);
		$expire = intval($this->data['expire_in']);
		if(!empty($type) && !empty($type_uid)){
			$user = M('login')->where("type_uid='{$type_uid}' AND type='{$type}'")->find();
			if($user && $user['uid']>0){
				if( $login = M('login')->where("uid=".$user['uid']." AND type='location'")->find() ){
					$data['oauth_token']         = $login['oauth_token'];
					$data['oauth_token_secret']  = $login['oauth_token_secret'];
					$data['uid']                 = $login['uid'];
				}else{
					$data['oauth_token']         = getOAuthToken($user['uid']);
					$data['oauth_token_secret']  = getOAuthTokenSecret();
					$data['uid']                 = $user['uid'];
					$savedata['type']            = 'location';
					$savedata = array_merge($savedata,$data);
					$result = M('login')->add($savedata);
					if(!$result) return array('status'=>0,'msg'=>'获取失败');
				}
				return $data; 
			}else{
				return array('status'=>'0','msg'=>'帐号尚未绑定');
			}
		}else{
			return array('status'=>'0','msg'=>'参数错误');
		}
	}

	/**
	 * 绑定第三方帐号，生成新账号 --using
	 * @param varchar uname 用户名
	 * @param varchar password 密码
	 * @param varchar type 帐号类型
	 * @param varchar type_uid 第三方用户标识
	 * @param varchar access_token 第三方access token
	 * @param varchar refresh_token 第三方refresh token（选填，根据第三方返回值）
	 * @param varchar expire_in 过期时间（选填，根据第三方返回值）
	 */
	public function bind_new_user(){
		$uname = t( $this->data['uname'] );
		$password = t($this->data['password']);
		//用户名验证
		if(!model('Register')->isValidName($uname)) {
			$msg = model('Register')->getLastError();
			$return = array('status'=>0, 'msg'=>$msg);
			return $return;
		}
		//密码验证
		if(!model('Register')->isValidPasswordNoRepeat($password)){
			$msg = model('Register')->getLastError();
			$return = array('status'=>0, 'msg'=>$msg);
			return $return;
		}
		$login_salt = rand(11111, 99999);
		$map['uname'] = $uname;
		$map['login_salt'] = $login_salt;
		$map['password'] = md5(md5($password).$login_salt);
		$map['login'] = $uname;
		$map['ctime'] = time();
		$registerConfig = model('Xdata')->get('admin_Config:register');
		$map['is_audit'] = $registerConfig['register_audit'] ? 0 : 1;
		$map['is_active'] = 1; //手机端不需要激活
		$map['is_init'] = 1; //手机端不需要初始化步骤
		$map['first_letter'] = getFirstLetter($uname);
		if ( preg_match('/[\x7f-\xff]+/', $map['uname'] ) ){	//如果包含中文将中文翻译成拼音
			$map['search_key'] = $map['uname'].' '.model('PinYin')->Pinyin( $map['uname'] ); 
		}else{
			$map['search_key'] = $map['uname'];
		}
		$uid = model('User')->add($map);
		if ( $uid ){
			//第三方登录数据写入
			$other['oauth_token']         = addslashes($this->data['access_token']);
			$other['oauth_token_secret']  = addslashes($this->data['refresh_token']);
			$other['type']                = addslashes($this->data['type']);
			$other['type_uid']            = addslashes($this->data['type_uid']);
			$other['uid']                 = $uid;
			M('login')->add($other);

			$data['oauth_token']         = getOAuthToken($uid);
			$data['oauth_token_secret']  = getOAuthTokenSecret();
			$data['uid']                 = $uid;
			$savedata['type']            = 'location';
			$savedata = array_merge($savedata,$data);
			$result = M('login')->add($savedata);
			
			// 添加至默认的用户组
			$userGroup = empty($registerConfig['default_user_group']) ? C('DEFAULT_GROUP_ID') : $registerConfig['default_user_group'];
			model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));
			// 添加双向关注用户
			$eachFollow = $registerConfig['each_follow'];
			if(!empty($eachFollow)) {
				model('Follow')->eachDoFollow($uid, $eachFollow);
			}
			// 添加默认关注用户
			$defaultFollow = $registerConfig['default_follow'];
			$defaultFollow = array_diff(explode(',', $defaultFollow), explode(',', $eachFollow));
			if(!empty($defaultFollow)) {
				model('Follow')->bulkDoFollow($uid, $defaultFollow);
			}

			return $data;
		}else{
			return array('status'=>'0','msg'=>'注册失败');
		}
	}

/********** 其他公用操作API **********/
	/**
	 * 验证字符串是否是email --using
	 * @param varchar email 邮箱
	 * @return boolean
	 */
	public function isValidEmail($email) {
		return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
	}

	/**
	 * 验证字符串是否是手机号 --using
	 * @param varchar phone 手机号
	 * @return boolean 
	 */
	public function isValidPhone($phone) {
		return preg_match("/^[1][358]\d{9}$/", $phone) !== 0;
	}
}