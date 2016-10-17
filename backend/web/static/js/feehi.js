yii.confirm = function(message, ok, cancel) {
    var url = $(this).attr('href');
    var if_pjax = $(this).attr('data-pjax') ? $(this).attr('data-pjax') : 0;
    swal({
        title: message,
        text: tips.realyToDo,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        cancelButtonText: tips.cancel,
        confirmButtonText: tips.ok,
        closeOnConfirm: false
    }, function (isConfirm) {
        if(isConfirm) {
            if( parseInt( if_pjax ) ){
                !ok || ok();
            }else {
                swal(tips.waitingAndNoRefresh, tips.operating + '...', "success");
                $.ajax({
                    "url": url,
                    "dataType": "json",
                    "type": $(this).attr('data-method') ? $(this).attr('data-method') : "get",
                    "success": function (data) {
                        if (data.code == 0) {
                            swal(tips.success + '!', tips.operatingSuccess + '.', "success");
                            location.reload();
                        } else {
                            swal(tips.error + ': ' + data.message, tips.operatingFailed + '.', "error");
                        }
                    },
                    "error": function (jqXHR, textStatus, errorThrown) {
                        swal(tips.error + ': ' + jqXHR.responseJSON.message, tips.operatingFailed + '.', "error");
                    }
                });
            }
        }else{
            !cancel || cancel();
        }
    });
}
function viewLayer(url, obj)
{
    layer.open({
        type: 2,
        title: obj.attr('title'),
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        area : ['800px' , '520px'],
        content: url
    });
}

$(document).ready(function(){
    //$('.info').animate({opacity: 1.0}, 3000).fadeOut('slow');
    $("input[type=file]").prettyFile({text:common.chooseFile});
    $(".multi-operate").click(function () {
        var url = $(this).attr('href');
        var ids = new Array();
        $("tr td input[type=checkbox]:checked").each(function(){
            ids.push($(this).val());
        });
        if(ids.length <= 0){
            swal(tips.noItemSelected, tips.PleaseSelectOne);
            return false;
        }
        ids = ids.join(',');
        swal({
            title: $(this).attr("data-confirm"),
            text: ids,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: tips.cancel,
            confirmButtonText: tips.ok,
            closeOnConfirm: false
        }, function (isConfirm) {
            if(isConfirm) {
                swal(tips.waitingAndNoRefresh, tips.operating+'...', "success");
                $.ajax({
                    "url":url,
                    "dataType" : "json",
                    "type" : "get",
                    "data":{'id':ids},
                    "success" : function (data) {
                        if (data.code == 0) {
                            swal(tips.success + '!', tips.operatingSuccess + '.', "success");
                            location.reload();
                        } else {
                            swal(tips.error + ': ' + data.message, tips.operatingFailed + '.', "error");
                        }
                    },
                    "error": function (jqXHR, textStatus, errorThrown) {
                        swal(tips.error + ': ' + jqXHR.responseJSON.message, tips.operatingFailed + '.', "error");
                    }
                });
            }else{
                return false;
            }
        });
        return false;
    })

    $("a.close-link").click(function () {
        var node = $(this).parents("div.ibox:first");
        node.hide();
        if(node.length == 0){
            $(this).parents("div.ibox-title").hide();
            $(this).parents("div.ibox-title:first").next().hide();
        }
        $(this).parents("div.ibox:first").hide();
    })

    $("a.collapse-link").click(function () {
        var node = $(this).parents("div.ibox:first").children("div.ibox-content");
        node.slideToggle();
        var iClass = $(this).children("i:first").attr('class');
        if(iClass == 'fa fa-chevron-up'){
            $(this).children("i:first").attr('class', 'fa fa-chevron-down');
        }else{
            $(this).children("i:first").attr('class', 'fa fa-chevron-up');
        }
        if(node.length == 0){
            $(this).parents("div.ibox-title:first").next().slideToggle();
        }

    })

    $('a.sort').click(function(){
        $('input[type=number]').each(function(){
            $(this).clone().attr('type', 'hidden').appendTo('form[name=sort]');
            $('form[name=sort]').append();
        })
        $('form[name=sort]').submit();
        return false;
    })

    $('a.refresh').click(function(){
        location.reload();
        return false;
    });
    $('input[type=file]').bind('change', function () {
        if (typeof FileReader === 'undefined') {
            return;
        }
        var that = $(this);
        var files = $( this )[0].files;
        if(that.parent().parent().attr('class').indexOf("image")){
            if(!/image\/\w+/.test(files[0].type)){
                layer.tips(tips.mustChoosePic, that.parent().parent());
                return false;
            }
            var reader = new FileReader();
            reader.readAsDataURL(files[0]);
            reader.onload = function (e) {
                that.parents("div.image").children("img").attr("src", this.result);
            }
        }
    })
})