<?php   
date_default_timezone_set(PRC);
$buyFp=fopen("http://hq.sinajs.cn/list=sh000001", 'rb');
$buy=fgets($buyFp,999);
$array0_sh=explode('"',$buy);
$stock_sh=explode(',',$array0_sh[1]);
if ($stock_sh[30]<date("Y-m-d")){
        exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DINGLI</title>
    <style type="text/css">
    #slogn{
        width:100%; 
        margin:0px auto;
        text-align:center;
    }

</style>
</head>
<body>
    <div id="slogn">
        人类一思考，上帝就发笑<br/>
        操作得越多，犯错就越多<br/>
        贪婪还是恐惧？修心为上<br/>

    </div>
    <button id="find">查找</button><br>
    <p id ="findResult"></p>
    <a href="stocksShow.php">new</a>
<script>
document.getElementById("find").onclick = function() { 
    var request = new XMLHttpRequest();
    request.open("GET", "select.php");
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState===4) {
            if (request.status===200) { 
                document.getElementById("findResult").innerHTML = request.responseText;
            } else {
                alert("发生错误：" + request.status);
            }
        } 
    }
}


</script>
    
</body>
</html>