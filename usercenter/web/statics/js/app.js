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


});