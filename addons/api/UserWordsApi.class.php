<?php
/**
 * 用户单词操作
 * @author liubin
 *
 */
class UserWordsApi extends Api{
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 返回用户生词本内容
	 * @return array
	 */
	public function user_words(){
                if( $this->data[ 'order' ] == 'ctime' ){
                        $order = 'a.ctime';
                } else {
			$order = 'b.count';
		}
                if( $this->data[ 'order_type' ] == 'asc' ){
                        $order .= ' asc';
                } else {
			$order .= ' desc';
		}
                $list = M( 'user_words' )->join('a left join ts_paper_words b on a.word_name=b.word_name')->
					 where( 'a.uid='.$this->mid )->field('a.*,b.count as count')->order($order)->findAll();
		return $list;
	}
	/**
	 * 添加单词到生词本
	 * @return number
	 */
	public function add_user_word(){
		$data[ 'word_name' ] = t( $this->data[ 'word_name' ] );
		$data[ 'uid' ] = $this->mid;
		$userword = M( 'user_words' )->where( $data )->find();
		if( $userword ){
			if( $userword[ 'status' ] < 3 ){
				M( 'user_words' )->where( $data )->setInc( 'status' );
			}
			return array('status'=>1,'msg'=>'添加成功');
		} else {
			$data[ 'status' ] = 1;
			$data[ 'paraphrase' ] = t( $this->data[ 'paraphrase' ] );
			$data[ 'type' ] = intval( $this->data[ 'type' ] );
			$data[ 'ctime' ] = $_SERVER[ 'REQUEST_TIME' ];
			$res = M( 'user_words' )->add( $data );
			return $res ? array('status'=>1,'msg'=>'添加成功') : array('status'=>0,'msg'=>'添加失败');
		}
	}
	/**
	 * 检查单词是否存在生词本
	 * @return number
	 */
	public function exsit_user_word(){
		$map[ 'word_name' ] = t( $this->data[ 'word_name' ] );
		$map[ 'uid' ] = $this->mid;
		$res = M( 'user_words' )->where( $map )->find();
		return $res ? array('status'=>1,'msg'=>'已存在生词本') : array('status'=>0,'msg'=>'不存在生词本');
	}
	/**
	 * 删除用户生词本单词
	 * @return number
	 */
	public function del_user_word(){
		$map[ 'word_name' ] = t( $this->data[ 'word_name' ] );
		$map[ 'uid' ] = $this->mid;
		$res = M( 'user_words' )->where( $map )->delete();
		return $res ? array('status'=>1,'msg'=>'删除成功') : array('status'=>0,'msg'=>'删除失败');
	}
}
?>
