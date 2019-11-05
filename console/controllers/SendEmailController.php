<?php
namespace console\controllers;

use common\models\ehr\TrCandidateForSchool;
use Yii;
use yii\console\Controller;


class SendEmailController extends Controller
{

    public function actionTrSchoolAllExport(){
        $emailList = [
            'hujie@knowbox.cn',
            'wangjing2@knowbox.cn',
//            'houlk@knowbox.cn',
            'luyl@knowbox.cn',
            'wangchao@knowbox.cn',
            'liqiang@knowbox.cn'
        ];
        $select = 'mobile, email, name, sex, join_city_name, city_name, school_name, grade, subject_1, subject_2, subject_3, job_type, create_time';
        $list = TrCandidateForSchool::findList([],$select);
        $data = [
            [
                '姓名',
                '手机号',
                '邮箱',
                '性别',
                '入职城市',
                '所在城市',
                '学校',
                '年级',
                '第一志愿',
                '第二志愿',
                '第三志愿',
                '职种',
                '报名时间'
            ]
        ];
        foreach ($list as $v){
            $data[] = [
                $v['name'],
                '\''.$v['mobile'],
                $v['email'],
                [1=>'男',2=>'女'][$v['sex']]??'未知',
                $v['join_city_name'],
                $v['city_name'],
                $v['school_name'],
                [101=>'大一',102=>'大二',103=>'大三',104=>'大四'][$v['grade']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_1']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_2']]??'未知',
                [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_3']]??'未知',
                [1=>'兼职',2=>'全职'][$v['job_type']]??'未知',
                $v['create_time']
            ];
        }
        $filename = '/data/wwwroot/kael/console/runtime/'.microtime(true).'.xls';
        $objPHPExcel = new \PHPExcel();
        $objSheet = $objPHPExcel->getActiveSheet();
        $objSheet->setTitle('候选人名单');
        $objSheet->fromArray($data);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $objWriter->save($filename);
        foreach ($emailList as $emailAddr){
            $mail= \Yii::$app->mailer->compose();
            $mail->setTo($emailAddr)
                ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                ->setSubject("候选人名单")
                ->setTextBody("候选人名单")
                ->attach($filename);
            $ret = $mail->send();
            var_dump($ret);
        }


    }


    public function actionTrSchoolExport(){
        $cityNameList = [
            '长春'=>'hujie@knowbox.cn',
            '西安'=>'wangjing2@knowbox.cn',
            '长沙'=>'houlk@knowbox.cn',
            '合肥'=>'luyl@knowbox.cn',
        ];
        foreach ($cityNameList as $cityName=>$emailAddr){
            $select = 'mobile, email, name, sex, join_city_name, city_name, school_name, grade, subject_1, subject_2, subject_3, job_type, create_time';
            $list = TrCandidateForSchool::findList(['join_city_name'=>$cityName],$select);
            $data = [
                [
                    '姓名',
                    '手机号',
                    '邮箱',
                    '性别',
                    '入职城市',
                    '所在城市',
                    '学校',
                    '年级',
                    '第一志愿',
                    '第二志愿',
                    '第三志愿',
                    '职种',
                    '报名时间'
                ]
            ];
            foreach ($list as $v){
                $data[] = [
                    $v['name'],
                    '\''.$v['mobile'],
                    $v['email'],
                    [1=>'男',2=>'女'][$v['sex']]??'未知',
                    $v['join_city_name'],
                    $v['city_name'],
                    $v['school_name'],
                    [101=>'大一',102=>'大二',103=>'大三',104=>'大四'][$v['grade']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_1']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_2']]??'未知',
                    [-1=>'未填写',0=>'数学',1=>'语文',2=>'英语'][$v['subject_3']]??'未知',
                    [1=>'兼职',2=>'全职'][$v['job_type']]??'未知',
                    $v['create_time']
                ];
            }
            $filename = '/data/wwwroot/kael/console/runtime/'.microtime(true).'.xls';
            $objPHPExcel = new \PHPExcel();
            $objSheet = $objPHPExcel->getActiveSheet();
            $objSheet->setTitle('候选人名单');
            $objSheet->fromArray($data);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
            $objWriter->save($filename);
            $mail= \Yii::$app->mailer->compose();
//            $emailAddr = 'wangchao@knowbox.cn';
            $mail->setTo($emailAddr)
                ->setFrom( ['mail_service@knowbox.cn'=>'基地邮件通知'])
                ->setSubject("候选人名单({$cityName})")
                ->setTextBody("候选人名单({$cityName})")
                ->attach($filename);
            $ret = $mail->send();
            var_dump($ret);
        }

    }

}
