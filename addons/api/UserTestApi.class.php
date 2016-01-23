<?php
/**
 * 用户真题API
 * @author liubin
 *
 */
class UserTestApi extends Api{
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 返回用户收藏真题
	 * @return unknown
	 */
	public function user_tests(){
		$list = M( 'user_test' )->join(' a left join ts_paper_article_test b on a.test_id=b.id')
								->where( 'a.uid='.$this->mid )->findAll();
		return $list;
	}
	/**
	 * 添加用户收藏
	 * @return number
	 */
	public function add_user_test(){
		$data[ 'test_id' ] = intval( $this->data[ 'test_id' ] );
		$data[ 'info' ] = t( $this->data[ 'info' ] );
		$data[ 'article_id' ] = intval( $this->data[ 'article_id' ] );
		$data[ 'uid' ] = $this->mid;
		$user_test = M( 'user_test' )->where( $data )->find();
		if( !$user_test ){
			$data[ 'ctime' ] = $_SERVER[ 'REQUEST_TIME' ];
			$res = M( 'user_test' )->add( $data );
			return $res ? array('status'=>1,'msg'=>'添加成功') : array('status'=>0,'msg'=>'添加失败');
		} else {
			return array('status'=>-1,'msg'=>'不存在');
		}
	}
	/**
	 * 是否收藏真题
	 * @return number
	 */
	public function exsit_user_test(){
		$data[ 'test_id' ] = intval( $this->data[ 'test_id' ] );
		$data[ 'uid' ] = $this->mid;
		$res = M( 'user_test' )->where( $data )->find();
		return $res ? array('status'=>1,'msg'=>'已收藏') : array('status'=>0,'msg'=>'未收藏');
	}
	/**
	 * 删除真题收藏
	 * @return number
	 */
	public function del_user_test(){
		$data[ 'test_id' ] = intval( $this->data[ 'test_id' ] );
		$data[ 'uid' ] = $this->mid;
		$res = M( 'user_test' )->where( $data )->delete();
		return $res ? array('status'=>1,'msg'=>'删除成功') : array('status'=>0,'msg'=>'删除失败');
	}
	/**
	 * 解析疑问
	 */
	public function add_paper_confusion(){
		$data[ 'uid' ] = $this->mid;
		$data[ 'paper_id' ] = intval ( $this->data[ 'paper_id' ] );
		$data[ 'info' ] = t( $this->data[ 'info' ] );
		$data[ 'paper_section' ] = intval ( $this->data[ 'paper_section' ] );
		$data[ 'topic_num' ] = intval ( $this->data[ 'topic_num' ] );
		$data[ 'content' ] = t( $this->data[ 'content' ] );
		$data[ 'uid' ] = $this->mid;
		$res = M( 'paper_confusion' )->add( $data );
		if( $res ){
			$new_confusion = F( 'new_confusion' );
			if( $new_confusion ){
				if( $new_confusion == 10 ){
					model( 'Sms' )->send_sms( '18500022096' , '亲爱的用户，您的注册验证码为：回答问题' );
					$new_confusion=1;
				} else {
					$new_confusion++;
				}
				F( 'new_confusion' , $new_confusion );
			} else {
				F( 'new_confusion' , 1 );
			}
		}
		return $res ? array('status'=>1,'msg'=>'添加成功') : array('status'=>0,'msg'=>'添加失败');
	}
	public function feedback_confusion(){
		$id = intval( $this->data[ 'id' ] );
		$feedback = intval( $this->data[ 'feedback' ] );
		$res = M( 'paper_confusion' )->where( 'id='.$id.' and uid='.$this->mid )->setField( 'feedback' , $feedback );
		return $res ? array('status'=>1,'msg'=>'添加成功') : array('status'=>0,'msg'=>'添加失败');
	}
	public function confusion_list(){
		M( 'paper_confusion' )->where( 'uid='.$this->mid )->setField( 'is_read' , 1 );
		model( 'UserCount' )->resetUserCount( $this->mid , 'unread_confusion_answer' , 0 );
		$list = M( 'paper_confusion' )->where('uid='.$this->mid)->order('is_read')->findAll();
		if( $list ){
			return $list;
		} else {
			return array('status'=>0,'msg'=>'没有结果') ;
		}
	}
	public function test_info(){
		$map[ 'paper_id' ] = intval( $this->data[ 'paper_id' ] );
		$map[ 'paper_section' ] = intval( $this->data[ 'paper_section' ] );
		$map[ 'topic_num' ] = intval( $this->data['topic_num'] ) ;
		$info = M( 'paper_article_test' )->where( $map )->find();
		return $info ? $info : array('status'=>0,'msg'=>'没有结果');
	}
	/**
	 * 解析纠错
	 */
	public function add_paper_correct(){
		$data[ 'uid' ] = $this->mid;
		$data[ 'paper_id' ] = intval ( $this->data[ 'paper_id' ] );
		$data[ 'paper_section' ] = intval ( $this->data[ 'paper_section' ] );
		$data[ 'topic_num' ] = intval ( $this->data[ 'topic_num' ] );
		$data[ 'topic_content' ] = t( $this->data[ 'topic_content' ] );
		$data[ 'content' ] = t( $this->data[ 'content' ] );
		$res = M( 'paper_correct' )->add( $data );
		return $res ? array('status'=>1,'msg'=>'添加成功') : array('status'=>0,'msg'=>'添加失败');
	}
}
?>
