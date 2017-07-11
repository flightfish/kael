<?php
namespace common\libs;


class Elasticsearch{

    public $host;
    public $index;
    public $type;

    private $searchUrl;
    private $rootUrl;
    private $analyzer;
    private $analyzeUrl;

    private $size = 1000;
    private $from = 0;
    private $sort = [];
    private $aggs = [];

    private $boolQuery;
    private $boolFilter;
    private $fields = [];

    private $minimumShouldMatch = "60%";



    public function __construct($host,$index,$type,$analyzer="ik_max_word")
    {
        $this->host = $host;
        $this->index = $index;
        $this->type = $type;
        $this->analyzer = $analyzer;
        $this->rootUrl = $this->host . '/' . $this->index .'/' .$this->type;
        $this->searchUrl = $this->rootUrl. '/_search';
        $this->analyzeUrl = $this->host. '/_analyze?analyzer='.$this->analyzer;
    }



    public function createMapping($map){
        $url = $this->host . '/' . $this->index;
        $result = AppFunc::curlMethod('PUT',$url,$map);
        return $result;
    }


    /**
     * @param $query
     * @return array
     * 分词
     */
    public function splitWord($query){
        $result = AppFunc::curlMethod('POST',$this->analyzeUrl,["text"=>$query]);
        $result = $result['result'];
        $result = json_decode($result,true);
        $words = array_column($result['tokens'],'token');
        return $words;
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     * 添加 更新
     */
    public function put($id,$data){
        $url = $this->rootUrl.'/'.$id;
        $ret = AppFunc::curlMethod('PUT',$url,$data);
        if(in_array($ret['code'],[200,201])){
            return true;
        }else{
            return $ret;
        }
    }


    /**
     * @param $id
     * @return bool
     * 删除
     */
    public function del($id){
        $url = $this->rootUrl.'/'.$id;
        $ret = AppFunc::curlMethod('DELETE',$url);
        if($ret['code'] == 200){
            return true;
        }else{
            return $ret;
        }
    }

    //删除索引
    public function dropIndex($index){
        $url = $this->host.'/'.$index;
        $ret = AppFunc::curlMethod('DELETE',$url);
        if($ret['code'] == 200){
            return true;
        }else{
            return false;
        }
    }

    public function offset($from){
        $this->from = $from;
        return $this;
    }

    public function limit($limit){
        $this->size = $limit;
        return $this;
    }



    public function queryAll(){
        $queryData = [];
        !empty($this->fields) && $queryData["_source"] = $this->fields;
        $queryData["query"] = ['bool'=>$this->boolQuery];
        if(!empty($this->boolFilter)){
            $queryData["query"]['bool']['filter'] = ['bool'=>$this->boolFilter];
        }
        $queryData["from"] = $this->from;
        $queryData["size"] = $this->size;
        !empty($this->sort) && $queryData["sort"] = $this->sort;
        !empty($this->aggs) && $queryData["aggs"] = $this->aggs;
        $ret = AppFunc::curlMethod('POST',$this->searchUrl,$queryData);
        if($ret['code'] == 200){
            $result = $ret['result'];
            $result = json_decode($result,true);
            $resultList = $result['hits']['hits'];
            $list  = [];
            foreach($resultList as $v){
                $list[$v['_id']] = $v['_source'];
            }
            $count = $result['hits']['total'];
            return ['count'=>$count,'list'=>$list];
        }else{
            return false;
        }
    }


    private function queryAllAdvance(){
        /*
        {
            "query":{
                "function_score":{
                    "query":{
                        "bool":{
                            "must":[
                                {"match":{"section_list.name":"认识图形"} }
                            ],
                            "filter":{
                                    "bool":{
                                        "must":[
                                            {"match":{"section_list.name":"认识图形"} }
                                        ]
                                    }

                                }
                          }

                       },
                       "field_value_factor":{
                            "field":"type_show",
                            "modifier":"log1p",
                            "factor":"0.000001"
                       },
                       "boost_mode": "sum",
                       "max_boost":  1.5
                }
	        }

        }
        */
    }


    public function sortBy($sort){
        if(!is_array($sort)){
            $sortList = explode(",",$sort);
            $sort = [];
            foreach($sortList as $v){
                $v = explode(" ",$v);
                $sort[$v[0]] = isset($v[1]) ? $v[1] : "asc";
            }
        }
        //{"a":{"order":"asc"} },{"b":{"order":"asc"} }
        foreach($sort as $k=>$v){
            $this->sort[$k] = ['order'=>$v];
        }
        return $this;
    }

    public function select($select){
        if(!is_array($select)){
            $select = explode(',',$select);
        }
        $this->fields['include'] = $select;
        return $this;
    }

    public function notSelect($notSelect){
        if(!is_array($notSelect)){
            $notSelect = explode(',',$notSelect);
        }
        $this->fields['exclude'] = $notSelect;
        return $this;
    }

    public function buildSubQuery($k,$v,$exact = false){
        if(!$exact){
            is_array($v) && $v = join(' ',$v);
        }
        $k = explode(',',$k);
        !is_array($v) && $v = [$v];
        $query['bool'] = [];
        foreach($k as $subKey){
            foreach($v as $subValue){
                is_string($subValue) && $subValue = strtolower($subValue);
                $query['bool']['should'][] = $exact ? ["term" => [ $subKey => $subValue ]] : ["match" => [ $subKey => $subValue ]];
            }
        }
        return $query;
    }

    public function buildSubQueryMatchShould($k,$v,$exact = false){
        is_array($v) && $v = join(' ',$v);
        is_string($v) && $v = strtolower($v);
        $k = explode(',',$k);
        if(count($k) == 1){
            $k = $k[0];
            $query['bool']['should'][] = $exact ? ["term" => [ $k => $v ]] : ["match" => [ $k => $v ]];
        }else{
            $query['bool']['should'][] = [
                'multi_match'=>[
                    "query"=> $v,
                    "type" => "best_fields",
                    "fields" => $k,
                    "tie_breaker" => 0.8,
                    "minimum_should_match"=> $this->minimumShouldMatch
                ]
            ];
        }
        return $query;
//        if(empty($minium)){
//            $minium = $this->minimumShouldMatch;
//        }
//        is_array($v) && $v = join(' ',$v);
//        $v = $this->splitWord($v);
//        !is_array($v) && $v = [$v];
//
//        $k = explode(',',$k);
//
//        $querys = [
//            'bool'=>[
//                "should"=>[]
//            ]
//        ];
//        foreach($k as $subKey){
//            $query = ['bool'=>[]];
//            foreach($v as $subValue){
//                is_string($subValue) && $subValue = strtolower($subValue);
//                $query['bool']['should'][] = $exact ? ["term" => [ $subKey => $subValue ]] : ["match" => [ $subKey => $subValue ]];
//                !$exact && $query['bool']['minimum_should_match'] = $minium;
//            }
//            $querys['bool']['should'][] = $query;
//        }
//        return $querys;
    }

    public function andWhere($must,$exact = false){
        if($exact){
            //精准
            foreach($must as $k => $v){
                $this->boolFilter['must'][] = $this->buildSubQuery($k,$v,$exact);
            }
        }else{
            foreach($must as $k => $v){
//                $this->boolQuery['must'][] = $this->buildSubQuery($k,$v,$exact);
                $this->boolQuery['must'][] = $this->buildSubQueryMatchShould($k,$v,$exact);
            }
        }
        return $this;
    }

    public function orWhere($shound,$exact = false){
        if($exact){
            //精准
            foreach($shound as $k => $v){
                $this->boolFilter['shound'][] = $this->buildSubQuery($k,$v,$exact);
            }
        }else {
            foreach ($shound as $k => $v) {
                $this->boolQuery['shound'][] = $this->buildSubQuery($k, $v, $exact);
            }
        }
        return $this;
    }

    public function notWhere($mustNot,$exact = false){
        if($exact){
            //精准
            foreach ($mustNot as $k => $v) {
                $this->boolFilter['must_not'][] = $this->buildSubQuery($k, $v, $exact);
            }
        }else {
            foreach ($mustNot as $k => $v) {
                $this->boolQuery['must_not'][] = $this->buildSubQuery($k, $v, $exact);
            }
        }
        return $this;
    }
}
