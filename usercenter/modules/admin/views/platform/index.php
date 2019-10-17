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
    <script src="/statics/qiniu/qiniu.min.js"></script>
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
        var listURL = "/admin/platform/list"+urlParam;
        var editURL = "/admin/platform/edit"+urlParam;
        var addUrl = "/admin/platform/add"+urlParam;
        var delURL = "/admin/platform/del"+urlParam;
        var qiniuTokenUrl = "/admin/platform/qiniu-token"+urlParam;
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

        <div style="width: 100px;float: left;">
            <select id="filter-envtype" value="-1" class="form-control">
                <option value="-1">全部</option>
                <option value="1">线上</option>
                <option value="2">预览</option>
            </select>
        </div>

        <div style="width: 300px;float: left;">
            <input id="filter-platformname" type="text"  class="form-control" placeholder="应用名称">
        </div>
        <div style="float: left;">
            <button id="search-button" class="btn btn-info">搜索</button>
        </div>
        <div style="float: left;">
            <button class="btn btn-success" data-toggle="modal" data-target="#editModal" data-whatever="0">新建应用</button>
        </div>

    </div>

</div>


<div class="modal inmodal fade" id="editModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modal-title2">编辑</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modid-platform" value="">

                <div class="input-group">
                    <span class="input-group-addon" >应用名称</span>
                    <input id="platform_name" value="" class="form-control"/>
                </div>
                <div class="input-group" id="file_icon_parent">
                    <span class="input-group-addon" >应用图标</span>
<!--                    <input type="file" id="file_icon" class="form-control" value=""/>-->
                    <div  id="file_icon" class="form-control" value="form-control">点击上传</div>
                </div>
                <img src="" id="platform_icon" style="width: 270px;height:140px;">
                <div class="input-group">
                    <span class="input-group-addon" >应用链接</span>
                    <input id="platform_url" value="" class="form-control"/>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" >应用类型</span>
                    <select id="env_type" value="-1" class="form-control">
                        <option value="-1">请选择</option>
                        <option value="1">线上</option>
                        <option value="2">预览</option>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" >外网访问</span>
                    <select id="ip_limit" value="-1" class="form-control">
                        <option value="-1">请选择</option>
                        <option value="1">仅限内网访问</option>
                        <option value="0">不限</option>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" >展示卡片</span>
                    <select id="is_show" value="-1" class="form-control">
                        <option value="-1">请选择</option>
                        <option value="1">展示卡片</option>
                        <option value="0">不展示(仅有APP/无网页)</option>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" >负责人ID</span>
                    <input id="admin_user" value="0" class="form-control"/>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal" id="closebtn">关闭</button>
                <button type="button" class="btn btn-primary" id="saveedit-department" onclick="editPlatform">保存</button>
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
        pageSize: 10,            //每页的记录行数（*）
        pageList: [10,20,50],    //可供选择的每页的行数（*）
        clickToSelect:false,        //是否启用点击选中行
        height: maxheight,         //行高，如果没有设置height属性，表格自动根据记录条数觉得表格高度
        uniqueId: "platform_id",           //每一行的唯一标识，一般为主键列
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
                field: 'platform_id',
                title: '应用ID',
            }, {
                field: 'platform_name',
                title: '应用名称',
            },{
                field: 'platform_icon',
                title: '应用图标',
                formatter:function(value,row,index){
                    if(value != ''){
                        return '<img src=' + value + ' style="width:270px;height=140px;">';
                    }else{
                        return '未配置';
                    }

                }
            },{
                field: 'platform_url',
                title: '应用链接',
            },{
                field: 'env_type',
                title: '应用类型',
                formatter:function(value,row,index){
                    if(parseInt(value) == 1){
                        return "线上";
                    }else if(parseInt(value) == 2){
                        return "预览";
                    }else{
                        return "未设置";
                    }
                }
            },{
                field: 'admin_user_name',
                title: '负责人',
            },{
                field: 'admin_user_department',
                title: '负责人部门',
            },{
                field: 'ip_limit',
                title: '外网访问',
                formatter:function(value,row,index){
                    if(value == 0){
                        return "不限";
                    }else{
                        return "限内网";
                    }
                }
            },
            {
                field: 'caozuo',
                title: '操作',
                width: '20%',
                formatter:function(value,row,index){
                    var id = row.platform_id;
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
            env_type:$("#filter-envtype").val(),
            platform_name:$("#filter-platformname").val(),
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
                platform_id:id,
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


    $('#editModal').on('hidden.bs.modal', function (event) {
        $("#mytable").bootstrapTable("refresh");
    });

    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var tmpid = button.data('whatever'); // Extract info from data-* attributes
        var modal = $(this);
        if(tmpList[tmpid]){
            $("#platform_icon").attr('src',tmpList[tmpid]['platform_icon']);
            $("#platform_name").val(tmpList[tmpid]['platform_name']);
            $("#platform_url").val(tmpList[tmpid]['platform_url']);
            $("#admin_user").val(tmpList[tmpid]['admin_user']);
            $("#modal-title2").html("编辑");
            $("#ip_limit").val(tmpList[tmpid]['ip_limit']);
        }else{
            $("#platform_icon").attr('src','');
            $("#platform_name").val('');
            $("#platform_url").val('');
            $("#admin_user").val(0);
            $("#modal-title2").html("新建");
            $("#ip_limit").val(-1);
        }
        $("#modid-platform").val(tmpid);
    });

    function editPlatform(){
        var id = $('#modid-platform').val() || 0;
        if(!$("#platform_name").val()){
            alert("请填写应用名称");
            return false;
        }
        if(!$("#platform_icon").val()){
            alert("请上传应用图标");
            return false;
        }
        if(!$("#platform_url").val()){
            alert("请填写应用链接");
            return false;
        }
        if(!$("#env_type").val() == -1){
            alert("请选择应用类型");
            return false;
        }
        if(!$("#ip_limit").val() == -1){
            alert("请选择IP限制");
            return false;
        }
        if(!$("#is_show").val() == -1){
            alert("请选择是否展示卡片");
            return false;
        }
        let data = {
            platform_id:id,
            platform_name:$("#platform_name").val(),
            platform_url:$("#platform_url").val(),
            admin_user:$("#admin_user").val(),
            is_show:$("#is_show").val(),
            platform_icon:$("#platform_icon").attr('src'),
            env_type:$("#env_type").val(),
            ip_limit:$("#ip_limit").val(),
        };
        if($("#modal-title2").html() == '编辑'){
            $.ajax({
                type:'post',
                url: editURL,
                data:data,
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
        }else{
            $.ajax({
                type:'post',
                url: addUrl,
                data:data,
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
        }

    };



    $("#search-button").on('click',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });


    $("#filter-platform").on('change',function(){
        $('#mytable').bootstrapTable('refresh',{url:listURL});
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $("#file_icon").on('change',function(e){
        let filepath = $('#file_icon').val();
        $.ajax({
            type:'post',
            url: qiniuTokenUrl,
            data:data,
            success:function(data){
                if(data.code==0){
                    let qiniutoken = dats.data;
                    var observable = qiniu.upload(file, key, token, putExtra, config)
                    console.log(qiniutoken)
                }else{
                    alert(data.message);
                }
            }
        });
    });

    //qiniu
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',    //上传模式,依次退化
        browse_button: 'file_icon',       //上传选择的点选按钮，**必需**
        uptoken_url: qiniuTokenUrl,            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
        // uptoken : '<Your upload token>', //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
        // unique_names: true, // 默认 false，key为文件名。若开启该选项，SDK为自动生成上传成功后的key（文件名）。
        // save_key: true,   // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK会忽略对key的处理
        domain: 'https://innerplatformqiniu.knowbox.cn/o_1dnbt88b59r3scvlev5kma55a.png/',   //bucket 域名，下载资源时用到，**必需**
        get_new_uptoken: false,  //设置上传文件的时候是否每次都重新获取新的token
        container: 'file_icon_parent',           //上传区域DOM ID，默认是browser_button的父元素，
        max_file_size: '4mb',           //最大文件体积限制
        flash_swf_url: 'js/plupload/Moxie.swf',  //引入flash,相对路径
        max_retries: 3,                   //上传失败最大重试次数
        dragdrop: true,                   //开启可拖曳上传
        drop_element: 'file_icon_parent',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
        chunk_size: '4mb',                //分块上传时，每片的体积
        auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
        init: {
            'FilesAdded': function(up, files) {
                plupload.each(files, function(file) {
                    // 文件添加进队列后,处理相关的事情
                });
            },
            'BeforeUpload': function(up, file) {
                // 每个文件上传前,处理相关的事情
            },
            'UploadProgress': function(up, file) {
                // 每个文件上传时,处理相关的事情
            },
            'FileUploaded': function(up, file, info) {
                // 每个文件上传成功后,处理相关的事情
                // 其中 info 是文件上传成功后，服务端返回的json，形式如
                // {
                //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                //    "key": "gogopher.jpg"
                //  }
                // 参考http://developer.qiniu.com/docs/v6/api/overview/up/response/simple-response.html

                console.log(info)
                var domain = up.getOption('domain');
                var res = info.parseJSON();
                var sourceLink = domain + res.key; //获取上传成功后的文件的Url
                $("#platform_icon").attr('src',sourceLink)
            },
            'Error': function(up, err, errTip) {
                //上传出错时,处理相关的事情
            },
            'UploadComplete': function() {
                //队列文件处理完毕后,处理相关的事情
            },
            'Key': function(up, file) {
                // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                // 该配置必须要在 unique_names: false , save_key: false 时才生效
                let key = "iconkael/"+ (+new Date())+"_"+file.name;
                // do something with key here
                return key
            }
        }
    });



</script>

</body>

</html>
