
function getLodop(oOBJECT,oEMBED){
	var br = GetIEVersion();
	
	if(br != "IE11.0"){

        var strHtmInstall="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='../plugin/printf/install_dhb_print_32.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtmUpdate="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='../plugin/printf/install_dhb_print_32.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtm64_Install="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='../plugin/printf/install_dhb_print_64.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtm64_Update="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='../plugin/printf/install_dhb_print_64.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtmFireFox="<br><br><font color='#FF00FF'>注意：<br>1：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它。</font>";
        var LODOP=oEMBED;		
	try{		     
	     if (navigator.appVersion.indexOf("MSIE")>=0) LODOP=oOBJECT;
	     if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
		 if (navigator.userAgent.indexOf('Firefox')>=0)
  	         document.documentElement.innerHTML=strHtmFireFox+document.documentElement.innerHTML;
		 if (navigator.userAgent.indexOf('Win64')>=0){
		 	if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtm64_Install); else
		 	document.documentElement.innerHTML=strHtm64_Install+document.documentElement.innerHTML;		 
		 } else {
		 	if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtmInstall); else
		 	document.documentElement.innerHTML=strHtmInstall+document.documentElement.innerHTML;
		 }
		 return LODOP; 
	     } else if (LODOP.VERSION<"6.1.4.5") {
		if (navigator.userAgent.indexOf('Win64')>=0){
	            if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtm64_Update); else
		    document.documentElement.innerHTML=strHtm64_Update+document.documentElement.innerHTML; 
		} else {
	            if (navigator.appVersion.indexOf("MSIE")>=0) document.write(strHtmUpdate); else
		    document.documentElement.innerHTML=strHtmUpdate+document.documentElement.innerHTML; 
		}
		 return LODOP;
	  }
	     //=====如下空白位置适合调用统一功能:=====	     
		LODOP.SET_LICENSES("成都阿商信息技术有限公司","1B61DF8932E17DEDB113D1E7F683CA12","",""); 

	     //=======================================
	     return LODOP; 
	}catch(err){
		if (navigator.userAgent.indexOf('Win64')>=0)	
		document.documentElement.innerHTML="Error:"+strHtm64_Install+document.documentElement.innerHTML;else
		document.documentElement.innerHTML="Error:"+strHtmInstall+document.documentElement.innerHTML;
	     return LODOP; 
	}

	}else{


        var strHtmInstall="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='../plugin/printf/install_lodop32.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtmUpdate="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='../plugin/printf/install_lodop32.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtm64_Install="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='../plugin/printf/install_lodop64.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtm64_Update="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='../plugin/printf/install_lodop64.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtmFireFox="<br><br><font color='#FF00FF'>注意：<br>1：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它。</font>";
        var LODOP=oEMBED;               
        try{    
             var isIE    =  (navigator.userAgent.indexOf('MSIE')>=0) || (navigator.userAgent.indexOf('Trident')>=0);
             var is64IE  = isIE && (navigator.userAgent.indexOf('x64')>=0);
             if (isIE) LODOP=oOBJECT;
             if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
                 if (navigator.userAgent.indexOf('Firefox')>=0)
                             {document.documentElement.innerHTML=strHtmFireFox+document.documentElement.innerHTML;};
                 if (is64IE) {document.write(strHtm64_Install);} else            
                 if (isIE)   {document.write(strHtmInstall);   } else 
                             {document.documentElement.innerHTML=strHtmInstall+document.documentElement.innerHTML;};     
                 return LODOP; 
             } else 
             if (LODOP.VERSION<"6.1.5.8") {
                if (is64IE){document.write(strHtm64_Update);} else
                if (isIE)  {document.write(strHtmUpdate);    } else
                           {document.documentElement.innerHTML=strHtmUpdate+document.documentElement.innerHTML; };
                return LODOP;
             }
             //=====如下空白位置适合调用统一功能:=====             
			LODOP.SET_LICENSES("成都阿商信息技术有限公司","1B61DF8932E17DEDB113D1E7F683CA12","",""); 

             //=======================================
             return LODOP; 
        }catch(err){
                if (is64IE)     
                document.documentElement.innerHTML="Error:"+strHtm64_Install+document.documentElement.innerHTML;else
                document.documentElement.innerHTML="Error:"+strHtmInstall+document.documentElement.innerHTML;
             return LODOP; 
        }
	}
}

function GetIEVersion() {
              var userAgent = navigator.userAgent,    

                rMsie = /(msie\s|trident.*rv:)([\w.]+)/,    

                rFirefox = /(firefox)\/([\w.]+)/,    

                rOpera = /(opera).+version\/([\w.]+)/,    

                rChrome = /(chrome)\/([\w.]+)/,    

                rSafari = /version\/([\w.]+).*(safari)/;   

                var browser;   

                var version;   

                var ua = userAgent.toLowerCase();   

                function uaMatch(ua) {   

                    var match = rMsie.exec(ua);   

                    if (match != null) {   

                        return { browser : "IE", version : match[2] || "0" };   

                    }   

                    var match = rFirefox.exec(ua);   

                    if (match != null) {   

                        return { browser : match[1] || "", version : match[2] || "0" };   

                    }   

                    var match = rOpera.exec(ua);   

                    if (match != null) {   

                        return { browser : match[1] || "", version : match[2] || "0" };   

                    }   

                    var match = rChrome.exec(ua);   

                    if (match != null) {   

                        return { browser : match[1] || "", version : match[2] || "0" };   

                    }   

                    var match = rSafari.exec(ua);   

                    if (match != null) {   

                        return { browser : match[2] || "", version : match[1] || "0" };   

                    }   

                    if (match != null) {   

                        return { browser : "", version : "0" };   

                    }   

                }   

                var browserMatch = uaMatch(userAgent.toLowerCase());   

                if (browserMatch.browser) {   

                    browser = browserMatch.browser;   

                    version = browserMatch.version;   

                }   

                return (browser+version);  
}