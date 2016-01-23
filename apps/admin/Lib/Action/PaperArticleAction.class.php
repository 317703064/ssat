<?php
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class PaperArticleAction extends AdministratorAction{
	/**
	 * 文章管理
	 */
	public function index(){
		$map = array();
		if( $_POST[ 'content' ] ){
			$map[ 'content' ] = array( 'like' , '%'.t( $_POST[ 'content' ] ).'%' );
		}
		if( $_POST[ 'explain' ] ){
			$map[ 'explain' ] = array( 'like' , '%'.t( $_POST[ 'explain' ] ).'%' );
		}
		if( $_POST[ 'paper_id' ] ){
			$map[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
		}
		if( $_POST[ 'paper_section' ] ){
			$map[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
		}
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section' , 'content' , 'explain' , 'DOACTION');
		$this->pageTab[] = array('title'=>'文章列表','tabHash'=>'index','url'=>U('admin/PaperArticle/index'));
		$list = M( 'paper_section_article' )->where( $map )->findPage();
		foreach ( $list[ 'data' ] as &$v ){
			$v[ 'answer_time' ] = friendlyDate( $v[ 'answer_time' ] , 'full' );
			$v[ 'DOACTION' ] = '<a href="'.U( 'admin/PaperArticle/editArticle' , array( 'id' => $v[ 'id' ] ,'tabHash'=>'editArticle')).'">编辑</a>';
		}
		$this->searchKey = array( 'paper_id' , 'paper_section', 'content' , 'explain' );
		$this->pageButton [] = array (
				'title' => '搜索',
				'onclick' => "admin.fold('search_form')" 
		);
		$this->assign('pageTitle' , '试卷文章管理');
		$this->displayList( $list );
	}
	public function editArticle(){
		if( $_POST ){
			$id = intval( $_POST[ 'id' ] );
			$data[ 'paper_id' ] = intval( $_POST[ 'paper_id' ] );
			$data[ 'paper_section' ] = intval( $_POST[ 'paper_section' ] );
			$data[ 'content' ] = addslashes( $_POST[ 'content' ] );
			$data[ 'explain' ] = addslashes( $_POST[ 'explain' ] );
			$data[ 'start' ] = intval( $_POST[ 'start' ] );
			$data[ 'end' ] = intval( $_POST[ 'end' ] );
			$res = M( 'paper_section_article' )->where( 'id='.$id )->save( $data );
			$this->success( '编辑成功' );
		}
		$id = intval( $_GET[ 'id' ] );
		$map = array( 'id' => $id );
		$this->pageKeyList = array( 'id' , 'paper_id' , 'paper_section'  , 'content' , 'explain' , 'start' , 'end');
		$this->pageTab[] = array('title'=>'文章列表','tabHash'=>'index','url'=>U('admin/PaperArticle/index'));
		$this->pageTab[] = array('title'=>'编辑文章','tabHash'=>'editArticle','url'=>U('admin/PaperArticle/editArticle',array('id'=>$id)));
		$data = M( 'paper_section_article' )->where( $map )->find();
		$this->assign('pageTitle' , '文章管理');
		$this->savePostUrl = U( 'admin/PaperArticle/editArticle' );
		$this->displayConfig( $data );
	}
}
?>
