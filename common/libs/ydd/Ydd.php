<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */


namespace common\libs\ydd;


/**
*请求示例
*如一个完整的url为http://api.aaaa.com/createobject?key1=value&key2=value2
*$host为http://api.aaaa.com
*$path为/createobject
*query为key1=value&key2=value2
*/
class Ydd
{
    //获取部门所有数据
    public static function depList($echoError=true){
        $ret = YddCore::doGet('/api/Dep/DepGetList');
        $retJson = json_decode($ret,true);
        if($echoError && empty($retJson) || !isset($retJson['status']) || $retJson['status'] != 8000){
            echo "=========获取部门列表失败========\n";
            echo strval($ret)  . "\n";
            return false;
        }
        /**
         * [{
        "id": 8123,
        "parentId": null,
        "name": "合伙人",
        "level": 0
        }]
         */
        return $retJson['data'];
    }

    //添加部门
    public static function depAdd($name,$echoError=true){
        /**
        "status": 8000,
        "data": {
        "id": 8396,
        "parentId": null,
        "name": "测试部门",
        "level": 0
        },
        "msg": "Success",
        "isSuccess": true
         */

        $ret = YddCore::doPostJson('/api/Dep/DepAdd',['name'=>$name]);
        $retJson = json_decode($ret,true);
        if($echoError && empty($retJson) || !isset($retJson['status']) || $retJson['status'] != 8000){
            echo "=========创建部门【{$name}】失败========\n";
            echo strval($ret)  . "\n";
            return false;
        }
        return $retJson['data'];
    }

    //添加部门
    public static function depDelete($departmentID){
        $data = YddCore::doPostJson('/api/Dep/DepDelete',['departmentID'=>$departmentID]);
        return $data;
    }

    public static function accountExists($account){
        $data = YddCore::doGet('/Api/User/AccountExists',['account'=>$account]);
        return $data;
    }

    public static function userAdd($name,$email,$departmentId,$colorAuth=1,$printAuth=1,$echoError=true){
        /**
         * {
        "status": 8000,
        "data": "948238846",
        "msg": "Success",
        "isSuccess": true
        }
         */
        $ret = YddCore::doPostJson('/api/User/Add',[
            'name'=>$name,
            'sex'=>0,
            'phone'=>'',
            'department_Id'=>$departmentId,
            'email'=>$email,
            'colorAuth'=>$colorAuth,
            'printAuth'=>$printAuth,
        ]);
        $retJson = json_decode($ret,true);
        if($echoError && empty($retJson) || !isset($retJson['status']) || $retJson['status'] != 8000){
            echo "=========创建部门【{$name}】失败========\n";
            echo strval($ret)  . "\n";
            return false;
        }
        return $retJson['data'];
    }

    public static function userDel($account){
        $data = YddCore::doGet('/api/User/Delete',['account'=>$account]);
        return $data;
    }
    public static function userInfo($account){
        $data = YddCore::doGet('/api/User/Get',['account'=>$account]);
        return $data;
    }
    public static function userList($page=1,$pagesize=5000,$echoError=true){
        $ret = YddCore::doPostJson('/api/User/GetPageList',['currentPageIndex'=>$page,'size'=>$pagesize]);
        $retJson = json_decode($ret,true);
        if($echoError && empty($retJson) || !isset($retJson['status']) || $retJson['status'] != 8000){
            echo "========获取用户列表失败========\n";
            echo strval($ret)  . "\n";
            return false;
        }
        /**
        [{
        "account": "1928581282",
        "name": "刘夜",
        "sex": 1,
        "phone": "13911552662",
        "telephone": null,
        "birthday": null,
        "companyId": "105383",
        "department_Id": 8123,
        "departmentName": "合伙人",
        "cardNo": "",
        "email": "liuye@knowbox.cn",
        "colorAuth": 2,
        "printAuth": 0
        }]
         */
        return $retJson['data'];
    }
    public static function userUpdate($account,$name,$email,$phone,$departmentId,$colorAuth=1,$printAuth=1){
        $data = YddCore::doPostJson('/api/User/Update',[
            'account'=>$account,
            'name'=>$name,
            'sex'=>0,
            'phone'=>$phone,
            'department_Id'=>$departmentId,
            'email'=>$email,
            'colorAuth'=>$colorAuth,
            'printAuth'=>$printAuth,
        ]);
        return $data;
    }

}