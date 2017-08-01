<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2017/2/18
 * Time: 12:04
 */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = '人员管理';
?>
<div id="wrapper">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i>
        </div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" src="<?php echo Url::to('@web/statics/img/profile_small.jpg');?>" /></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold"><?= Html::encode($this->title) ?></strong></span>

                        </a>
                    </div>
                    <div class="logo-element">用户中心
                    </div>
                </li>
<!--                <li>-->
<!--                    <a class="J_menuItem" href="--><?//= Url::toRoute('/admin_bookstore/user/index')?><!--">试卷人员管理</a>-->
<!--                </li>-->
<!--                <li>-->
<!--                    <a class="J_menuItem" href="--><?//= Url::toRoute('/admin_qselect/user/index')?><!--">质量控制人员管理</a>-->
<!--                </li>-->
            </ul>
        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
        </div>
        <div class="row content-tabs">
            <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
            </button>
<!--            <nav class="page-tabs J_menuTabs">-->
<!--                <div class="page-tabs-content">-->
<!--                    <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">用户管理</a>-->
<!--                </div>-->
<!--            </nav>-->
            <a href="/" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
        </div>
        <div class="row J_mainContent" id="content-main">
            <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="" frameborder="0" data-id="index_v1.html" seamless></iframe>
        </div>
    </div>
    <!--右侧部分结束-->

</div>
<?=Html::jsFile('@web/statics/js/jquery.min.js')?>
<?=Html::jsFile('@web/statics/js/bootstrap.min.js')?>
<?=Html::jsFile('@web/statics/js/plugins/metisMenu/jquery.metisMenu.js')?>
<?=Html::jsFile('@web/statics/js/plugins/slimscroll/jquery.slimscroll.min.js')?>
<?=Html::jsFile('@web/statics/js/plugins/layer/layer.min.js')?>
<?=Html::jsFile('@web/statics/js/hplus.min.js')?>
<?=Html::jsFile('@web/statics/js/contabs.min.js')?>
<?=Html::jsFile('@web/statics/js/plugins/pace/pace.min.js')?>
<?php
$script = <<<SCRIPT
$(function () {     
    function getCookie(name)
    {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
        if(arr=document.cookie.match(reg))
        return unescape(arr[2]);
        else
        return null;
    }
    var token = getCookie('token');    
//    token='ow4JYZwIIFsOp8cC1g6jkd6x3U5flA1Wnd6 0nH /wdf gWH1erL9IypNye1q5oS';
    $.ajax({
        url: "/admin/welcome/list?token=" + token,
        type: "get",
        dataType: 'json',
        success: function (datas) {
            if (datas.code == 0) {
                var li = '';
                $.each(datas.data.list,function(i,v){
                    li += '<li><a class="J_menuItem" href="'+v.url+'" target="iframe0">'+v.name+'</a></li>';
                })
                $(li).appendTo('.nav');
            } else {
                swal(datas.code, datas.message, "error");
            }
        }
    })
})
SCRIPT;
$this->registerJs($script, yii\web\View::POS_END);
?>