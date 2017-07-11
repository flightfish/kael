$(function () {
    var data = {
        'studentCount':"all",
        'actionType':"all",
        'newType':"all",
        'subject':"all",
        'gradepart':"all",
        'province_id':"all",
        'city_id':"all",
        'version':"all",
        'entry_type':"allentry",
        'startday':$('#datepicker input[name="start"]').val(),
        'endday':$('#datepicker input[name="end"]').val(),
        'group':"week",
    }
    $('.group button').each(function(){
        $(this).click(function(){
            data.group = $(this).attr('value');
            var text = $('#enter .btn-group button:eq(0)').text();
            picture(text);
        })
    })

    function picture(name){
        var lineoption = {
            title: {
                text: '点展比趋势图',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: [],
                tickInterval: 5,
            },
            yAxis: {
                title: {
                    text: '点展比'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: '%'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: []
        }
        var columnoption = {
            chart: {
                type: 'column'
            },
            title: {
                text: '点展比占比图'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [],
                crosshair: true,
                tickInterval: 5,
            },
            yAxis: {
                min: 0,
                title: {
                    text: '点展比'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: []

        }
        $.ajax({
            url:"/enter/click-show/click",
            type: "post",
            dataType:'json',
            data:data,
            success: function(datas) {
                var category = [];
                var series = [];
                var seriesdata=[];

                $.each(datas.data,function(i,v){
                    category.push(v.xAxis);
                    seriesdata.push(parseInt(v.counts));
                })
                series.push({name:name,data:seriesdata});
                if(category.length > 10){
                    columnoption.xAxis.tickInterval = Math.ceil(category.length/9);
                    lineoption.xAxis.tickInterval = Math.ceil(category.length/9);
                }else{
                    lineoption.xAxis.tickInterval = 1;
                    columnoption.xAxis.tickInterval = 1;
                }
                lineoption.xAxis.categories = category
                lineoption.series = series;
                columnoption.xAxis.categories = category;
                columnoption.series = series;

                $('#container').highcharts(lineoption);
                $('#container1').highcharts(columnoption);
            }
        });
    }


    $.ajax({
        url:"/common/city",
        type: "GET",
        dataType:'json',
        success: function(datas) {
            var option = "";
            $.each(datas.data,function(i,v){
                option += '<option value="'+v.city_id+'" hassubinfo="true">'+v.name+'</option>';
            })
            $(option).appendTo('.sheng');
            $(".chosen-select").trigger("chosen:updated");
            $('.sheng').change(function(){
                var index = $(this).val();
                data.province_id = index;
                $('.shi').html("");
                var shi = '<option value="all">请选择市</option>';
                $.each(datas.data,function(i,v){
                    $.each(v.son,function(ii,vv){
                        if(vv.parent_city_id == index){
                            shi += '<option value="'+vv.city_id+'" hassubinfo="true">'+vv.name+'</option>';
                        }
                    })
                })
                $(shi).appendTo('.shi');
                $(".chosen-select").trigger("chosen:updated");
                $('.shi').change(function(){
                    var index = $(this).val();
                    data.city_id = index;
                });
            });

        }
    });
    $.ajax({
        url:"/common/entype",
        type: "GET",
        dataType:'json',
        success: function(data) {
            var button = "";
            $.each(data.data,function(i,v){
                if(i==0){
                    button += '<button class="btn btn-primary" type="button" value='+v.entry_type+'>'+v.name+'</button>';
                }else{
                    button += '<button class="btn btn-white" type="button" value='+v.entry_type+'>'+v.name+'</button>';
                }

            })
            $(button).appendTo('#enter .btn-group');
            var text = $('#enter .btn-group button:eq(0)').text();
            picture(text);

        }
    });
    $.ajax({
        url:"/common/subject",
        type: "GET",
        dataType:'json',
        success: function(data) {
            var button = "";
            $.each(data.data,function(i,v){
                button += '<button class="btn btn-white" type="button" value='+v.subject+'>'+v.name+'</button>';
            })
            $(button).appendTo('.subject .btn-group');

        }
    });

    $.ajax({
        url:"/common/version",
        type: "GET",
        dataType:'json',
        success: function(datas) {
            var option = "";
            $.each(datas.data,function(i,v){
                option += '<option value="'+v.version+'" hassubinfo="true">'+v.version+'</option>';
            })
            $(option).appendTo('.version');
            $(".chosen-select").trigger("chosen:updated");
            $('.version').change(function(){
                var index = $(this).val();
                data.version = index;
            });
        }
    });
    $('.form-group:not("#enter") .btn-group').on('click','button',function(){
        $(this).removeClass('btn-white');
        $(this).addClass('btn-primary').siblings().removeClass('btn-primary').addClass('btn-white');
        $(this).parent().prev().removeClass('btn-primary').addClass('btn-white');
    })

    $('.form-group:not("#enter") .all').click(function(){
        $(this).addClass('btn-primary').removeClass('btn-white');
        $(this).next().find('.btn-primary').addClass('btn-white');
        $(this).next().find('button').removeClass('btn-primary');
    })
    $('#enter .all').click(function(){
        var that = $(this);
        data.entry_type = $(that).val();
        if($(this).hasClass("btn-white")){
//                        $(this).next().find('button').addClass('btn-primary').removeClass('btn-white');
            $(this).addClass('btn-primary').removeClass('btn-white');
            toggelline(that);
        }else{
//                        $(this).next().find('button').addClass('btn-white').removeClass('btn-primary');
            $(this).addClass('btn-white').removeClass('btn-primary');
            $.each($('.highcharts-legend-item text'),function(){
                if($(this).text() == $(that).text()){
                    $(this).trigger('click');
                }
            })
        }
    })
    function toggelline(that){
        var status = 1;
        $.each($('.highcharts-legend-item text'),function(){
            if($(this).text() == $(that).text()){
                $(this).trigger('click');
                status = 0;
            }
        })
        if(status){
            $.ajax({
                url:"/enter/click-show/click",
                type: "post",
                dataType:'json',
                data:data,
                success: function(datas) {
                    var seriesdata=[];
                    $.each(datas.data,function(i,v){
                        seriesdata.push(parseInt(v.counts));
                    })

                    $('#container,#container1').highcharts().addSeries(
                        {
                            "name": that.text(),
                            "data": seriesdata
                        });
                }
            });
        }
    }
    $('#enter .btn-group').on('click','button',function(){
        var that = $(this);
        data.entry_type = $(that).val();

        if($(this).hasClass("btn-white")){
            $(this).addClass('btn-primary').removeClass('btn-white');
            toggelline(that);
        }else{
            $(this).addClass('btn-white').removeClass('btn-primary');
            $.each($('.highcharts-legend-item text'),function(){
                if($(this).text() == $(that).text()){
                    $(this).trigger('click');
                }
            })

        }
        $('#enter .btn-group button').each(function(){

        })

    })
    $('.group button').click(function(){
        $(this).removeClass('btn-white');
        $(this).addClass('btn-default').siblings().removeClass('btn-primary').addClass('btn-white');
    })


    $('.check').click(function(){
        data.studentCount = $('.stunum .btn-primary').val();
        data.actionType = $('.use .btn-primary').val();
        data.newType = $('.new .btn-primary').val();
        data.subject = $('.subject .btn-primary').val();
        data.gradepart = $('.gradepart .btn-primary').val();
        var text = $('#enter .btn-group button:eq(0)').text();
        picture(text);
    })
    $('.dateok').click(function(){
        data.startday = $('#datepicker input[name="start"]').val();
        data.endday = $('#datepicker input[name="end"]').val();
        var text = $('#enter .btn-group button:eq(0)').text();
        picture(text);
    })

});