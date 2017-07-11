<?php

namespace questionmis\modules\common\config;


use common\basedbmodels\BaseCourseSection;
use common\basedbmodels\BaseKnowledgeNode;
use common\basedbmodels\QaBaseAbilityNode;
use common\basedbmodels\QaBaseMethodNode;
use common\basedbmodels\QaBaseShowtypeNode;
use common\basedbmodels\QaBaseThoughtNode;
use common\basedbmodels\QaRelateAbilityQuestion;
use common\basedbmodels\QaRelateMethodQuestion;
use common\basedbmodels\QaRelateShowtypeQuestion;
use common\basedbmodels\QaRelateThoughtQuestion;
use common\basedbmodels\RelateKnowledgeQuestion;
use common\basedbmodels\RelateSectionQuestion;
use questionmis\components\exception\Exception;

class TreeConf {

    const TREE_TYPE_SECTION = 1;
    const TREE_TYPE_SECTION_BY_ASSIST = 101;
    const TREE_TYPE_PAPER = 2;
    const TREE_TYPE_KNOWLEDGE = 21;
    const TREE_TYPE_ISSUE = 22;
    const TREE_TYPE_ISSUE_BY_KNOWLEDGE = 122;
    const TREE_TYPE_METHOD = 23;//方法
    const TREE_TYPE_ABILITY = 24;//能力
    const TREE_TYPE_THOUGHT = 25;//思想
    const TREE_TYPE_KNOWLEDGE_WITH_ISSUE = 26;//替代issue
    const TREE_TYPE_SHOWTYPE = 27;//showtype

    const TREE_TYPE_QSELECT_TASK = 99;//任务树

    public static function realType($type){
        return $type % 100;
    }


    /**
     * @param $type
     * @return bool
     * 获取表名
     */
    public static function relateInfo($type){
        $type = self::realType($type);
        switch($type){
            case TreeConf::TREE_TYPE_SECTION:
                $mainColumn = 'main_type';
                $nodeColumn = 'section_id';
                $relateTable = 'relate_section_question';
                $nodeTable = 'base_course_section';
                $parentColumn = 'parent_id';
                $relateClass = '\common\basedbmodels\RelateSectionQuestion';
                break;
            case TreeConf::TREE_TYPE_KNOWLEDGE_WITH_ISSUE:
                $mainColumn = 'main_type';
                $nodeColumn = 'know_id';
                $relateTable = 'relate_knowledge_question';
                $nodeTable = 'base_knowledge_node';
                $parentColumn = 'parent_id';
                $nodeExt = ['know_type'=>1];
                $relateClass = '\common\basedbmodels\RelateKnowledgeQuestion';
                break;
            case TreeConf::TREE_TYPE_KNOWLEDGE:
                $mainColumn = 'main_type';
                $nodeColumn = 'know_id';
                $relateTable = 'relate_knowledge_question';
                $nodeTable = 'base_knowledge_node';
                $parentColumn = 'parent_id';
                $nodeExt = ['know_type'=>0];
                $relateClass = '\common\basedbmodels\RelateKnowledgeQuestion';
                break;
            case TreeConf::TREE_TYPE_ISSUE:
                $mainColumn = 'main_type';
                $nodeColumn = 'know_id';
                $relateTable = 'relate_knowledge_question';
                $nodeTable = 'base_knowledge_node';
                $parentColumn = 'parent_id';
                $nodeExt = ['know_type'=>1];
                $relateClass = '\common\basedbmodels\RelateKnowledgeQuestion';
                break;
            case TreeConf::TREE_TYPE_METHOD:
                $mainColumn = 'main_type';
                $nodeColumn = 'node_id';
                $relateTable = 'qa_relate_method_question';
                $nodeTable = 'qa_base_method_node';
                $parentColumn = 'parent_id';
                $relateClass = '\common\basedbmodels\QaRelateMethodQuestion';
                break;
            case TreeConf::TREE_TYPE_ABILITY:
                $mainColumn = 'main_type';
                $nodeColumn = 'node_id';
                $relateTable = 'qa_relate_ability_question';
                $nodeTable = 'qa_base_ability_node';
                $parentColumn = 'parent_id';
                $relateClass = '\common\basedbmodels\QaRelateAbilityQuestion';
                break;
            case TreeConf::TREE_TYPE_THOUGHT:
                $mainColumn = 'main_type';
                $nodeColumn = 'node_id';
                $relateTable = 'qa_relate_thought_question';
                $parentColumn = 'parent_id';
                $nodeTable = 'qa_base_thought_node';
                $relateClass = '\common\basedbmodels\QaRelateThoughtQuestion';
                break;
            case TreeConf::TREE_TYPE_SHOWTYPE:
                $mainColumn = 'main_type';
                $nodeColumn = 'node_id';
                $relateTable = 'qa_relate_showtype_question';
                $parentColumn = 'parent_id';
                $nodeTable = 'qa_base_showtype_node';
                $relateClass = '\common\basedbmodels\QaRelateShowtypeQuestion';
                break;
            default:
                throw new Exception('不支持的类型',Exception::ERROR_COMMON);
        }
        return [
            'mainColumn'=>$mainColumn,
            'nodeColumn'=>$nodeColumn,
            'relateTable'=>$relateTable,
            'nodeTable'=>$nodeTable,
            'parentColumn'=>$parentColumn,
            'nodeExt'=> empty($nodeExt) ? [] :$nodeExt,
            'relateClass'=> $relateClass,
        ];
    }
}