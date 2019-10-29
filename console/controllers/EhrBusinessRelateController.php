<?php
namespace console\controllers;



use common\libs\DingTalkApi;
use common\libs\DingTalkApiJZ;
use common\models\DBCommon;
use common\models\ehr\BusinessLineRelateStaff;
use common\models\TmpImportJianzhi;
use yii\console\Controller;


class EhrBusinessRelateController extends Controller
{
    public function actionImportStaffV4(){

        $businessLineToId = [
            '公司级'=>454,
            '网校'=>458,
            '编程课'=>459,
            '轻课'=>460,
            '自学'=>461,
            '公立校老师'=>467,
            '学生用户'=>468,
            '比赛评测用户'=>469,
            '备课和教学资源用户'=>470,
            '公司校家长'=>464,
            '课程用户池'=>465,
            '云选平台'=>471,
            '外部投放'=>472,
        ];


        $filePath = './ehr_business_line_20191027.xlsx';
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        $objPHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $data = $objPHPExcel->getSheet(0)->toArray();
        $rows = [];
        foreach ($data as $k=>$v){
            if($k == 0){
                continue;
            }
            /**
            0企业
            1人员 ID
            2姓名
            3部门ID
            4部门路径
            5分管bp
            6业务线
            7比例
            8业务线2
            9比例2
            10业务线3
            11比例3
            12业务线4
            13比例4
            14业务线5
            15比例5

             */
            if(empty($v[1])){
                //没有人员ID
                continue;
            }
            $kaelId = $v[1];
            if(!empty($v[6])){
                //业务线1
                $businessId = $businessLineToId[$v[6]];
                $hc = intval($v[7])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[8])){
                //业务线2
                $businessId = $businessLineToId[$v[8]];
                $hc = intval($v[9])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[10])){
                //业务线3
                $businessId = $businessLineToId[$v[10]];
                $hc = intval($v[11])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[12])){
                //业务线4
                $businessId = $businessLineToId[$v[12]];
                $hc = intval($v[13])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[14])){
                //业务线5
                $businessId = $businessLineToId[$v[14]];
                $hc = intval($v[15])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[16])){
                //业务线6
                $businessId = $businessLineToId[$v[16]];
                $hc = intval($v[17])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[18])){
                //业务线7
                $businessId = $businessLineToId[$v[18]];
                $hc = intval($v[19])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[20])){
                //业务线8
                $businessId = $businessLineToId[$v[20]];
                $hc = intval($v[21])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
            if(!empty($v[22])){
                //业务线9
                $businessId = $businessLineToId[$v[22]];
                $hc = intval($v[23])/100;
                $rows[] = [4,$kaelId,$businessId,$hc];
            }
        }
        $columns = [
            'version_id','user_id','business_id','hc'
        ];
        echo json_encode($rows,64|256)."\n";
//        DBCommon::batchInsertAll(BusinessLineRelateStaff::tableName(),$columns,$rows,BusinessLineRelateStaff::getDb(),'INSERT');
    }
}
