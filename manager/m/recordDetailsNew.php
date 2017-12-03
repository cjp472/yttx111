<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title>回访记录详情</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
	<style>
		label{
			color: #333333;
			font-size: 15px;
			font-weight: bold;
		}
		input{
			width: 300px;
			height: 25px;
		}
		.detailName{
			text-align: left;
		}
		.myTable tr td{
			height: 50px;
		}
		.submitBtn,.returnBtn{
			background: #FF0066;
			border: none;
			border-radius: 6px;
			width: 100px;
			cursor: pointer;
		}
		.returnBtn{
			background: #169BD5;
		}
		.allBtn{
			margin-top: 30px;
			margin-left: 300px;
		}
	</style>
	<script>
	</script>
</head>
<body>
<form action="" method="post"  class="goBackDetail">
	<table cellspacing="0" cellpadding="0" class="myTable">
		<tr>
			<td class="detailName">
				<label for="recordTime">记录 时间：</label>
			</td>
			<td >
				<input type="text"  id="recordTime"/><br/>
			</td>
		</tr>
		<tr>
			<td class="detailName">
				<label for="recordPerson">联 系 人：</label>
			</td>
			<td>
				<input type="text"  id="recordPerson"/><br/>
			</td>
		</tr>
		<tr>
			<td class="detailName">
				<label for="recordPersonDuty">联系人职务：</label>
			</td>
			<td>
				<input type="text"  id="recordPersonDuty"/><br/>
			</td>
		</tr>
		<tr>
			<td class="detailName">
				<label for="recordPhone">联系人电话：</label>
			</td>
			<td>
				<input type="text"  id="recordPhone"/><br/>
			</td>
		</tr>
		<tr>
			<td class="detailName">
				<label for="goBackPerson">回 访 人：</label>
			</td>
			<td>
				<input type="text"  id="goBackPerson"/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="recordTime">回访 简情：</label>
			</td>
			<td>
				<input type="text"  id="recordDetail"/><br/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="recordInfo">记录 内容：</label>
			</td>
			<td>
				<textarea name="" id="recordInfo" cols="60" rows="8"></textarea><br/>
			</td>
		</tr>
	</table>
	<div class="allBtn">
		<input type="button" value="提交" class="submitBtn"/>
		<input type="button" value="返回" class="returnBtn"/>
	</div>
</form>
</body>
</html>