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
        var listURL = "/admin/department/list"+urlParam;
        var editURL = "/admin/department/edit"+urlParam;
        var delURL = "/admin/department/del"+urlParam;
        var platURL = "/admin/user/platform-by-department-admin" + urlParam;
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

        <div style="width: 300px;float: left;">
            <input id="filter-search" type="text"  class="form-control" placeholder="搜索部门名称">
        </div>
        <div style="float: left;">
            <button id="search-button" class="btn btn-info">搜索</button>
        </div>
    </div>

</div>


<div class="modal inmodal fade" id="editAdminModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modal-title">平台管理员设置</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modid" value="">

                <div class="input-group">
                    <span class="input-group-addon" >新增/修改管理员列表</span>
                    <select id="admin_user" value="-1" class="form-control" onchange="updatePlatCheck()">
                        <option value="-1">请选择部门管理员</option>
                        <?php foreach($adminList as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['username'];?></option>
                        <?php endforeach;?>
                    </select>
                </div>

                <div class="input-group">
                    <span class="input-group-addon" >平台权限</span>
                    <div class="form-control" id="platform_list_container">

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal" id="closebtn">关闭</button>
                <button type="button" class="btn btn-primary" id="saveedit" onclick="edit(1)">保存当前管理员配置</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(".chosen-select").chosen();

    var maxheight = $('body').height();
    var tmpList = [];
    $("#mytable").bootstrapTable({
        url:listURL,     //请求后台的URL（*）
        method: 'post',           //请求方式（*）
        toolbar: '#toolbar',        //工具按钮用哪个容器
        pagination: true,          //是否显示分页（*）
        sidePagination: "server",      //分页方式：client客户端分页，server服务端分页（*）
        pageNumber:1,            //初始化加载第一页，默认第一页
        pageSize: 1000,            //每页的记录行数（*）
        pageList: [1000],    //可供选择的每页的行数（*）
        clickToSelect:false,        //是否启用点击选中行
        height: maxheight,         //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
        uniqueId: "id",           //每一行的唯一标识，一般为主键列
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
                field: 'department_id',
                title: '部门ID',
            }, {
                field: 'department_name',
                title: '部门名称',
            },{
                field: 'is_outer',
                title: '部门类型',
                formatter:function(value,row,index){
                    if(parseInt(value) == 0){
                        return "公司内部";
                    }else{
                        return "外包";
                    }
                }
            },{
                field: 'platform_list',
                title: '平台权限',
                width:"30%",
                formatter:function(value,row,index){
                    var name = "";
                    for(var i in value){
                        if(name){
                            name += "<br/>";
                        }
                        name = name + value[i].platform_name
                    }
                    return name;
                }
            },{
                field: 'admin_list',
                title: '部门管理员',
                width:"30%",
                formatter:function(value,row,index){
                    var name = "";
                    for(var i in value){
                        if(name){
                            name += "<br/>";
                        }
                        name = name + value[i].username + '(';
                        for(var j in value[i]['platform_list']){
                            if(j>0){
                                name += ',';
                            }
                            name += value[i]['platform_list'][j].platform_name;
                        }
                        name += ')';
                    }
                    return name;
                }
            },

            {
                field: 'caozuo',
                title: '操作',
                width: '20%',
                formatter:function(value,row,index){
                    var id = row.department_id;
                    tmpList[id] = row;
                    var btnhtml = '<div class="btn-group" role="group">'+
                        '<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editAdminModal" data-whatever="'+ id +'">编辑</button>'+
                        '<button class="btn btn-danger btn-sm" onclick="setdel('+ id +')">删除</button>'+
                        '</div>';
                    return btnhtml;
                }
            } ],
        responseHandler:function(res) {
            top.$("#spiner").hide();
            ret =  res.data;
            if(res.code != 0){
                this.formatNoMatches = function(){
                    return res.message;
                }
            }else{
                this.formatNoMatches = function () {  //没有匹配的结果
                    return '无符合条件的记录';
                };
            }
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
            filter:{
                role:$("#filter-role").val(),
                department:$("#filter-department").val(),
                platform:$("#filter-platform").val(),
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


    function updatePlatCheck(){
        let departmentId = $("#modid").val();
        let adminId = $("#admin_user").val();
        $("input[name='platform_list']").prop("checked", false);
        for(let i in tmpList[departmentId]['admin_list']){
            if(parseInt(tmpList[departmentId]['admin_list'][i]['id']) != parseInt(adminId)){
                continue;
            }
            for(let j in tmpList[departmentId]['admin_list'][i]['platform_list']){
                $("input[name='platform_list'][value='"+ tmpList[departmentId]['admin_list'][i]['platform_list'][j]['platform_id'] +"']").prop("checked", true);
            }
        }


    }



    $('#editAdminModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var tmpid = button.data('whatever'); // Extract info from data-* attributes
        var modal = $(this);
        var row = tmpList[tmpid];
        modal.find('#modid').val(tmpid);
        platfromListByDepart(tmpid);
        $("#admin_user").val(-1);
    });

    function edit(is_old){
        var id = is_old ? $('#modid').val() : 0;
        var platform_list_checked = $('input[name="platform_list"]:checked');
        var platform_list = [];
        $.each(platform_list_checked, function () {
            var platform_id = $(this).val()
            platform_list.push(platform_id);
        });
        $.ajax({
            type:'post',
            url: editURL,
            data:{
                department_id:id,
                user_id:$("#admin_user").val(),
                platform_list: platform_list,
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

    function platfromListByDepart(department_id){
        var adminList = tmpList[department_id]['admin_list'];
        $.ajax({
            type:'post',
            url: platURL,
            data:{
                department_id: department_id
            },
            success:function(data){
                if(data.code==0){
                    //clear
                    $("#platform_list_container").html("");
                    let html = "";
                    for(var i in data.data){
                        html +=  '<input type="checkbox" name="platform_list" value="'+ data.data[i].platform_id +'"/>' + data.data[i].platform_name;
                    }
                    $("#platform_list_container").html(html);

                }else{
                    alert(data.message);
                }
            }
        });
    }

    $("#search-button").on('click',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

    $("#filter-role").on('change',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

    $("#filter-department").on('change',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

    $("#filter-platform").on('change',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

</script>

</body>

</html>
