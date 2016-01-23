<?php
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class SatPublicAction extends AdministratorAction{
	
	public function index(){
		$this->pageKeyList = array( 'id' , 'img' , 'url' , 'ctime' ,'DOACTION' );
		$this->pageTab[] = array('title'=>'数据列表','tabHash'=>'index','url'=>U('admin/SatPublic/index'));
		$this->pageButton [] = array (
				'title' => '添加图片',
				'onclick' => "javascript:location.href='".U( 'admin/SatPublic/addImg' , array( 'tabHash' => 'addImg' ) )."'"
		);
		
		$list = M( 'sat_ad' )->findPage();
		foreach ( $list[ 'data' ] as &$v ){
			$v[ 'img' ] = '<img width="150" height="150" src="'.getAttachUrlByAttachId( $v[ 'img' ] ).'" />';
			$v[ 'ctime' ] = friendlyDate( $v[ 'ctime' ] , 'full' );
			$v[ 'DOACTION' ] = '<a href="'.U( 'admin/SatPublic/editImg' , array( 'id' => $v[ 'id' ] , 'tabHash'=>'editImg') ).'">编辑</a> '.
								'<a href="javascript:admin.delsatimg('.$v['id'].')">删除</a>';
		}
		$this->assign('pageTitle' , '铃铛广告');
		$this->displayList( $list );
	}
	
	public function addImg(){
		if( $_POST ){
			$data[ 'img' ] = intval( $_POST[ 'img' ] );
			$data[ 'url' ] = t( $_POST[ 'url' ] );
			$data[ 'ctime' ] = $_SERVER[ 'REQUEST_TIME' ];
			if( !$data[ 'img' ] ){
				$this->error( '请上传图片！' );
			}
			$res = M( 'sat_ad' )->add( $data );
			if( $res ){
				$this->assign( 'jumpUrl' , U( 'admin/SatPublic/index' ) );
				$this->success( '添加成功' );
			} else {
				$this->error( '添加失败' );
			}
		} else {
			$this->pageKeyList = array( 'img' , 'url' );
			$this->pageTab[] = array('title'=>'数据列表','tabHash'=>'index','url'=>U('admin/SatPublic/index'));
			$this->pageTab[] = array('title'=>'添加图片','tabHash'=>'addImg','url'=>U('admin/SatPublic/addImg'));
			$this->savePostUrl = U( 'admin/SatPublic/addImg' );
			$this->assign('pageTitle' , '添加图片');
			$this->displayConfig();
		}
	}
	
	public function editimg(){
		if( $_POST ){
			$img = t( $_POST[ 'img' ] );
			$url = t( $_POST[ 'url' ] );
			$id = intval( $_POST[ 'id' ] );
			
			if( $id && $img ){
				$data[ 'img' ] = $img;
				$data[ 'url' ] = $url;
				M( 'sat_ad' )->where( 'id' , $id )->save( $data );
				$this->success( '编辑成功' );
			}
		} else {
			$id = intval( $_GET[ 'id' ] );
			$this->pageKeyList = array( 'id' , 'img' , 'url' );
			$this->pageTab[] = array('title'=>'数据列表','tabHash'=>'index','url'=>U('admin/SatPublic/index'));
			$this->pageTab[] = array('title'=>'编辑图片','tabHash'=>'editImg','url'=>U('admin/SatPublic/editImg'));
			$this->savePostUrl = U( 'admin/SatPublic/editImg' );
			$this->assign('pageTitle' , '编辑文章');
			$data = M( 'sat_ad' )->where( 'id=' . $id )->find();
			$this->displayConfig( $data );
		}
	}
	
	public function delImg(){
		$id = intval( $_GET[ 'id' ] );
		$res = M( 'sat_ad' )->where( 'id='.$id )->delete();
		if( $res ){
			$this->success( '删除成功' );
		} else {
			$this->error( '删除失败' );
		}
	}
}
?>