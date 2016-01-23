<?php
class SsatPublicTestApi extends Api{
	
	private $paper_id;
	private $type;
	private $sysdao;
	
	public function __construct(){
		parent::__construct();
		$this->paper_id = intval( $this->data[ 'paper_id' ] );
		$this->type = t( $this->data[ 'type' ] );
		$this->sysdao = M( 'sys_ana_test' );
		
	}
	
	public function test_list(){
		if( $this->paper_id && $this->type ){
			$map[ 'paper_id' ] = $this->paper_id;
			$map[ 'type' ] = $this->type;
			$list = $this->sysdao->where( $map )->findAll();
			if( $list ){
				foreach ( $list as &$v ){
					$v[ 'answer' ] = $v[ 'op_'.$v[ 'answer' ] ];
				}
				return $list;
			} else {
				return array( 'status' => 0 , 'error' => '找不到题目' );
			}
		} else {
			return array( 'status' => 0 , 'error' => '参数错误' );
		}
	}
	
	
	
	
}
?>