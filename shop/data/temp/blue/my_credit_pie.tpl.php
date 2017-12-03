<script src="template/js/percentPie.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
<? if($detailAdd['CreditStatus'] != open ) { ?>
/*未开通*/
    var option1 = {
        value:0,//百分比,必填。总额度，100或0
        name:'未开通',//必填
        backgroundColor:null,
        color:['#01a157','rgba(1,161,87,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_total")//必填
    },percentPie1 = new PercentPie(option1);
    percentPie1.init();
    
  //已用
    var option2 = {
        value:0,//百分比,必填
        name:'未开通',//必填
        backgroundColor:null,
        color:['#f49400','rgba(236,118,26,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_used")//必填
    },percentPie2 = new PercentPie(option2);
    percentPie2.init();

    //剩余
    var option3 = {
        value:0,//百分比,必填
        name:'未开通',//必填
        backgroundColor:null,
        color:['#28b7a9','rgba(40,183,169,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_unused")//必填
    },percentPie3 = new PercentPie(option3);
    percentPie3.init();    
<? } else { ?>
/*已开通*/
var option1 = {
        value:100,//百分比,必填。总额度，100或0
        name:'￥<? echo number_format($Amount,2); ?>',//必填
        backgroundColor:null,
        color:['#01a157','#f49400'],
        fontSize:16,
        domEle:document.getElementById("credit_total")//必填
    },percentPie1 = new PercentPie(option1);
    percentPie1.init();
    
    //已用
    var option2 = {
        value:<?=$usedLu?>,//百分比,必填
        name:'￥<? echo number_format($usedAmount,2); ?>',//必填
        backgroundColor:null,
        color:['#f49400', 'rgba(236,118,26,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_used")//必填
    },percentPie2 = new PercentPie(option2);
    percentPie2.init();

    //剩余
    var option3 = {
        value:<?=$Residuelu?>,//百分比,必填
        name:'￥<? echo number_format($ResidueAmount,2); ?>',//必填
        backgroundColor:null,
        color:['#28b7a9','rgba(40,183,169,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_unused")//必填
    },percentPie3 = new PercentPie(option3);
    percentPie3.init();
<? } ?>
</script>