<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxsearch\Helper\DB\Mysql;

use Magento\Framework\App\ResourceConnection;

class Fulltext extends \Magento\Framework\DB\Helper\Mysql\Fulltext
{
    /**
     * FULLTEXT search in MySQL search mode "natural language"
     */
    const FULLTEXT_MODE_NATURAL = 'IN NATURAL LANGUAGE MODE';

    /**
     * FULLTEXT search in MySQL search mode "natural language with query expansion"
     */
    const FULLTEXT_MODE_NATURAL_QUERY = 'IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION';

    /**
     * FULLTEXT search in MySQL search mode "boolean"
     */
    const FULLTEXT_MODE_BOOLEAN = 'IN BOOLEAN MODE';

    /**
     * FULLTEXT search in MySQL search mode "query expansion"
     */
    const FULLTEXT_MODE_QUERY = 'WITH QUERY EXPANSION';

    /**
     * FULLTEXT search in MySQL MATCH method
     */
    const MATCH = 'MATCH';

    /**
     * FULLTEXT search in MySQL AGAINST method
     */
    const AGAINST = 'AGAINST';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;
    protected $searchbox;
    protected $helper;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        \Magebees\Ajaxsearch\Block\Searchbox $searchbox,
        \Magebees\Ajaxsearch\Helper\Data $helper,
        ResourceConnection $resource
    ) {
    
        $this->searchbox = $searchbox;
        $this->helper = $helper;
        $this->connection = $resource->getConnection();
    }
	public function getMatchQueryRelevanceScore($columns, $expression, $mode = self::FULLTEXT_MODE_NATURAL)
    {
       $config=$this->searchbox->getConfig();
		 if ($config['enable']) {
            if($expression!='')
            {
                if (is_array($columns)) {
                $columns = implode(', ', $columns);
                }
              $columns = is_array($columns) ? array_pop($columns):$columns;
                $expression = str_replace('*', '', $expression);
                $expression = str_replace('%', '%%', $expression);
                
                $new=array_unique(explode(" ", $expression));
        
                $condition=implode(" ", $new);
                $and_condi[] = "'";
                $or_condi [] = array();
             
                for ($i=0; $i<count($new); $i++) {
                       
                        $expression = '([^>]*'.$new[$i].')';
                        $and_condi[] = $expression;              
                        $or_condi[$i] = $columns.' RLIKE '."'".'([^>]*'.$new[$i].'*)'."'";
                }
                $and_condi[] = "'";
                $multiple_conditions = array();
                $multiple_conditions[] = $columns.' RLIKE '.implode("",$and_condi);
                $conditions = array_merge($multiple_conditions,$or_condi);
            }
            else
            {
              $conditions=[];  
            }			
			return $conditions;
		 }else{
		 if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
		$expression = $this->connection->quote($expression);
		$condition = self::MATCH . " ({$columns}) " . self::AGAINST . " ({$expression} {$mode})";
		return $condition;
		
		 }
    }
	
    public function getMatchQueryScore($columns, $expression, $mode = self::FULLTEXT_MODE_NATURAL)
    {	

		if (is_array($columns)) {
                $columns = implode(', ', $columns);
            }
		
			 $columns = is_array($columns) ? array_pop($columns):$columns;
                $expression = str_replace('*', '', $expression);
                $expression = str_replace('%', '%%', $expression);
				
		$new=array_unique(explode(" ", $expression));
		
		 $condition=implode(" ", $new);
		$condi[] = "'";
		for ($i=0; $i<count($new); $i++) {                       
			 			$expression = '(?=.*'.$new[$i].')';
                        $condi[] = $expression;
                    }
		$condi[] = "'";
		
        if($expression!='')
        {
            return $condition = $columns.' RLIKE '.implode("",$condi);
        }
        else
        {
           return $condition = $columns.' LIKE '.$expression; 
        }
		
		
    }
    /**
     * Method for FULLTEXT search in Mysql, will generated MATCH ($columns) AGAINST ('$expression' $mode)
     *
     * @param string|string[] $columns Columns which add to MATCH ()
     * @param string $expression Expression which add to AGAINST ()
     * @param string $mode
     * @return string
     */
    public function getMatchQuery($columns, $expression, $mode = self::FULLTEXT_MODE_NATURAL)
    {
        $config=$this->searchbox->getConfig();
        $search_type=$this->helper->getSearchType();
        $match_type=$this->helper->getMatchType();
        if ($config['enable']) {
            if (is_array($columns)) {
                $columns = implode(', ', $columns);
            }
            
            if ($search_type==0) {
              //LIKE
                $columns = is_array($columns) ? array_pop($columns):$columns;
                $expression = str_replace('*', '', $expression);
                $expression = str_replace('%', '%%', $expression);
                if ($match_type==1) {
                //Exact match
                    $new=explode(" ", $expression);
                    for ($i=0; $i<count($new); $i++) {
                        $expression = $this->connection->quote('%'.$new[$i].'%');
                        $condi[] = $columns.' LIKE '.$expression;
                    }
                    $condition=implode(" AND ", $condi);
                } else {
                    //Not exact match
                    $new=explode(" ", $expression);
                    for ($i=0; $i<count($new); $i++) {
                        $expression = $this->connection->quote('%'.$new[$i].'%');
                        $condi[] = $columns.' LIKE '.$expression;
                    }
                    $condition=implode(" OR ", $condi);
                }
            } elseif ($search_type==1) {
            //LIKE
                if (is_array($columns)) {
                    $columns = implode(', ', $columns);
                }
                $expression = str_replace('*', '', $expression);
                $expression = str_replace('%', '%%', $expression);
                $expression = $this->connection->quote($expression);
    
                $condition = self::MATCH . " ({$columns}) " . self::AGAINST . " ({$expression} {$mode})";
            } else {
                //Combine
                    $columns = is_array($columns) ? array_pop($columns):$columns;
                    $expression = str_replace('*', '', $expression);
                    $expression = str_replace('%', '%%', $expression);
                    $new=explode(" ", $expression);
                for ($i=0; $i<count($new); $i++) {
                    $like_expression = $this->connection->quote('%'.$new[$i].'%');
                    $condi[] = $columns.' LIKE '.$like_expression;
                }
                    $combine_condi[]=implode(" OR ", $condi);
                    
                    $fulltext_expression = $this->connection->quote('% '.$expression.' %');
                    $combine_condi[] = self::MATCH . " ({$columns}) " . self::AGAINST . " ({$fulltext_expression} {$mode})";
                    $condition=implode(" OR ", $combine_condi);
            }
                    return $condition;
        } else {
            if (is_array($columns)) {
                $columns = implode(', ', $columns);
            }

            $expression = $this->connection->quote($expression);

            $condition = self::MATCH . " ({$columns}) " . self::AGAINST . " ({$expression} {$mode})";
            return $condition;
        }
    }
}

