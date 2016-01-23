<?php
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class PaperAction extends AdministratorAction{
	public function index(){
		$this->pageKeyList = array( 'id' , 'paper_name' , 'sort');
		$list = M( 'paper' )->findPage();
		$this->pageButton [] = array (
				'title' => '添加试卷',
				'onclick' => "javascript:location.href='".U( 'admin/Paper/addPaper' , array('tabHash'=>'addPaper') )."'" 
		);
		$this->_tablist();
		$this->displayList( $list );
	}
	public function _tablist(){
		$this->assign('pageTitle' , '试卷管理');
		$this->pageTab[] = array('title'=>'试卷列表','tabHash'=>'index','url'=>U('admin/Paper/index'));
	}
	public function addPaper(){
		if( $_POST ){
			$paper_name = t( $_POST[ 'paper_name' ] );
			$sort = intval ( $_POST[ 'sort' ] );
			if( !$paper_name ){
				$this->error( '试卷名称不能为空' );
			} else {
				$data[ 'paper_name' ] = $paper_name;
				$data[ 'sort' ] = $sort;
				$res = M( 'paper' )->add( $data );
				if( $res ){
					$this->success( '添加成功' );
				}
			}
		} else {
			$this->_tablist();
			$this->pageKeyList = array( 'paper_name' , 'sort' );
			$this->pageTab[] = array('title'=>'添加试卷','tabHash'=>'addPaper','url'=>U('admin/Paper/addPaper'));
			$this->savePostUrl = U( 'admin/Paper/addPaper' );
			$this->displayConfig();
		}
	}
	public function sectionWords(){
		$map = array();
		if( $_POST[ 'word_name' ] ){
			$map[ 'word_name' ] = array( 'like' , '%'.t( $_POST[ 'word_name' ] ).'%' );
		}
		if( $_POST[ 'paraphrase' ] ){
			$map[ 'paraphrase' ] = array( 'like' , '%'.t( $_POST[ 'paraphrase' ] ).'%' );
		}
		if( $_POST[ 'paper_id' ] ){
			$map[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
		}
		if( $_POST[ 'paper_section' ] ){
			$map[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
		}
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'word_name' , 'type' , 'soundmark' , 'paraphrase' , 'DOACTION');
		$this->pageTab[] = array('title'=>'单词列表','tabHash'=>'sectionWords','url'=>U('admin/Paper/sectionWords',array('tabHash'=>'sectionWords')));
		$list = M( 'paper_section_words' )->where( $map )->findPage();
		foreach ( $list[ 'data' ] as &$v ){
			$v[ 'DOACTION' ] = '<a href="'.U( 'admin/Paper/editSectionWords' , array( 'id' => $v[ 'id' ] ,'tabHash'=>'editSectionWords')).'">编辑</a>';
		}
		$this->searchKey = array( 'paper_id' , 'paper_section' , 'word_name' , 'paraphrase' );
		$this->pageButton [] = array (
				'title' => '搜索',
				'onclick' => "admin.fold('search_form')"
		);
		$this->assign('pageTitle' , '试卷单词管理');
		$this->displayList( $list );
	}
	public function editSectionWords(){
		if( $_POST ){
			$id = intval( $_POST[ 'id' ] );
			$data[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
			$data[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
			$data[ 'word_name' ] = t( $_POST[ 'word_name' ] );
			$data[ 'type' ] = t( $_POST[ 'type' ] );
			//$data[ 'soundmark' ] = addslashes( $_POST[ 'soundmark' ] );
			$data[ 'paraphrase' ] = addslashes( $_POST[ 'paraphrase' ] );
			$res = M( 'paper_section_words' )->where( 'id='.$id )->save( $data );
			$this->success( '编辑成功' );
		}
		$id = intval( $_GET[ 'id' ] );
		$map = array( 'id' => $id );
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'word_name' , 'type' , 'soundmark' , 'paraphrase' );
		$this->pageTab[] = array('title'=>'单词列表','tabHash'=>'sectionWords','url'=>U('admin/Paper/sectionWords',array('tabHash'=>'sectionWords')));
		$this->pageTab[] = array('title'=>'编辑试卷单词','tabHash'=>'editSectionWords','url'=>U('admin/Paper/editSectionWords',array('id'=>$id,'tabHash'=>'editSectionWords')));
		$data = M( 'paper_section_words' )->where( $map )->find();
		$this->assign( 'pageTitle' , '试卷单词管理');
		$this->savePostUrl = U( 'admin/Paper/editSectionWords' );
		$this->displayConfig( $data );
	}
	public function feedback(){
		$this->assign('pageTitle' , '意见反馈');
		$this->pageTab[] = array('title'=>'意见列表','tabHash'=>'index','url'=>U('admin/Paper/index'));
		$this->pageKeyList = array( 'feedback' , 'uid' ,'ctime' );
		$list = M('Feedback')->order('id desc')->findPage();
		foreach ( $list[ 'data' ] as &$v ){
			$v[ 'uid' ] = getUserName( $v[ 'uid' ] );
			$v[ 'ctime' ] = friendlyDate( $v[ 'ctime' ] , 'full' );
		}
		$this->displayList( $list );
	}
}
?>
