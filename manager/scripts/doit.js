$(document).ready(function() 
{
	$('#commentsubmit').click( function(){
		
		$("#warning").show();

		if($("#rconent").val() == ""){
			$("#warning").html("你忘了写内容？");
		}		
		else{
			$('#commentsubmit').attr("disabled","disabled");
			$.post("/doit.php",
				{m:"addcomment", kid: $("#kid").val(),rname: $("#rname").val(), rcontent: $("#rcontent").val()},
				function(data){
					if(data == "ERROR"){
						$("#warning").html("提交失败！");
						$('#commentsubmit').attr("disabled","");
					}else{
						$("#warning").html("留言已成功提交！");
						$("#comment_iwant2").after(data);
						$("#rconent").val('');

					}
				}			
			);
		}
		window.setTimeout("hideshow('warning')",10000);

	});
});


function loadcomment(fid,pid){
			
			$.post("/loadgame.php",
				{m:"loadcomment", fid:fid, pid:pid, lan:"cn"},
				function(data){
					$("#listcomment").html(data);
				}
			);

}

function control(obj, sType) 
{
	var oDiv = document.getElementById(obj);
	if (sType == 'show') { oDiv.style.display = 'block';}
	if (sType == 'hide') { oDiv.style.display = 'none';}
}

function hideshow(divid)
{
	$("#"+divid).animate({opacity: 'hide'}, 'fast');
	$("#"+divid).html('<img src="/images/loader.gif" alt="loading" class="img" /> Loading...');
}