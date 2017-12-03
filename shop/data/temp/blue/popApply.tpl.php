<style>
.applyPop{
width: 310px;
height: 120px;

padding: 0.1px;
border-radius: 8px;
display: none;

overflow: hidden;
}
.applyPop>svg{
width: 28px;
height: 28px;
position: absolute;
top: 8px;
right: 8px;

cursor: pointer;
}
.applyPop>p{
width: 100%;
text-align: center;

font-size: 14px;
color: #666;

margin: 35px 0 20px 0;
}
.applyPop>a{
display: block;
width: 238px;
height: 36px;
line-height: 36px;
text-align: center;

color: #fff;
font-size: 14px;
background: #04A057;

margin: 0 auto;
}
</style>

<div class="applyPop" id="applyPop">
<i class="iconfont icon-weibiaoti101 close"></i>
<p >您暂未开通医统账期，请现在前往开通</p>
<a href="my.php?m=creditApply">开通账期</a>
</div>
<script type="text/javascript">
$(".close").click(function(){
layer.closeAll();
})
</script>