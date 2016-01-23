<?php
/**
 * SAT搜索接口
 * @version 1.0.0
 */
class SearchApi extends Api
{
//     const SPHINX_HOST = 'localhost';
	const SPHINX_HOST = '123.56.92.55';
    const SPHINX_PORT = 9312;
    private $sphinx;
    private $keyword;
    private $dao;
    
    public function __construct(){
    	parent::__construct();
    	// 获取关键字
        $this->keyword = t($this->data['keyword']);
        $this->type = $this->data[ 'type' ] ? intval( $this->data[ 'type' ] ) : 1;
        // 分页数
        $page = intval($this->data['page']);
        $page <= 0 && $page = 1;
        // 分页大小
        $pageSize = 10;
        // 使用sphinx进行搜索功能
        $sphinx = new SphinxClient();
        // 配置sphinx服务器信息
        $sphinx->SetServer(self::SPHINX_HOST, self::SPHINX_PORT);
        // 配置返回结果集
        $sphinx->SetArrayResult(true);
        // 匹配结果偏移量
        $sphinx->SetLimits(($page - 1) * $pageSize, $pageSize, 1000);
        // 设置最大搜索时间
        $sphinx->SetMaxQueryTime(3);
        // 设置搜索模式
        $sphinx->setMatchMode(SPH_MATCH_PHRASE);
        $this->sphinx = $sphinx;
    }

    // 搜索接口
    public function search()
    {
		switch ( $this->type ){
			case 1:
				$index = 'ssat_article_index';
				$this->dao = M( 'ssat_article' );
				break;
			case 2:
				$index = 'ssat_article_test_index';
				$this->dao = M( 'ssat_article_test' );
				break;
			case 3:
				$index = 'ssat_sys_ana_test_index';
				$this->dao = M( 'sys_ana_test' );
				break;
		}
        $list = $this->searchSource( $index  );
        return $list;
    }
    
    public function search_list(){
    	$this->dao = M( 'sys_ana_test' );
    	$sys_list = $this->searchSource( 'ssat_sys_ana_test_index' );
    	
    	$this->dao = M( 'ssat_article' );
    	$article_list = $this->searchSource( 'ssat_article_index' );
    	
    	$this->dao = M( 'ssat_article_test' );
    	$art_test_ids = $this->searchSourceId( 'ssat_article_test_index' );
    	
    	$art_test_list = M( 'ssat_article_test' )->where( array( 'id'=>array('in',$art_test_ids) ) )->field('paper_id,num')->findAll();
    	$semap[ 'paper_id' ] = array( 'in' , getSubByKey( $art_test_list , 'paper_id' ) );
    	$se_article_list = M('ssat_article')->where( $semap )->findAll();
    	foreach ($art_test_list as &$tlist ){
    		foreach ($se_article_list as $se_alist){
    			if( $tlist['num'] > $se_alist['num_start'] && $tlist['num'] < $se_alist['num_end'] ){
    				$tlist['content'] = $se_alist['content'];
    				$tlist['translate'] = $se_alist['translate'];
    				$tlist['num_start'] = $se_alist['num_start'];
    				$tlist['num_end'] = $se_alist['num_end'];
    			}
    		}
    	}
    	$result = array();
    	foreach ( $sys_list as $sv ){
    		$sv['search_type']=$sv['type'];
    		$result[]=$sv;
    	}
    	foreach ( $article_list as $arv ){
    		$arv['search_type']='article';
    		$result[]=$arv;
    	}
    	foreach ( $art_test_list as $tev ){
    		$tev['search_type']='test';
    		$result[]=$tev;
    	}
    	return $result;
    }
    
    public function searchSourceId( $index ){
    	$result = $this->sphinx->query( $this->keyword, $index );
    	$ids = getSubByKey( $result[ 'matches' ] , 'id' );
    	if( $ids ){
    		return $ids;
    	} else {
    		return array();
    	}
    }
    
    public function searchSource( $index , $field='*'){
    	$result = $this->sphinx->query( $this->keyword, $index );
    	$ids = getSubByKey( $result[ 'matches' ] , 'id' );
    	if( $ids ){
    		$map[ 'id' ] = array( 'in' , $ids );
    		$list = $this->dao->where( $map )->field($field)->findAll();
    		return $list;
    	} else {
    		return array();
    	}
    }
    
}
