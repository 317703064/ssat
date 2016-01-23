<?php
/**
 * SAT考试接口
 * @author liubin
 *
 */
class PaperApi extends Api{
	
	/**
	 * 返回真题相关的section和文章ID  --//可优化
	 * @return array
	 */
	public function paper_section_list(){
		$papers = M( 'paper' )->field( 'id,paper_name' )->order('type,sort desc')->findAll();
		$paperkeys = array();
		foreach ( $papers as $p ){
			$paperkeys[ $p[ 'id' ] ] = $p;
		}
		
		$psections = M( 'paper_section_article' )->field( 'id,paper_id,paper_section' )->order( 'paper_id,paper_section' )->findAll();
		
		foreach ( $psections as $v ){
			$paperkeys[ $v[ 'paper_id' ] ][ 'sections' ][ $v[ 'paper_section' ] ][] = $v[ 'id' ];
		}
		
		$returnarray = array();
		foreach ( $paperkeys as &$ov ){
			$sectionarray = array();
			foreach ( $ov[ 'sections' ] as $sk=>$so){
				sort($so);
				$sodata['section'] = $sk;
				$sodata['list'] = $so;
				$sectionarray[] = $sodata;
			}
			
			$ov[ 'sections' ] = $sectionarray;
			$returnarray[] = $ov;
		}
		
		return $returnarray;
	}
	/**
	 * 所有试卷
	 */
	public function paper_list(){
		$list = M( 'paper' )->field( 'id,title' )->findAll();
		return $list;
	}
}
?>
