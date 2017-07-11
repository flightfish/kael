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
<form>
    <input name="qid" type="text" placeholder="输入题目id">
    <input type="submit" value="搜索">
</form>
<div>题目ID：<?php echo $question_id;?></div>
<table class="table">
    <tr>
        <td>图片名称</td>
        <td>图片</td>
    </tr>
    <?php foreach($list as $v) : ?>
        <tr>
            <td><?php echo $v['resource_name'];?></td>
            <td><img src="<?php echo $v['resource_url'];?>" style="max-height: 100px;"></td>
        </tr>
    <?php endforeach;?>
</table>
</body>