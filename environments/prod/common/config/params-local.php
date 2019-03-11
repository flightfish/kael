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

    'qqemail_corpid'=>'wm3eb44ff1466ebc41',
    'qqemail_department'=>'4574112202817200851',//公司员工
    'qqemail_corpsecret_txl'=>'7eCI-TyawzhY8PSai5SyFX4rxqlU7ajzfPY57Q58fv4K0y07CcpGtzFFr_rq99OG',//通讯录
    'qqemail_corpsecret_sso'=>'96WZ1in_SmKUlFSeNGuXrO0OgkhdAxxLIHYIJH11L_CRxMjjmZR9a6E5gRiBOPmL',//单点登录
];
