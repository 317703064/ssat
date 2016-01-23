<?php
class SsatArticleTestApi extends Api{
	
	private $paper_id;
	private $testdao;
	
	public function __construct(){
		parent::__construct();
		$this->paper_id = intval( $this->data[ 'paper_id' ] );
		$this->testdao = M( 'ssat_article_test' );
	}
	
	public function test_list(){
		if( $this->paper_id ){
			$map[ 'paper_id' ] = $this->paper_id;
			$start = intval( $this->data[ 'num_start' ] );
			$end = intval( $this->data[ 'num_end' ] );
			$map[ 'num' ] = array( 'between' , array($start , $end) );
			$list = $this->testdao->where( $map )->findAll();
			if( $list ){
				foreach ( $list as &$v ){
					$v[ 'answer' ] = $v[ 'op_'.$v[ 'answer' ] ];
				}
				return $list;
			} else {
				return array( 'status'=>0 , 'error' =>'找不到题目' );
			}
		} else {
			return array( 'status'=>0 , 'error' => '参数错误' );
		}
	}
}
?>