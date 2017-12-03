/**
 * Created by louis on 2015/10/28.
 */
//保存推广信息数据
function do_save_generalize()
{
    document.MainForm.referer.value = document.location;
    var data_generalizeNo = $('#data_generalizeNo').val();
    if(data_generalizeNo=="")
    {
        $.blockUI({ message: "<p>请先输入编号！</p>" });
    }else if(!/^[A-Za-z0-9]*$/.test(data_generalizeNo)){
        $.blockUI({ message: "<p>只能输入数字或字母！</p>" });
    }else if(data_generalizeNo.length > 6){
        $.blockUI({ message: "<p>只能输入6个以内字符！</p>" });
    }else if($('#data_generalizeName').val()=="" && $('#data_generalizeName')!=undefined){
            $.blockUI({ message: "<p>请先输入名称！</p>" });
    }else if($('#data_generalizeType').val()==""){
        $.blockUI({ message: "<p>请先输入类型！</p>" });
    }else if($('#data_generalizeName').val()!=undefined && $('#data_generalizeType').val()=="seller"){
            $.blockUI({ message: "<p>不能输入seller,请更换！</p>" });
    }else if($('#data_url').val()==""){
        $.blockUI({ message: "<p>请先输入地址！</p>" });
    }else{
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_generalize.php?m=generalize_add_save", $("#MainForm").serialize(),
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    $.blockUI({ message: "<p>保存成功!</p>" });
                    window.location.reload(true);
                    //window.location.href = "generalize.php";
                    $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                }
            }
        );
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_edit_generalize()
{
    document.MainForm.referer.value = document.location;

    var edit_GeneralizeNO = $('#edit_GeneralizeNO').val();
    console.debug(edit_GeneralizeNO.length);
    if(edit_GeneralizeNO=="")
    {
        $.blockUI({ message: "<p>请先输入编号！</p>" });
    }else if(!/^[A-Za-z0-9]*$/.test(edit_GeneralizeNO)){
        $.blockUI({ message: "<p>只能输入数字或字母！</p>" });
    }else if(edit_GeneralizeNO.length > 6){
        $.blockUI({ message: "<p>只能输入6个以内字符！</p>" });
    }else if($('#edit_GeneralizeName').val()=="" && $('#edit_GeneralizeName')!=undefined){
            $.blockUI({message: "<p>请先输入名称！</p>"});
    }else if($('#edit_GeneralizeType').val()==""){
        $.blockUI({ message: "<p>请先输入类型！</p>" });
    }else if($('#edit_GeneralizeName').val() != undefined && $('#edit_GeneralizeType').val()=="seller"){
            $.blockUI({ message: "<p>不能输入seller,请更换！</p>" });
    }else if($('#edit_Url').val()==""){
        $.blockUI({ message: "<p>请先输入地址！</p>" });
    }else{
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_generalize.php?m=generalize_edit_save", $("#MainForm").serialize(),
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    $.blockUI({ message: "<p>保存成功!</p>" });
                    window.location.reload(true);
                    //window.location.href = "generalize.php";
                    $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                    $('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
                }
            }
        );
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 5000);
}

function do_delete_bill(pid)
{
    if(confirm('确认删除吗?'))
    {
        $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
        $.post("do_generalize.php",
            {m:"delete_generalize", ID: pid},
            function(data){
                data = Jtrim(data);
                if(data == "ok"){
                    var delline = "line_" + pid;
                    $("#"+delline).hide();
                    $.blockUI({ message: "<p>删除成功!</p>" });
                }else{
                    $.blockUI({ message: "<p>"+data+"</p>" });
                }
            }
        );
    }else{
        return false;
    }
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
    window.setTimeout($.unblockUI, 2000);
}

function set_edit_bill(uid,uno,uname,utype,uurl)
{
    $("#update_id").val(uid);
    $("#edit_GeneralizeNO").val(uno);
    $("#edit_GeneralizeName").val(uname);
    $("#edit_GeneralizeType").val(utype);
    if('http://'.indexOf(uurl) !== -1){
        var u = uurl.split('http://');
        uurl = u[1];
    }
    var url = uurl.split('/a/');
    $("#edit_Url").val(url[0]);
    $("#edit_generalize").show();
}

//去空隔函数
function Jtrim(str){
    return str.replace(/^\s*|\s*$/g,"");
}
/*********/
var old_bg="";
function inStyle(obj)
{
    old_bg=obj.style.background;
    obj.style.background="#edf3f9";
}

function outStyle(obj)
{
    obj.style.background=old_bg;
}
