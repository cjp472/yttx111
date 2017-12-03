
  //刷新
  function frm_reload()
  {
    window.top.location.reload();
  } 
  
  //预览
  function frm_print_view(){
  
    window.parent.printMain.focus();
    window.parent.printMain.Preview();

  }
  
  //打印
  function frm_print(){
  
    window.parent.printMain.focus();
    window.parent.printMain.Preprint();

  }

  //excel
  function frm_excel(){

    window.parent.printMain.MainForm.target		= "exe_iframe";
    window.parent.printMain.MainForm.handle.value	= "excel";
	window.parent.printMain.MainForm.submit();

  }

  function frm_close(){

	top.close();
  }

    //显示
  function frm_showhide()
  {
		if(document.getElementById('showid').checked==true)
	    {
			window.parent.printMain.control('showcontactid','hide');
	    }else{
			window.parent.printMain.control('showcontactid','show');
	   }
  }

  function frm_hiddeninfo()
  {
		document.getElementById('showid').checked=true;
  }