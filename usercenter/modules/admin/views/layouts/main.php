<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2016/12/19
 * Time: 12:04
 */
use yii\helpers\Html;
$this->title = '人员管理';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title><?= Html::encode($this->title) ?></title>
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico">

    <?=Html::cssFile('@web/statics/css/bootstrap.min14ed.css')?>
    <?=Html::cssFile('@web/statics/css/font-awesome.min93e3.css')?>
    <?=Html::cssFile('@web/statics/css/animate.min.css')?>
    <?=Html::cssFile('@web/statics/css/style.min862f.css')?>
    <?=Html::cssFile('@web/statics/css/site.css')?>
    <?= Html::cssFile('@web/statics/css/plugins/bootstrap-table/bootstrap-table.min.css') ?>
    <?=Html::cssFile('@web/statics/js/plugins/showLoading/css/showLoading.css')?>
    <?=Html::jsFile('@web/statics/js/jquery.min.js')?>
    <?php $this->head() ?>
</head>
<body class="fixed-sidebar full-height-layout gray-bg">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
