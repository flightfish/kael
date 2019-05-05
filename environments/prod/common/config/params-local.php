<?php
return [
    'sendFrom'=>['tikunotice@knowbox.cn'=>'用户中心操作通知'],
    'redis_cache'=>1,
    'redis_cache_time'=>1,

    'ip_list'=>[
        '36.110.92.194','36.110.92.195','36.110.92.196','36.110.92.197',
        '36.110.92.198','106.120.244.34','106.120.244.35','106.120.244.36',
        '106.120.244.37','106.120.244.38','1.202.220.122','1.202.220.123','1.202.220.124','1.202.220.125','1.202.220.126',
        '222.129.22.192','222.129.22.193','222.129.22.194','222.129.22.195','222.129.22.196','222.129.22.197',
        '222.129.22.198','222.129.22.199','222.129.22.200','222.129.22.201','222.129.22.202','222.129.22.203','222.129.22.204',
        '222.129.22.205','222.129.22.206','222.129.22.207'
    ],//公司ip

    'ldap_addr'=>'ldaps://10.9.58.21',
    'ldap_port'=>636,
    'ldap_rdn'=>'cn=Manager,dc=kb,dc=com',
    'ldap_passwd'=>'ldap123_dc',

    'env'=>'prod',

    'meican_email'=>'@mc.knowbox.cn',
    'meican_crop_token'=>'1273560b-9068-400d-bbf9-589d09699986',
    'meican_login'=>'https://meican.com/corps/simpleauth',
    'meican_api'=>'https://api.meican.com',
    'meican_corp_prefix'=>'195691',
    'meican_department'=>[
        '作业盒子',
        '未知部门',
        'BD',
        '布克学堂',
        '运营部',
        '人事部',
        '设计部',
        '产品部',
        '研发部',
        '市场部',
        '财务部',
        '教研部',
        '行政部',
        '小盒课堂',
        '采购部',
        '战略综合部',
        '创意部',
        '内审部',
        '小象编程',
        'GR部',
        '销售部'
    ],

    'qqemail_corpid'=>'wm3eb44ff1466ebc41',
    'qqemail_department'=>4574112202817200851,//公司员工
    'qqemail_corpsecret_txl'=>'7eCI-TyawzhY8PSai5SyFX4rxqlU7ajzfPY57Q58fv4K0y07CcpGtzFFr_rq99OG',//通讯录
    'qqemail_corpsecret_sso'=>'96WZ1in_SmKUlFSeNGuXrO0OgkhdAxxLIHYIJH11L_CRxMjjmZR9a6E5gRiBOPmL',//单点登录
];
