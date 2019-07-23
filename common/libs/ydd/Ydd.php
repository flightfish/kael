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

use common\libs\ydd\constant\ContentType;
use common\libs\ydd\constant\HttpHeader;
use common\libs\ydd\constant\HttpMethod;
use common\libs\ydd\constant\SystemHeader;
use common\libs\ydd\http\HttpClient;
use common\libs\ydd\http\HttpRequest;

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
    public static function depList(){
        $data = YddCore::doGet('/api/Dep/DepGetList');
        return $data;
    }

    //添加部门
    public static function depAdd($name){
        $data = YddCore::doPostJson('/api/Dep/DepAdd',['name'=>$name]);
        return $data;
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

    public static function userAdd($name,$email,$phone,$departmentId,$colorAuth=1,$printAuth=1){
        $data = YddCore::doPostJson('/api/User/Add',[
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

    public static function userDel($account){
        $data = YddCore::doGet('/api/User/Delete',['account'=>$account]);
        return $data;
    }
    public static function userInfo($account){
        $data = YddCore::doGet('/api/User/Get',['account'=>$account]);
        return $data;
    }
    public static function userList(){
        $data = YddCore::doGet('/api/User/GetPageList',['currentPageIndex'=>1,'size'=>5000]);
        return $data;
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