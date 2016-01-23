<?php
class GrammarApi extends Api{
	public function grammar_cate(){
		$list = M( 'paper_grammar_cate' )->findAll();
		return $list;
	}
	public function grammar_list(){
		$cate_id = intval( $this->data[ 'cate_id' ] );
		$list = M( 'paper_grammar' )->join( ' as a left join ts_grammar_relation b on a.id=b.grammar_id' )
				->where( 'b.cate_id='.$cate_id )->field( 'a.*' )->findAll();
		return $list;
	}
	public function updateData(){
		$list = M( 'paper_grammar' )->findAll();
		foreach ( $list as $v ){
			if( $v[ 'point' ] ){
				$point = explode( ',' , $v[ 'point' ] );
				foreach ( $point as $p ){
					$data[ 'grammar_id' ] = $v[ 'id' ];
					$data[ 'cate_id' ] = $p;
					M( 'paper_grammar_relation' )->add( $data );
					dump( M()->getLastSql() );
				}
			}
		}
	}
}
?>