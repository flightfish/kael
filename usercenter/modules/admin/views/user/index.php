<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="/statics/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/statics/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/statics/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/statics/css/animate.min.css" rel="stylesheet">
    <link href="/statics/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link href="/statics/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/statics/css/plugins/dropzone/basic.css" rel="stylesheet">
    <link href="/statics/css/plugins/dropzone/dropzone.css" rel="stylesheet">
    <link href="/statics/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style type="text/css">
        .chosen-container{
            width: 100% !important;
        }
        .chosen-container-single .chosen-single{
            border-radius: 0 !important;
        }
        #my-awesome-dropzone{
            /*width: 55%;*/
            /*min-height: 190px;*/
            /*margin-left: 3%;*/
        }
        .numdown{
            display: inline-block;
        }
        .fixed-table-toolbar .bars, .fixed-table-toolbar .columns, .fixed-table-toolbar .search{
            /*line-height: 15px;*/
        }
        html,body{
            width:100%;
            height:100%;
        }
    </style>

    <script src="/statics/js/jquery.min.js?v=2.1.4"></script>
    <script src="/statics/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/statics/js/jquery.cookie.min.js?v=1.4.1"></script>
    <script src="/statics/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="/statics/qiniu/qiniu.min.js?v=1.0.16"></script>
    <script type="text/javascript" src="/statics/qiniu/moxie.min.js"></script>
    <script type="text/javascript" src="/statics/qiniu/plupload.full.min.js"></script>
    <script src="/statics/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
    <script src="/statics/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
    <script src="/statics/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/statics/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/statics/js/plugins/dropzone/dropzone.js"></script>
    <script src="/statics/js/plugins/sweetalert/sweetalert.min.js"></script>
    <script>
        var urlParam = "?token="+$.cookie('token' )+ "&source=admin";
        var platformType = '';
        var listURL = "/admin/user/list"+urlParam;
        var editURL = "/admin/user/edit"+urlParam;
        var delURL = "/admin/user/del"+urlParam;
        var checkmobileURL = "/admin/user/check-mobile"+urlParam;
        var downloadURL = "/admin/user/download"+urlParam;
        var uploadURL = "/admin/user/upload"+urlParam;

    </script>
</head>

<body>
<div class="col-sm-12">
    <div class="example-wrap">
        <table id="mytable" >

        </table>
    </div>
</div>


<div id="toolbar">

    <div style="width: 100%;">
        <div style="float: left;">
            <button class="btn btn-info" data-toggle="modal" data-target="#editModal" data-whatever="0">添加</button>
            <button type="button" class="btn btn-primary addmany" data-toggle="modal" data-target="#batchAdd">
                批量新增
            </button>
        </div>
        <div style="width: 200px;float: left;">
            <select id="filter-role" value="1" class="form-control">
                <option value="0">全部权限</option>
                <?php foreach($platformList as $v): ?>
                    <option value="<?php echo $v['platform_id']; ?>"><?php echo $v['platform_name'];?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div style="width: 200px;float: left;">
            <input id="filter-search" type="text"  class="form-control" placeholder="搜索手机号或用户名">
        </div>
        <div style="width: 100px;float: left;">
            <input id="filter-subject" type="text"  class="form-control" placeholder="学科">
        </div>
        <div style="width: 100px;float: left;">
            <input id="filter-grade" type="text"  class="form-control" placeholder="学段">
        </div>
        <div style="float: left;">
            <button id="search-button" class="btn btn-info">搜索</button>
        </div>
    </div>

</div>


<div class="modal inmodal fade" id="batchAdd" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">批量新增</h4>
                <!--                                <small class="font-bold">这里可以显示副标题。-->
            </div>
            <div class="modal-body">
                <div class="ibox-content">
                    <div class="input-group"><span class="input-group-addon">第一步：检查手机号</span></div>
                    <form id="download-form" action="" method="post" class="col-xl-12" >
                        <div class="input-group">
                            <span class="input-group-addon">手机号列表</span>
<!--                            <input type="text" id="mobile" class="form-control" placeholder=""  >-->
                            <textarea class="form-control "  name="mobile" id="" cols="30" rows="10"></textarea>
                            <input type="hidden" name="type" id="type">
                        </div>
                        <button type="submit" class="btn btn-primary download col-xs-12">
                            下载格式模版
                        </button>
                    </form>
<!--                    <form action="" method="post" class="input-group numdown" style="width: 30%">-->
<!--                        <span class="input-group-addon" style="display: block;">电话号码</span>-->
<!--                        <textarea name="mobile" id="mobile" cols="30" rows="10"></textarea>-->
<!--                        <input type="hidden" name="type" id="type">-->
<!--                        <button type="submit" class="btn btn-primary download">-->
<!--                            下载格式模版-->
<!--                        </button>-->
<!--                    </form>-->
                    <div class="input-group"><span class="input-group-addon">第二步：修改后上传</span></div>
                    <form id="my-awesome-dropzone" class="dropzone"  action="">
                        <div class="dropzone-previews"></div>
                        <button type="submit" class="btn btn-primary pull-right">上传</button>
                        <input type="hidden" name="type" id="type1">
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white closefile" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>



<div class="modal inmodal fade" id="editModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modal-title">新建</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modid" value="">

                <div class="input-group">
                    <span class="input-group-addon">手机号</span>
                    <input type="text" id="mobile" class="form-control" placeholder=""  >
                </div>

                <div id="group-container" style="display: none">
                    <div class="input-group">
                        <span class="input-group-addon" >用户名</span>
                        <input type="text" id="username" class="form-control" placeholder="">
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon" >身份证</span>
                        <input type="text" id="idcard" class="form-control" placeholder=""  >
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon" >性别</span>
                        <div class="form-control">
                            <input type="radio" name="sex" value="1"/>男
                            <input type="radio" name="sex" value="2"/>女
                        </div>
                    </div>

                    <?php //if(in_array($type,['qselect','qualitysys'])):?>
                        <div class="input-group">
                            <span class="input-group-addon" >学科</span>
                            <input type="text" id="subject" class="form-control" placeholder=""  >
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon" >学段</span>
                            <input type="text" id="grade_part" class="form-control" placeholder=""  >
                        </div>
                    <?php //endif;?>

                    <div class="input-group">
                        <span class="input-group-addon" >银行名称</span>
                        <input type="text" id="bank_name" class="form-control" placeholder=""  >
                    </div>


                    <div class="input-group">
                        <span class="input-group-addon" >银行分行</span>
                        <input type="text" id="bank_deposit" class="form-control" placeholder=""  >
                    </div>


                    <div class="input-group">
                        <span class="input-group-addon" >银行地区</span>
                        <input type="text" id="bank_area" class="form-control" placeholder=""  >
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon" >银行帐号</span>
                        <input type="text" id="bank_account" class="form-control" placeholder=""  >
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon" >权限</span>
                        <div class="form-control">
                            <?php foreach($platformList as $v): ?>
                                <input type="checkbox" name="role_list" value="<?php echo $v['role_id']?>"/><?php echo $v['name'];?>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal" id="closebtn">关闭</button>
                <button type="button" class="btn btn-primary" id="saveedit" onclick="edit(1)">保存</button>
                <button type="button" class="btn btn-primary" id="checkmobile" onclick="checkmobile()">检查手机</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    //批量上传开始
    $('#type').attr('value',platformType);
    $('#type1').attr('value',platformType);
    $('#download-form').attr('action',downloadURL);
    $('#my-awesome-dropzone').attr('action',uploadURL);
    $(".chosen-select").chosen();
    zone();
    $('#batchAdd .modal-footer .closefile').click(function(){
        zone();
    });
    $('#batchAdd .close').click(function(){
        zone();
    });
    function zone(){
        $('.dz-preview').remove();
        Dropzone.options.myAwesomeDropzone = {
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 100,
            init: function () {
                var myDropzone = this;
                this.element.querySelector("button[type=submit]").addEventListener("click", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    myDropzone.processQueue()
                });
                this.on("sendingmultiple", function () {
                });
                this.on("successmultiple", function (files, response) {
                    if (response.code != 0) {
                        swal("上传失败！", response.message, "error");
                    } else {
                        swal("上传成功！", response.data, "success");
                        $('#batchAdd .modal-footer .closefile').trigger("click");
                        zone();
                        table();
                    }
                });
                this.on("errormultiple", function (files, response) {
                })
            }
        }
    }
    if(platformType == "entrystore"){
        $('.addmany').remove();
    }
    //批量上传结束
    var maxheight = $('body').height();
    var tmpList = [];
    $("#mytable").bootstrapTable({
        url:listURL,     //请求后台的URL（*）
        method: 'post',           //请求方式（*）
        toolbar: '#toolbar',        //工具按钮用哪个容器
        pagination: true,          //是否显示分页（*）
        sidePagination: "server",      //分页方式：client客户端分页，server服务端分页（*）
        pageNumber:1,            //初始化加载第一页，默认第一页
        pageSize: 10,            //每页的记录行数（*）
        pageList: [10,20,50],    //可供选择的每页的行数（*）
        clickToSelect:false,        //是否启用点击选中行
        height: maxheight,         //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
        uniqueId: "resource_id",           //每一行的唯一标识，一般为主键列
        cardView: false,          //是否显示详细视图
        detailView: false,          //是否显示父子表
        showHeader:true,
        search: false,//是否显示右上角的搜索框
        checkboxHeader:true,
        showFooter:false,
        undefinedText:"",
        selectItemName:"select",
        showToggle: true,   //名片格式
        showColumns: true, //显示隐藏列
        showRefresh: true,  //显示刷新按钮
        singleSelect: false,//复选框只能选择一条记录
        queryParams: queryParams, //参数
        queryParamsType: "limit", //参数格式,发送标准的RESTFul类型的参数请求
        silent: true,  //刷新事件必须设置
        smartDisplay: true, // 智能显示 pagination 和 cardview 等
        formatLoadingMessage: function () {
            return "请稍等，正在加载中...";
        },
        formatNoMatches: function () {  //没有匹配的结果
            return '无符合条件的记录';
        },
        columns: [
            {
                field: 'user_id',
                title: '用户ID',
                width: '2%',
            }, {
                field: 'username',
                title: '用户名',
                width: '10%'
            },{
                field: 'mobile',
                title: '手机号码',
                width: '10%'
            },
            {
                field: 'sex',
                title: '性别',
                width: '5%',
                formatter:function(value,row,index){
                    if(parseInt(value) == 1){
                        return "男";
                    }else{
                        return "女";
                    }
                }
            },
            {
                field: 'priv',
                title: '用户角色',
                width: '10%'
            },
            {
                field: 'idcard',
                title: '身份证号码',
                width: '10%'
            },
            <?php //if(in_array($type,['qselect','qualitysys'])): ?>
            {
                field: 'subject',
                title: '学科',
                width: '5%'
            },
            {
                field: 'grade_part',
                title: '学段',
                width: '5%'
            },
            <?php //endif; ?>
            {
                field: 'create_time',
                title: '添加时间',
                width: '15%'
            },
            {
                field: 'update_time',
                title: '修改时间',
                width: '15%'
            },
            {
                field: 'caozuo',
                title: '操作',
                width: '20%',
                formatter:function(value,row,index){
                    var id = row.user_id;
                    tmpList[id] = row;
                    var btnhtml = '<div class="btn-group" role="group">'+
                        '<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal" data-whatever="'+ id +'">编辑</button>'+
                        '<button class="btn btn-danger btn-sm" onclick="setdel('+ id +')">删除</button>'+
                        '</div>';
                    return btnhtml;
                }
            } ],
        responseHandler:function(res) {
            top.$("#spiner").hide();
            ret =  res.data;
            ret.page = ret.page;
            ret.total = ret.total;
            ret.rows = ret.list;
            return ret;
        },
    });


    function queryParams(params) {  //配置参数
        top.$("#spiner").show();
        var temp = {   //这里的键的名字和控制器的变量名必须一直，这边改动，控制器也需要改成一样的
            pagesize: params.limit,   //页面大小
            page: params.offset/params.limit + 1,  //页码
            type: platformType,
            filter:{
                role:$("#filter-role").val(),
                search:$("#filter-search").val(),
                subject:$("#filter-subject").val(),
                grade:$("#filter-grade").val()
            }
        };
        return temp;
    }

    function search(){
        $('#mytable').bootstrapTable({pageNumber:1,pageSize:10});
    }

    function setdel(id){
        ret = confirm("是否确认【删除】");
        if(!ret){
            return "";
        }
        $.ajax({
            type:'post',
            url: delURL,
            data:{
                id:id,
                type:platformType,
            },
            success:function(data){
                if(data.code== 0){
                    $("#mytable").bootstrapTable("refresh");
                }else{
                    alert(data.message);
                }
            }
        });
    }

    $("#mobile").on('change',function(){
        if($('#modal-title').html() == '新建'){
            $("#checkmobile").show();
            $("#group-container").hide();
            $("#saveedit").hide();
            $('#username').val("");
            $('#idcard').val("");
            $("input[name='sex'][value='"+ 1 +"']").attr("checked", true);
            $('#bank_name').val("");
            $('#bank_deposit').val("");
            $('#bank_area').val("");
            $('#bank_account').val("");
            $('input[name="rolelist"]').attr("checked", false);
        }
    });

    function checkmobile(){
        $.ajax({
            type:'post',
            url: checkmobileURL,
            data:{
                mobile:$("#mobile").val(),
                type:platformType
            },
            success:function(data){
                if(data.code==0){
                    $("#saveedit").show();
                    $("#checkmobile").hide();
                    $("#group-container").show();
                    var row = data.data;
                    if(row){
                        $('#username').val(row.username);
                        $('#mobile').val(row.mobile);
                        $('#idcard').val(row.idcard);
                        $("input[name='sex'][value='"+ row.sex +"']").attr("checked", true);
                        $('#bank_name').val(row.bank_name);
                        $('#bank_deposit').val(row.bank_deposit);
                        $('#bank_area').val(row.bank_area);
                        $('#bank_account').val(row.bank_account);
                        $('input[name="rolelist"]').attr("checked", false);
                        $("#subject").val(row.subject);
                        $("#grade_part").val(row.grade_part);
                    }
                }else{
                    alert(data.message);
                }
            }
        });
    }


    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var tmpid = button.data('whatever'); // Extract info from data-* attributes
        var modal = $(this);
        if(tmpid == 0){
            $("#group-container").hide();
            $("#saveedit").hide();
            $("#checkmobile").show();
            modal.find('.modal-title').text('新建');
            var row = {
                "user_id": "0",
                "status": "0",
                "create_time": "",
                "update_time": "",
                "username": "",
                "mobile": "",
                "idcard": "",
                "sex": "1",
                "bank_name": "",
                "bank_deposit": "",
                "bank_area": "",
                "bank_account": "",
                "priv": "",
                "role_list": []
            };
        }else{
            $("#group-container").show();
            $("#checkmobile").hide();
            modal.find('.modal-title').text('编辑');
            var row = tmpList[tmpid];
        }
        modal.find('#modid').val(row.user_id);
        modal.find('#username').val(row.username);
        modal.find('#mobile').val(row.mobile);
        modal.find('#idcard').val(row.idcard);
//        modal.find('#sex').val(row.sex);
        $("input[name='sex'][value='"+ row.sex +"']").attr("checked", true);
        modal.find('#bank_name').val(row.bank_name);
        modal.find('#bank_deposit').val(row.bank_deposit);
        modal.find('#bank_area').val(row.bank_area);
        modal.find('#bank_account').val(row.bank_account);
        modal.find('#subject').val(row.subject);
        modal.find('#grade_part').val(row.grade_part);

        $('input[name="rolelist"]').attr("checked", false);
        for(var i=0;i<row.role_list.length;++i){
            $("input[name=role_list][value="+ row.role_list[i] +"]").attr("checked", true);
        }
    });

    function edit(is_old){
        var id = is_old ? $('#modid').val() : 0;
        var role_check = $('input[name="role_list"]:checked');
        var roles = [];
        $.each(role_check, function () {
            var roleid = $(this).val()
            roles.push(roleid);
        });
        $.ajax({
            type:'post',
            url: editURL,
            data:{
                type:platformType,
                id:id,
                data:{
                    "center":{
                        "username": $('#username').val(),
                        "mobile": $('#mobile').val(),
                        "idcard": $('#idcard').val(),
                        "sex": $('input[name="sex"]:checked').val(),
                        "bank_name": $('#bank_name').val(),
                        "bank_deposit": $('#bank_deposit').val(),
                        "bank_area": $('#bank_area').val(),
                        "bank_account": $('#bank_account').val(),
                        "subject": $('#subject').val(),
                        "grade_part": $('#grade_part').val(),
                    },
                    "current":{
                        <?php //if(in_array($type,['qselect','qualitysys'])):?>
//                            "subject": $('#subject').val(),
//                            "grade_part": $('#grade_part').val(),
                        <?php //endif;?>
                    },
                    "role_list": roles,
                }
            },
            success:function(data){
                if(data.code==0){
                    alert("操作成功");
                    $("#closebtn").click();
                    $("#mytable").bootstrapTable("refresh");
                }else{
                    alert(data.message);
                }
            }
        });
    };

    $("#search-button").on('click',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

    $("#filter-role").on('change',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    })

</script>

</body>

</html>
