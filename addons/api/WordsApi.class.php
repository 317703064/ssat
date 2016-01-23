<?php
/**
 * 单词相关功能接口
 * @author liubin
 *
 */
class WordsApi extends Api{
	var $paper_id;
	var $paper_section;
	
	public function __construct(){
		parent::__construct();
		$this->paper_id = intval( $this->data[ 'paper_id' ] );
		$this->paper_section = intval( $this->data[ 'paper_section' ] );
	}
	/**
	 * section对应的单词
	 * @return array
	 */
	public function section_words(){
		if( $this->paper_id && $this->paper_section ){
			$map[ 'paper_id' ] = $this->paper_id;
			$map[ 'paper_section' ] = $this->paper_section;
			$section_words = M( 'paper_section_words' )->where( $map )->field( '*' )->findAll();
			return $section_words;
		}
	}
	/**
	 * 返回单词相关的短句
	 */
	public function words_sentence(){
		if( $this->data[ 'word_name' ] ) {
			$word_name = t( $this->data[ 'word_name' ] );
			$map[ 'word_name' ] = $word_name;
			$data = M( 'words_se' )->where( $map )->findAll();
			if( $data ){
				return $data;
			} else {
				return array( 'status'=>0 , 'msg'=>'没有结果' );
			}
		}
	}
	/**
	 * 返回单词对应的试题信息
	 * @return array
	 */
	public function words_section_infos(){
		$word_name = t( $this->data[ 'word_name' ] );
		$map[ 'word_name' ] = $word_name;
		$list = M( 'paper_section_words' )->where( $map )->findAll();
		return $list;
	}
	public function get_section_add(){
		if( $this->data[ 'id' ] ){
			$id = intval( $this->data[ 'id' ] );
			$list = M( 'paper_section_words' )->join(' as a left join ts_paper_words as b on a.word_name=b.word_name')->where('a.id>'.$id)->field('b.word_name,count')->findAll();
			return $list;
		} else {
			$id = M( 'paper_section_words' )->getField('max(id)');
			return array( 'status'=>$id , 'msg'=>'最大id' );
		}
	}
	public function all_section_words(){
		$list = M( 'paper_words' )->field('word_name,count')->findAll();
		file_put_contents(UPLOAD_PATH.'/section_word.txt' , json_encode( $list ));
	}
	/**
	 * 返回单词测试题
	 * @return array
	 */
	public function get_word_tests(){
		$word_names = explode( '|' , $this->data[ 'word_name' ] );
		$word_names = array_filter( $word_names , 't' );
		if( $word_names ){
			$map[ '_string' ] = "word_name in ('".implode( "','" , $word_names )."')";
			$list = M( 'word_test' )->where( $map )->findAll();
			return $list;
		}
	}
	public function update_words(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$words = M( 'paper_section_words' )->where('id>21184')->order( 'id' )->limit( $p.',1000' )->findAll();
		if( $words ){
			foreach ( $words as $v ){
				$v[ 'word_name' ] = trim( $v[ 'word_name' ] );
				$res = M( 'paper_section_words' )->save( $v );
				
				$map[ 'word_name' ] = $v[ 'word_name' ];
				$wordinfo = M( 'paper_words' )->where( $map )->find();
				if( $wordinfo ){
					M( 'paper_words' )->where( $map )->setInc('count');
					dump(M()->getLastSql());
				} else {
					$data[ 'word_name' ] = $v[ 'word_name' ];
					$data[ 'count' ] = 1;
					M( 'paper_words' )->add( $data );
					dump(M()->getLastSql());
				}
			}
		} else {
			dump( 'Over' );
		}
	}
	public function update1(){
		$words = M( 'paper_section_words' )->where('id>13368')->order( 'id' )->limit( '0,1000' )->findAll();
		foreach ( $words as $v ){
			$v[ 'word_name' ] = trim( $v[ 'word_name' ] );
		
			$map[ 'word_name' ] = $v[ 'word_name' ];
			$wordinfo = M( 'paper_words' )->where( $map )->find();
			if( $wordinfo['count'] ){
				M( 'paper_words' )->where( $map )->setDec( 'count' );
				dump(M()->getLastSql());
			} else {
				M( 'paper_words' )->where('id='.$wordinfo['id'])->delete();
				dump(M()->getLastSql());
			}
		}
	}
	public function update2(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$words = M( 'paper_section_words' )->where("id>21184 and paraphrase=''")->order( 'id' )->limit( $p.',1000' )->findAll();
		foreach ( $words as $v ){
			$smap[ 'word_name' ] = $v[ 'word_name' ];
			$smap[ 'type' ] = $v[ 'type' ];
			$paraphrase = M( 'paper_section_words' )->where( $smap )->getField( 'paraphrase' );
			if( $paraphrase ){
				$data[ 'paraphrase' ] = $paraphrase;
				$wordinfo = M( 'paper_section_words' )->where( 'id='.$v[ 'id' ] )->save( $data );
				dump(M()->getLastSql());
			} else {
				M('paper_section_words_copy')->add( $v );
			}
		}
	}
	public function update3(){
		$words = M( 'paper_section_words_copy' )->findAll();
		foreach ( $words as $v ){
			$smap[ 'word_name' ] = $v[ 'word_name' ];
			$smap[ 'id' ] = array( 'gt' , 21184 );
			$paraphrase = M( 'paper_section_words' )->where( $smap )->save( array( 'paraphrase'=>$v['paraphrase'] ) );
		}
	}
	public function update_temp(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$words = M( 'temp' )->limit( $p.',1000' )->findAll();
		foreach ( $words as $v ){
			$v[ 'soundmark' ] = str_replace(',', '|', $v[ 'soundmark' ] );
			$v[ 'soundmark' ] = str_replace(';', '|', $v[ 'soundmark' ] );
			$v[ 'soundmark' ] = str_replace('，', '|', $v[ 'soundmark' ] );
			$v[ 'soundmark' ] = str_replace('；', '|', $v[ 'soundmark' ] );
			$tdata = explode('|',$v[ 'soundmark' ]);
			$tdata = array_unique( array_filter( $tdata , 'trim' ) );
			$data[ 'soundmark' ] = implode( ',' , $tdata );
			M( 'temp' )->where( "word_name='".$v[ 'word_name' ]."'" )->save( $data );
			dump(M()->getLastSql());
		}
		if( !$words ){
			dump( 'over' );
		}
	}
	public function del_error_word(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$words = M( 'paper_section_words' )->order( 'id' )->limit( $p.',1000' )->findAll();
		if( $words ){
			foreach ( $words as $v ){
				$map[ 'paper_id' ] = $v[ 'paper_id' ];
				$map[ 'paper_section' ] = $v[ 'paper_section' ];
				$map[ 'word_name' ] = $v[ 'word_name' ];
				$count = M( 'paper_section_words' )->where( $map )->count();
				if( $count > 1 ){
					$id = M( 'paper_section_words' )->where( $map )->getField( 'id' );
					M( 'paper_section_words' )->where( 'id='.$id )->delete();
					dump( M()->getLastSql() );
					
					$pmap[ 'word_name' ] = $v[ 'word_name' ];
					M( 'paper_words' )->where( $pmap )->setDec( 'count' );
					dump( M()->getLastSql() );
				}
			}
		} else {
			dump( 'Over' );
		}
	}
	public function update_banlang(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$words = M( 'gre' )->limit( $p.',1000' )->findAll();
		if( $words ){
			foreach ( $words as $v ){
				$map[ 'word_name' ] = strtolower( trim( $v[ 'word_name' ] ) );
				$res = M( 'paper_section_words' )->where( $map )->find();
				if( $res ){
					$data[ 'word_name' ] = $map[ 'word_name' ];
					M( 'gre1' )->add( $data );
					dump(M()->getLastSql());
				} else {
					$data[ 'word_name' ] = $map[ 'word_name' ];
					M( 'gre2' )->add( $data );
					dump(M()->getLastSql());
				}
			}
		} else {
			dump('over');
		}
		
	}
	public function update_balang1(){
		
	}
	public function test(){
		dump(trim('arbitrator  '));
	}
	public function add_word_test(){
		set_time_limit(0);
		if( $_GET[ 'p' ] ){
			$p = $_GET['p'] * 1000;
		} else {
			$p = 0;
		}
		dump($p);
		$list = M( 'paper_words' )->limit( $p.',1000' )->order( 'id' )->findAll();
		$temarr = array( 1=>'a',2=>'b',3=>'c',4=>'d', );
		foreach ( $list as $v ){
			$map[ 'word_name' ] = $v[ 'word_name' ];
			$answer = M( 'paper_section_words' )->where( $map )->order( 'rand()' )->field( 'type,paraphrase' )->find();
			
			$answerindex = rand( 1 , 4 );
			
			$answerarr = array( 1=>'',2=>'',3=>'',4=>'' );
			
			unset( $answerarr[ $answerindex ] );
			
			$amap[ 'word_name' ] = array( 'neq' , $v[ 'word_name' ] );
			$answers = M( 'paper_section_words' )->where( $amap )->order( 'rand()' )->limit(3)->field( 'type,paraphrase' )->findAll();
			
			$i = 0;
			foreach ( $answerarr as &$av ){
				if( $answers[ $i ][ 'type' ] ){
					$av = $answers[ $i ][ 'type' ].'.'.$answers[ $i ][ 'paraphrase' ];
				} else {
					$av = $answers[ $i ][ 'paraphrase' ];
				}
				$i++;
			}
			if( $answer['type'] ){
				$answerarr[ $answerindex ] = $answer['type'].'.'.$answer['paraphrase'];
			} else {
				$answerarr[ $answerindex ] = $answer['paraphrase'];
			}
			
			$data[ 'word_name' ] = $v[ 'word_name' ];
			$data[ 'an_a' ] = $answerarr[ 1 ];
			$data[ 'an_b' ] = $answerarr[ 2 ];
			$data[ 'an_c' ] = $answerarr[ 3 ];
			$data[ 'an_d' ] = $answerarr[ 4 ];
			$data[ 'answer' ] = $temarr[ $answerindex ];
			$data[ 'ctime' ] = $_SERVER[ 'REQUEST_TIME' ];
			
			M( 'word_test' )->add( $data );
			dump( M()->getLastSql() );
			
		}
		if( !$list ){
			dump( 'over' );
		}
	}
}
?>