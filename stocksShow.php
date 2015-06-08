<?php   
date_default_timezone_set(PRC);
$buyFp=fopen("http://hq.sinajs.cn/list=sh000001", 'rb');
$buy=fgets($buyFp,999);
$array0_sh=explode('"',$buy);
$stock_sh=explode(',',$array0_sh[1]);
if ($stock_sh[30]<date("Y-m-d")){
        exit;
}else{
    $today=date("Y_m_d");
    $mysql = new SaeMysql();

    function poo($name,$mysql_p,$pic){
        $sql_poo="select iid,name,code from s_".$name." where picture = '".$pic."' order by code desc ;";
        $data_s=$mysql_p->getData( $sql_poo);
        for($i=0;$i<count($data_s);$i++){
            echo "<a href='http://xueqiu.com/S/".$data_s[$i]['iid']."' target = '_blank'>";
            echo $data_s[$i]['iid']." ";
            echo "</a>";
            echo $data_s[$i]['name']." ";
            echo $data_s[$i]['code'];
             echo "<br />";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DINGLI</title>
    <style type="text/css">
    #container { 
        width:100%; 
        margin:0px auto;
    }
    #slogn{
        width:100%; 
        margin:0px auto;
        text-align:center;
    }
.little{
         width:16%;
         height:100%;
         border:1px #333 solid;
         margin:3px;
         float:left;
        }
</style>
</head>
<body>
    <div id="slogn">
        人类一思考，上帝就发笑<br/>
        操作得越多，犯错就越多<br/>
        贪婪还是恐惧？修心为上<br/>

    </div>
    <div id="container">
        <div class = "little">
            起风 <br />
            <?php
            poo($today,$mysql,"起风");
    
            ?>
            
        </div>  
        <div class = "little">
            小红绿红 <br />
            <?php
            poo($today,$mysql,"小红绿红");
echo "<br />"."die3"."<br />";
poo($today,$mysql,"跌3");
    
            ?>
        </div>
        <div class = "little">
            反转 <br />
            <?php
            poo($today,$mysql,"反转");
    
            ?>
        </div>
        <div class = "little">
            小红 <br />
            <?php
            poo($today,$mysql,"小红");
    
            ?>
        </div>
        <div class = "little">
            启明 <br />
            <?php
            poo($today,$mysql,"启明");
    
            ?>
        </div>
        <div class = "little">
            惯性 <br />
            <?php
            poo($today,$mysql,"惯性");
    
            ?>
        </div> 
    </div>
</body>
</html>