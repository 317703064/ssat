<?php
/**
 * 文章相关功能接口
 * @author liubin
 *
 */
class ArticleApi extends Api{
	var $paper_id;
	var $paper_section;
	var $article_id;
	
	public function __construct(){
		parent::__construct();
		$this->paper_id = intval( $this->data[ 'paper_id' ] );
		$this->paper_section = intval( $this->data[ 'paper_section' ] );
		$this->article_id = intval( $this->data[ 'article_id' ] );
	}
	/**
	 * 返回文章信息
	 */
	public function get_article(){
		if( $this->article_id ){
			$data = M( 'paper_section_article' )->where( 'id='.$this->article_id )->find();
			if( $data ){
				return $data;
			} else {
				return array( 'error' => '-2' , 'info' => '找不到该文章' );
			}
		} else {
			return  array( 'error' => '-1' , 'info' => '没有文章ID' );
		}
	}
	/**
	 * 返回文章真题解析
	 * @return array
	 */
	public function get_article_test(){
		$start = intval( $this->data[ 'start' ] );//开始题号
		$end = intval( $this->data[ 'end' ] );//结束题号
		if( $this->paper_section && $this->paper_id && $start && $end ){
			$map[ 'paper_id' ] = $this->paper_id;
			$map[ 'paper_section' ] = $this->paper_section;
			$map[ 'topic_num' ] = array( 'between' , array( $start , $end ) );
			$data = M( 'paper_article_test' )->where( $map )->findAll();
			if( $data ){
				return $data;
			} else {
				return array( 'error' => '-2', 'info' => '没有题目结果' );
			}
		} else { 
			return  array( 'error' => '-1' , 'info' => '传入信息不全' );
		}
	}
}
?>
