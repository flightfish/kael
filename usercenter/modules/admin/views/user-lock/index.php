<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2016/10/21
 * Time: 12:04
 */
use yii\helpers\Html;

?>
<?= Html::cssFile('@web/statics/css/bootstrap.min14ed.css') ?>
<?= Html::cssFile('@web/statics/css/font-awesome.min93e3.css') ?>
<?= Html::cssFile('@web/statics/css/site.css') ?>
<?= Html::jsFile('@web/statics/js/jquery.min.js') ?>
<?= Html::cssFile('@web/statics/js/plugins/showLoading/css/showLoading.css') ?>
<?= Html::cssFile('@web/statics/css/plugins/sweetalert/sweetalert.css') ?>
<?= Html::cssFile('@web/statics/css/plugins/bootstrap-table/bootstrap-table.min.css') ?>
<?= Html::cssFile('@web/statics/css/animate.min.css') ?>
<?= Html::cssFile('@web/statics/css/style.min862f.css') ?>
<?= Html::cssFile('@web/statics/css/plugins/chosen/chosen.css') ?>
<?= Html::cssFile('@web/statics/css/plugins/datapicker/datepicker3.css') ?>
<div id="toolbar">
    <div style="width: 150%;">

        <div style="width: 150px;float: left;">
            <input id="filter-search" type="text" class="form-control" placeholder="搜索手机号">
        </div>
        <div style="float: left;">
            <button id="search-button" class="btn btn-info">搜索</button>
        </div>
    </div>

</div>
<div class="row">
    <div class="ibox-content">
        <!-- Example Events -->
        <div class="example">
            <table id="usertable">
            </table>
        </div>
        <!-- End Example Events -->
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?= Html::jsFile('@web/statics/js/jquery.min.js') ?>
<?= Html::jsFile('@web/statics/js/bootstrap.min.js') ?>
<?= Html::jsFile('@web/statics/js/content.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/datapicker/bootstrap-datepicker.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/jsKnob/jquery.knob.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/jasny/jasny-bootstrap.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/datapicker/bootstrap-datepicker.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/switchery/switchery.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/cropper/cropper.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/showLoading/js/jquery.showLoading.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/sweetalert/sweetalert.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/bootstrap-table/bootstrap-table.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js') ?>
<?= Html::jsFile('@web/statics/js/plugins/chosen/chosen.jquery.js') ?>
<?= Html::jsFile('@web/statics/js/demo/form-advanced-demo.min.js') ?>
<?php
$script = <<<SCRIPT
$(function () {
/**
 * Created by 李小雪 on 2017/3/17.
 */
    var data = {
        'userId': 0,
        'cityId': [],
    }
    
    $('.chosen-container').css('width','400px');
    $('.chosen-container-multi').css('width','400px');
    var listURL = "/admin/user-lock/lock-list";
    $("#search-button").on('click', function () {
        table()
        $('#usertable').bootstrapTable('refresh', {url: listURL});
    });
    function table() {
        var table = $('#usertable');
        table.bootstrapTable({
            url:listURL,
            dataType: "json",
            method:"post",
            pagination: true, //分页
            singleSelect: false,
            destroy: true,
            showRefresh:true,
            showToggle:true,
            queryParams:function(params){
                var temp = { 
                   pagesize: params.limit,   //页面大小
                   page: params.offset / params.limit + 1,  //页码
                   user_mobile: $("#filter-search").val(),
                };
                return temp;
            },
            search: true, //显示搜索框
            sidePagination: "server", //服务端处理分页
            columns: [
                {
                    field: 'id',
                    title: '序号'
                }, {
                    field: 'username',
                    title: '名字'
                }, {
                    field: 'mobile',
                    title: '电话号'
                }, {
                    field: 'email',
                    title: '邮箱'
                }, {
                    field: 'role',
                    title: '角色'
                }, {
                    field: 'status',
                    title: '状态'
                },
                {
                    title: '操作',
                    field: 'caozuo',
                    align: 'center',
                    formatter:function(value,row,index){
                        var mobile = row.mobile;
                        if(row.status=='正常'){
                            return '<button type="button" mobile="' + mobile + '" class="btn btn-primary btn-sm unlock" disabled="disabled">解锁</button>';
                        }else{
                            return '<button type="button" mobile="' + mobile + '" class="btn btn-primary btn-sm unlock">解锁</button>';
                        }
                    }
                }
            ]
        });
    }
    
    $("#usertable").on('click', '.unlock', function () {
        var mobile = $(this).attr('mobile');
        data.user_mobile = mobile;
        swal({
                title: "您确定要解锁这个用户吗？",
                text: "解锁后不可恢复，请谨慎操作！",
                type: "warning", showCancelButton: true,
                confirmButtonColor: "#dd6b55",
                confirmButtonText: "是的，我要确认通过！",
                cancelButtonText: "让我再考虑一下…",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "/admin/user-lock/unlock",
                        type: "post",
                        data: data,
                        success: function (datas) {
                            if (datas.code == 0) {
                                swal("成功！", "用户解锁成功。", "success");
                                $("button[name='refresh']").trigger('click');
                            } else {
                                swal("失败！", datas.message, "error");
                            }
                        }
                    })
                } else {
                    swal("已取消", "您取消了确定操作！", "error");
                }
            })
    })
});

SCRIPT;
$this->registerJs($script, yii\web\View::POS_END);
?>
