<?php

namespace Magebees\Ajaxsearch\Helper\Search\Adapter\Elasticsearch\Query\Builder;

use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Magento\Framework\Search\Adapter\Preprocessor\PreprocessorInterface;

/**
 * Elasticsearch query match. 
 */
class MatchQuery extends \Magento\Elasticsearch\SearchAdapter\Query\Builder\MatchQuery
{
   
    const QUERY_CONDITION_MUST_NOT = 'must_not';
    private $fieldMapper;
    protected $helper;
    protected $httpRequest;
    public function __construct(
        FieldMapperInterface $fieldMapper,
         \Magebees\Ajaxsearch\Helper\Data $helper,
         \Magento\Framework\App\Request\Http $httpRequest
    ) {
        $this->fieldMapper = $fieldMapper;
        $this->helper = $helper;
         $this->httpRequest = $httpRequest;
    }

   
    public function build(array $elasticselectQuery, RequestQueryInterface $requestQuery, $conditionType)
    {
        $search_type=$this->helper->getSearchType();
        $match_type=$this->helper->getMatchType();
         $config=$this->helper->getConfig();
        
        $queryValue = $this->prepareQuery($requestQuery->getValue(), $conditionType);
        $queries = $this->buildQueries($requestQuery->getMatches(), $queryValue);
        $requestQueryBoost = $requestQuery->getBoost() ?: 1;
        foreach ($queries as $query) {
            $searchQueryBody = $query['body'];
            if(isset($searchQueryBody['match_phrase']))
            {
                 $qmatchKey = 'match_phrase';
            }
            elseif(isset($searchQueryBody['match']))
            {
                $qmatchKey = 'match';
            }
            else
            {
                $qmatchKey = 'query_string';
            }
           
            foreach ($searchQueryBody[$qmatchKey] as $field => $ematchQuery) {
                if((($match_type!=0)&&($search_type!=0))&&($search_type!=2))
                {
                $ematchQuery['boost'] = $requestQueryBoost + $ematchQuery['boost'];
                }        

                if ($config['enable']) {
                 if (($search_type==0)&&($match_type==1)) {
                    $ematchQuery['operator']='and';
                 }
                }
                $searchQueryBody[$qmatchKey][$field] = $ematchQuery;
            }
            $elasticselectQuery['bool'][$query['condition']][] = $searchQueryBody;
        }

        return $elasticselectQuery;
    }
     /**
     * Prepare query.    
     */
    protected function prepareQuery(string $queryValue, string $conditionType): array
    {
       // $queryValue = $this->escape($queryValue);
      //  foreach ($this->preprocessorContainer as $preprocessor) {
            //$queryValue = $preprocessor->process($queryValue);
       // }
        $condition = $conditionType === BoolExpression::QUERY_CONDITION_NOT ?
            self::QUERY_CONDITION_MUST_NOT : $conditionType;
        return [
            'condition' => $condition,
            'value' => $queryValue,
        ];
    }

    /**
     * Creates valid ElasticSearch search condition queries.   
     */
    protected function buildQueries(array $matches, array $queryValue)
    {
        $qconditions = [];
        $search_type=$this->helper->getSearchType();
        $match_type=$this->helper->getMatchType();
        $config=$this->helper->getConfig();
        // Checking for quoted phrase \"phrase test\", trim escaped surrounding quotes if found
        $count = 0;
        $value = preg_replace('#^\\\\"(.*)\\\\"$#m', '$1', $queryValue['value'], -1, $count);
        $searchterm_arr=array_unique(explode(" ", $value));
        $condition = ($count) ? 'match_phrase' : 'match';

        foreach ($matches as $match) {
            $resolvedField = $this->fieldMapper->getFieldName(
                $match['field'],
                ['type' => FieldMapperInterface::TYPE_QUERY]
            );
            if ($config['enable']) {
                if ((($search_type==0)&&($match_type==0))||($search_type==2)){
               
                foreach($searchterm_arr as $queryval)
                {
                    
                 $qconditions[] = [
                        'condition' => $queryValue['condition'],
                        'body' => [
                            'query_string' => [                            
                            'query' => $resolvedField.':*'.$queryval.'*',
                            'boost' => isset($match['boost']) ? $match['boost'] : 1,
                            ],
                        ],
                    ];
                     $qconditions[] = [
                        'condition' => $queryValue['condition'],
                        'body' => [
                            'query_string' => [                            
                            'query' => $resolvedField.':'.$queryval.'',
                            'boost' => isset($match['boost']) ? $match['boost'] : 1,
                            ],
                        ],
                    ];
                }
                }
                else
                {
                    $qconditions[] = [
                    'condition' => $queryValue['condition'],
                    'body' => [
                        $condition => [
                            $resolvedField => [
                                'query' => $value,
                                'boost' => isset($match['boost']) ? $match['boost'] : 1,
                            ],
                        ],
                    ],
                ];
                }
            }
            else
            {
                 $qconditions[] = [
                    'condition' => $queryValue['condition'],
                    'body' => [
                        $condition => [
                            $resolvedField => [
                                'query' => $value,
                                'boost' => isset($match['boost']) ? $match['boost'] : 1,
                            ],
                        ],
                    ],
                ];

            }
        }


        return $qconditions;
    }

    
    protected function escape($value)
    {
        $value = preg_replace('/@+|[@+-]+$/', '', $value);

        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $value);
    }
}
