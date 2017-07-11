var Common = function () {
    return{
         inithichart : function () {
            $('.form-group:not("#enter,.subject ") .btn-group').on('click', 'button', function () {
                $(this).removeClass('btn-white');
                $(this).addClass('btn-primary').siblings().removeClass('btn-primary').addClass('btn-white');
                $(this).parent().prev().removeClass('btn-primary').addClass('btn-white');
            })

            $('.form-group:not("#enter,.subject") .all').click(function () {
                $(this).addClass('btn-primary').removeClass('btn-white');
                $(this).next().find('.btn-primary').addClass('btn-white');
                $(this).next().find('button').removeClass('btn-primary');
            })
            $('.subject .all').click(function () {
                if ($(this).hasClass("btn-white")) {
                    $(this).next().find('button').addClass('btn-white').removeClass('btn-primary');
                    $(this).addClass('btn-primary').removeClass('btn-white');
                } else {
                    $(this).next().find('button').addClass('btn-primary').removeClass('btn-white');
                    $(this).addClass('btn-white').removeClass('btn-primary');
                }
            })
            $('#enter .all').click(function () {
                if ($(this).hasClass("btn-white")) {
                    $(this).next().find('button').addClass('btn-white').removeClass('btn-primary');
                    $(this).addClass('btn-primary').removeClass('btn-white');
                } else {
                    $(this).next().find('button').addClass('btn-primary').removeClass('btn-white');
                    $(this).addClass('btn-white').removeClass('btn-primary');
                }
            })

            $('.subject .btn-group').on('click', 'button', function () {
                var that = $(this);
                if ($(this).hasClass("btn-white")) {
                    $(this).addClass('btn-primary').removeClass('btn-white');
                    $(this).parent().prev().removeClass('btn-primary').addClass('btn-white');
                } else {
                    $(this).addClass('btn-white').removeClass('btn-primary');
                }
            })
            $('#enter .btn-group').on('click', 'button', function () {
                var that = $(this);
                if ($(this).hasClass("btn-white")) {
                    $(this).addClass('btn-primary').removeClass('btn-white');
                    $(this).parent().prev().removeClass('btn-primary').addClass('btn-white');
                } else {
                    $(this).addClass('btn-white').removeClass('btn-primary');
                }
            })
            $('.type button').click(function () {
                $(this).removeClass('btn-white');
                $(this).addClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-white');
            })
            $('.columntype button').click(function () {
                $(this).removeClass('btn-white');
                $(this).addClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-white');
            })
            $('.group button').click(function () {
                $(this).removeClass('btn-white');
                $(this).addClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-white');
            })

            Highcharts.setOptions({
                'global': {
                    'useUTC': false
                },
                'lang':{
                    'downloadJPEG':'保存为JPEG图片',
                    'downloadPNG':'保存为PNG图片',
                    'downloadPDF':'保存为PDF文档',
                    'downloadSVG':'保存为SVG图片',
                    'downloadCSV': '另存为 CSV',
                    'downloadXLS': '另存为 XLS',
                    'drillUpText':"返回到 {series.name}",
                    'loading':"正在读取数据",
                    'months':['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                    'noData':"没有可用的数据",
                    'printChart':"打印报表",
                    'resetZoom':"还原缩放比例",
                    'resetZoomTitle':"缩放比例 100%",
                    'shortMonths':['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                    'weekdays':["周日", "周一", "周二", "周三", "周四", "周五", "周六"]
                },
                'credits':{
                    'enable':true,
                    'href':'http://www.jgyou.com',
                    'text':'made by jgyou.com'
                }
            });

        },
        cityajax : function (url,data) {
            jQuery(".province").showLoading();
            $.ajax({
                url: url,
                type: "GET",
                dataType: 'json',
                success: function (datas) {
                    var option = "";
                    $.each(datas.data, function (i, v) {
                        option += '<option value="' + v.city_id + '" hassubinfo="true">' + v.name + '</option>';
                    })
                    $(option).appendTo('.sheng');
                    $(".chosen-select").trigger("chosen:updated");
                    $('.sheng').change(function () {
                        var index = $(this).val();
                        data.province_id = index;
                        $('.shi').html("");
                        if(index == "all"){
                            data.city_id = "all";
                        }
                        var shi = '<option value="all">请选择市</option>';
                        $.each(datas.data, function (i, v) {
                            $.each(v.son, function (ii, vv) {
                                if (vv.parent_city_id == index) {
                                    shi += '<option value="' + vv.city_id + '" hassubinfo="true">' + vv.name + '</option>';
                                }
                            })
                        })
                        $(shi).appendTo('.shi');
                        $(".chosen-select").trigger("chosen:updated");
                        $('.shi').change(function () {
                            var index = $(this).val();
                            data.city_id = index;
                        });
                    });
                    jQuery(".province").hideLoading();
                }
            });
        },
        versionajax : function (url,data) {
            jQuery(".versiondiv").showLoading();
            $.ajax({
                url: url,
                type: "GET",
                dataType: 'json',
                success: function (datas) {
                    var option = "";
                    $.each(datas.data, function (i, v) {
                        if(v.version == 0){
                            v.version = "未知";
                        }
                        if(v.version == 131){
                            v.version = "网页版";
                        }
                        option += '<option value="' + v.version + '" hassubinfo="true">' + v.version + '</option>';
                    })
                    $(option).appendTo('.version');
                    $(".chosen-select").trigger("chosen:updated");
                    $('.version').change(function () {
                        var index = $(this).val();
                        data.version = index;
                    });
                    jQuery(".versiondiv").hideLoading();
                }
            });
        },
        entryajax : function (url,data) {
            jQuery("#enter").showLoading();
            $.ajax({
                url: url,
                type: "post",
                data: data,
                dataType: 'json',
                success: function (data) {
                    var button = "";
                    $.each(data.data, function (i, v) {
                        button += '<button class="btn btn-white" type="button" value=' + v.entry_type + ' style="font-size:10px;">' + v.name + '</button>';

                    })
                    $(button).appendTo('#enter .btn-group');
                    jQuery("#enter").hideLoading();
                }
            });
        },
        subjectajax : function (url,data) {
            $.ajax({
                url: url,
                type: "post",
                data: data,
                dataType: 'json',
                success: function (data) {
                    var button = "";
                    $.each(data.data, function (i, v) {
                        button += '<button class="btn btn-white" type="button" value=' + v.subject + '>' + v.name + '</button>';
                    })
                    $(button).appendTo('.subject .btn-group');
                    jQuery(".subject ").hideLoading();
                }
            });
        },
        datavalue : function (data) {
            data.studentCount = $('.stunum .btn-primary').val();
            data.actionType = $('.use .btn-primary').val();
            data.newType = $('.new .btn-primary').val();
            if ($('.subject .btn-primary').val() == "all" || !$('.subject .btn-primary:first')) {
                data.subject = $('.subject .btn-primary').val();
            } else {
                var subject = "";
                $.each($('.subject .btn-primary'), function () {
                    subject += $(this).val() + ",";
                })
                data.subject = subject;
            }
            data.gradepart = $('.gradepart .btn-primary').val();
            if ($('#enter .btn-primary').val() == "all" || !$('#enter .btn-primary:first')) {
                data.entry_type = $('#enter .btn-primary').val();
            } else {
                var entry_type = "";
                $.each($('#enter .btn-primary'), function () {
                    entry_type += $(this).val() + ",";
                })
                data.entry_type = entry_type;
            }
            data.startday = $('#datepicker input[name="start"]').val();
            data.endday = $('#datepicker input[name="end"]').val();
        },
        linechart : function (options) {

        }
    }

}();