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
    <style type="text/css">
        .chosen-container{
            width: 100% !important;
        }
        .chosen-container-single .chosen-single{
            border-radius: 0 !important;
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
    <script>
        var urlParam = "?token="+$.cookie('token' )+ "&source=admin";
        var listURL = "/admin/resource/list"+urlParam;
        var editURL = "/admin/resource/edit"+urlParam;
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
    <button class="btn btn-info" data-toggle="modal" data-target="#editModal" data-whatever="0">添加</button>
</div>



<div class="modal inmodal fade" id="editModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">新建</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modid" value="">

                <div class="input-group">
                    <span class="input-group-addon" >资源名称</span>
                    <input type="text" id="resource_name" class="form-control" placeholder=""  >
                </div>
                <div class="input-group">
                    <span class="input-group-addon" >资源类型</span>
                    <select class="form-control" id="resource_type">
                        <option value="1" selected>插画成品</option>
                    </select>
                </div>
                <div class="input-group" id="resource_url-container">
                    <span class="input-group-addon" >图片</span>
                    <img id="resource_url" style="width: 100px;" alt="点击上传">
                </div>
                <script>
                    // 图片上传
                    Qiniu_resource_file = new QiniuJsSDK();
                    var uploader_resource_file = Qiniu_resource_file.uploader({
                        runtimes: 'html5,flash,html4',    //上传模式,依次退化
                        browse_button: 'resource_url',       //上传选择的点选按钮，**必需**
                        uptoken_url: '/common/qiniu/get-upload-token',            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
//         uptoken : '', //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
                        // unique_names: true, // 默认 false，key为文件名。若开启该选项，SDK为自动生成上传成功后的key（文件名）。
                        save_key: true,   // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK会忽略对key的处理
                        domain: 'http://7xohdn.com2.z0.glb.qiniucdn.com/',   //bucket 域名，下载资源时用到，**必需**
                        get_new_uptoken: true,  //设置上传文件的时候是否每次都重新获取新的token
                        container: 'resource_url-container',           //上传区域DOM ID，默认是browser_button的父元素，
                        max_file_size: '4mb',           //最大文件体积限制
                        flash_swf_url: 'js/plupload/Moxie.swf',  //引入flash,相对路径
                        max_retries: 3,                   //上传失败最大重试次数
                        dragdrop: true,                   //开启可拖曳上传
//        drop_element: 'upload-container',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
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
                                var domain = up.getOption('domain');
                                var res = $.parseJSON(info);
                                var sourceLink = domain + res.key; //获取上传成功后的文件的Url
                                $("#resource_url").attr('src',sourceLink);
                                $("#resource_url").attr('etag',res.hash);
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

                                var key = "";
                                // do something with key here
                                return key
                            }
                        }
                    });
                </script>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal" id="closebtn">关闭</button>
<!--                <button type="button" class="btn btn-primary" id="saveedit" onclick="edit(1)">修改原纪录</button>-->
                <button type="button" class="btn btn-success" id="saveeditnew" onclick="edit(0)">保存</button>
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
                field: 'resource_id',
                title: '编号',
                width: '2%',
            }, {
                field: 'resource_name',
                title: '图片名称',
                width: '20%'
            },{
                field: 'resource_type',
                title: '图片类型',
                width: '10%'
            },
            {
                field: 'resource_url',
                title: '图片',
                width: '20%',
                formatter:function(value,row,index){
                    return value ? '<img src="'+value+'" style="max-width: 100%;max-height: 100px;" etag="'+ row.qiniu_etag +'">' : "";
                }
            },
            {
                field: 'create_user',
                title: '用户',
                width: '15%'
            },
            {
                field: 'caozuo',
                title: '操作',
                width: '20%',
                formatter:function(value,row,index){
                    var id = row.resource_id;
                    tmpList[id] = row;
                    var btnhtml = '<div class="btn-group" role="group">'+
//                        '<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal" data-whatever="'+ id +'">编辑</button>'+
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
            url: editURL + urlParam,
            data:{
                id:id,
                data:{
                    Status:1
                },
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



    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var tmpid = button.data('whatever'); // Extract info from data-* attributes
        var modal = $(this);
        if(tmpid == 0){
            modal.find('.modal-title').text('新建');
            var row = {
                "resource_id": "0",
                "resource_name": "",
                "resource_type": 1,
                "qiniu_etag": "",
                "resource_url": "",
            };
        }else{
            modal.find('.modal-title').text('编辑');
            var row = tmpList[tmpid];
        }
        modal.find('#modid').val(row.resource_id);
        modal.find('#resource_name').val(row.resource_name);
        modal.find('#resource_type').val(row.resource_type);
        modal.find('#resource_url').attr('src',row.resource_url);
    });

    function edit(is_old){
        var id = is_old ? $('#modid').val() : 0;
        var apiurl = editURL;
        $.ajax({
            type:'post',
            url: apiurl + urlParam,
            data:{
                id:id,
                data:{
                    "resource_name": $('#resource_name').val(),
                    "resource_type": $('#resource_type').val(),
                    "qiniu_etag": $('#resource_url').attr('etag'),
                    "resource_url": $('#resource_url').attr('src'),
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


</script>

</body>

</html>
