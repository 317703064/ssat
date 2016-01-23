<?php
class SsatArticleApi extends Api{
	
	private $paper_id;
	private $num;
	private $articledao;
	public function __construct(){
		parent::__construct();
		$this->paper_id = intval( $this->data[ 'paper_id' ] );
		$this->num = intval( $this->data[ 'num' ] );
		$this->articledao = M( 'ssat_article' );
	}
	public function article_list(){
		if( $this->paper_id ){
			$map[ 'paper_id' ] = $this->paper_id;
			$list = $this->articledao->where( $map )->findAll();
			if( $list ){
				return $list;
			} else {
				return array( 'status'=>0 , 'error' =>'没有文章列表' );
			}
		}
	}
	public function article_num(){
		if( $this->paper_id && $this->num ){
			$map[ '_string' ] = $this->num.' between num_start and num_end';
			$map[ 'paper_id' ] = $this->paper_id;
			$article = $this->articledao->where( $map )->find();
			if( $article ){
				return $article;
			} else {
				return array( 'status'=>0 , 'error' =>'找不到文章' );
			}
		} else {
			return array( 'status'=>0 , 'error' => '参数错误' );
		}
	}
}
?>