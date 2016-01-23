<?php

tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class PaperTestAction extends AdministratorAction{
	public function index(){
		$map = array();
		if( $_POST[ 'content' ] ){
			$map[ 'content' ] = array( 'like' , '%'.t( $_POST[ 'content' ] ).'%' );
		}
		if( $_POST[ 'paper_id' ] ){
			$map[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
		}
		if( $_POST[ 'paper_section' ] ){
			$map[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
		}
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' ,'topic_num','content', 'op_a', 'op_b', 'op_c', 'op_d', 'op_e' , 'answer','explain' , 'DOACTION');
		$this->pageTab[] = array('title'=>'真题列表','tabHash'=>'index','url'=>U('admin/PaperTest/index',array('tabHash'=>'index')));
		$list = M( 'paper_article_test' )->where( $map )->findPage();
		foreach ( $list[ 'data' ] as &$v ){
			$v[ 'DOACTION' ] = '<a href="'.U( 'admin/PaperTest/editTest' , array( 'id' => $v[ 'id' ] ,'tabHash'=>'editTest')).'">编辑</a>';
		}
		$this->searchKey = array( 'paper_id' , 'paper_section' , 'content' );
		$this->pageButton [] = array (
				'title' => '搜索',
				'onclick' => "admin.fold('search_form')"
		);
		$this->assign('pageTitle' , '真题管理');
		$this->displayList( $list );
	}
	public function editTest(){
		if( $_POST ){
			$id = intval( $_POST[ 'id' ] );
			$data[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
			$data[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
			$data[ 'top_num' ] = intval( $_POST[ 'top_num' ] );
			$data[ 'op_a' ] = t( $_POST[ 'op_a' ] );
			$data[ 'op_b' ] = t( $_POST[ 'op_b' ] );
			$data[ 'op_c' ] = t( $_POST[ 'op_c' ] );
			$data[ 'op_d' ] = t( $_POST[ 'op_d' ] );
			$data[ 'op_e' ] = t( $_POST[ 'op_e' ] );
			$data[ 'answer' ] = t( $_POST[ 'answer' ] );
			$data[ 'content' ] = addslashes( $_POST[ 'content' ] );
			$data[ 'explain' ] = addslashes( $_POST[ 'explain' ] );
			
			$data[ 'origin_pro' ] = addslashes( $_POST[ 'origin_pro' ] );
			$data[ 'origin_pro_red' ] = addslashes( $_POST[ 'origin_pro_red' ] );
			$res = M( 'paper_article_test' )->where( 'id='.$id )->save( $data );
			$this->success( '编辑成功' );
		}
		$id = intval( $_GET[ 'id' ] );
		$map = array( 'id' => $id );
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'topic_num' , 'content' , 'op_a', 'op_b', 'op_c' , 'op_d' , 'op_e' , 'answer' , 'explain' , 'origin_pro' , 'origin_pro_red' );
		$this->pageTab[] = array('title'=>'真题列表','tabHash'=>'index','url'=>U('admin/PaperTest/index'));
		$this->pageTab[] = array('title'=>'编辑真题','tabHash'=>'editTest','url'=>U('admin/PaperTest/editTest',array('id'=>$id,'tabHash'=>'editTest')));
		$data = M( 'paper_article_test' )->where( $map )->find();
		$this->assign( 'pageTitle' , '真题管理');
		$this->savePostUrl = U( 'admin/PaperTest/editTest' );
		$this->displayConfig( $data );
	}
	/**
	 * 题目解析疑惑
	 */
	public function confusion(){
		$map = array();
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'topic_num' , 'topic_content' ,'content' , 'answer' , 'answer_time' , 'DOACTION');
		$this->pageTab[] = array('title'=>'解析疑惑列表','tabHash'=>'paperTestConfusion','url'=>U('admin/PaperTest/confusion'));
		$list = M( 'paper_confusion' )->where( $map )->order('is_read desc,id')->findPage();
		
		foreach ( $list[ 'data' ] as &$v ){
			$map[ 'paper_id' ] = $v[ 'paper_id' ];
			$map[ 'paper_section' ] = $v[ 'paper_section' ];
			$map[ 'topic_num' ] = $v[ 'topic_num' ];
			$v[ 'topic_content' ] = M( 'paper_article_test' )->where( $map )->getField( 'content' );
			$v[ 'answer_time' ] = friendlyDate( $v[ 'answer_time' ] , 'full' );
			$v[ 'DOACTION' ] = '<a href="javascript:void(0)" onclick="admin.addAnswer('.$v[ 'id' ].')">回答</a>';
		}
		$this->assign('pageTitle' , '解析疑惑');
		$this->displayList( $list );
	}
	/**
	 * 疑惑解答
	 */
	public function doAnswer(){
		if( $_POST ){
			$id = intval ( $_POST[ 'id' ] );
			if( !$id ){
				echo 'id错误' ;exit;
			}
			$data[ 'answer' ] = t( $_POST[ 'answer' ] );
			if( !$data[ 'answer' ] ){
				echo '回答不能为空' ;exit;
			}
			$data[ 'answer_time' ] = $_SERVER[ 'REQUEST_TIME' ];
			$data[ 'is_read' ] = 0;
			$res = M( 'paper_confusion' )->where( 'id='.$id )->save( $data );
			if( $res ){
				$uid = M( 'paper_confusion' )->where( 'id='.$id )->getField( 'uid' );
				model( 'UserCount' )->updateUserCount( $uid , 'unread_confusion_answer' , 1 );
				echo 1;exit;
			} else {
				echo '修改失败';exit;
			}
		} else {
			$id = intval( $_GET[ 'id' ] );
			$confusion = M( 'paper_confusion' )->where( 'id='.$id )->find();
			$this->pageKeyList = array( 'id' , 'answer' );
			$data[ 'id' ] = $id;
			$this->assign( 'id' , $id );
			$this->assign( 'confusion' , $confusion );
			$this->display();
		}
	}
	/**
	 * 题目解析纠错
	 */
	public function correct(){
		$map = array();
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'topic_num' , 'topic_content' ,'content' , 'DOACTION');
		$this->pageTab[] = array('title'=>'解析纠错列表','tabHash'=>'paperTestConfusion','url'=>U('admin/PaperTest/paperTestCorrect'));
		$list = M( 'paper_confusion' )->where( $map )->findPage();
		$this->assign('pageTitle' , '解析纠错');
		$this->displayList( $list );
	}
}
