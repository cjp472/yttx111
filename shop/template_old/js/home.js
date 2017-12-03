
function change_spc(tid)
{
		switch(tid){
			case '1':

			 document.getElementById("show_product_1").style.display    = "block";
			 document.getElementById("show_product_2").style.display    = "none";
			 document.getElementById("show_product_3").style.display    = "none";
			 
			 document.getElementById("chenge_title_1").innerHTML    = '<img src="template/default/img/home_title1_over.jpg" border="0" title="特价促销" />';			 
			 document.getElementById("chenge_title_2").innerHTML    = '<img src="template/default/img/home_title2.jpg" border="0" title="新品上架" />';	
			 document.getElementById("chenge_title_3").innerHTML    = '<img src="template/default/img/home_title3.jpg" border="0" title="推荐商品" />';	  
			 break;
			 
			case '2':
			 document.getElementById("show_product_1").style.display    = "none";
			 document.getElementById("show_product_2").style.display    = "block";
			 document.getElementById("show_product_3").style.display    = "none";
			 
			 document.getElementById("chenge_title_1").innerHTML    = '<img src="template/default/img/home_title1.jpg" border="0" title="特价促销" />';			 
			 document.getElementById("chenge_title_2").innerHTML    = '<img src="template/default/img/home_title2_over.jpg" border="0" title="新品上架" />';	
			 document.getElementById("chenge_title_3").innerHTML    = '<img src="template/default/img/home_title3.jpg" border="0" title="推荐商品" />';	  
			 break;
			 
			case '3':
			 document.getElementById("show_product_1").style.display    = "none";
			 document.getElementById("show_product_2").style.display    = "none";
			 document.getElementById("show_product_3").style.display    = "block";
			 
			 document.getElementById("chenge_title_1").innerHTML    = '<img src="template/default/img/home_title1.jpg" border="0" title="特价促销" />';			 
			 document.getElementById("chenge_title_2").innerHTML    = '<img src="template/default/img/home_title2.jpg" border="0" title="新品上架" />';	
			 document.getElementById("chenge_title_3").innerHTML    = '<img src="template/default/img/home_title3_over.jpg" border="0" title="推荐商品" />';	 	 
			 break;
		 			 			 
		}
}



function fixpng24(){
    var arVersion = navigator.appVersion.split("MSIE");
    var version = parseFloat(arVersion[1]);
    if ((version >= 5.5) && (document.body.filters)){
       for(var i=0; i<document.images.length; i++){
          var img = document.images[i];
          if (img.src.toLowerCase().slice(-3) == "png"){
             var imgID = (img.id) ? "id='" + img.id + "' " : "";
             var imgClass = (img.className) ? "class='" + img.className + "' " : "";
             var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' ";
             var imgStyle = "display:inline-block;" + img.style.cssText ;
             if (img.align == "left") imgStyle = "float:left;" + imgStyle;
             if (img.align == "right") imgStyle = "float:right;" + imgStyle;
             if (img.parentElement.href) imgStyle = "cursor:pointer;" + imgStyle;
             var strNewHTML = "<span " + imgID + imgClass + imgTitle
             + " style=\"width:" + img.width + "px; height:" + img.height + "px;" + imgStyle
             + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
             + "(src='" + img.src + "', sizingMethod='scale');\"></span>";
             img.outerHTML = strNewHTML;
             i--;
          }
       }
    }
}