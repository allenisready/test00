 <?php
		date_default_timezone_set(PRC);
		$buyFp=fopen("http://hq.sinajs.cn/list=sh000001", 'rb');
		$buy=fgets($buyFp,999);
		$array0=explode('"',$buy);
		$stock1=explode(',',$array0[1]);
		if ($stock1[30]<date("Y-m-d")){
			exit;
		}else{   
			$day=date("Y_m_d");
			$mysql= new SaeMysql();
			$sql_insert_day = "insert into goodday ( date) values ('".$day."');";
			$mysql->runSql($sql_insert_day);
			
			$sql_create_table="create table ".$day." ( ".$day."_id char(10) not null primary key, ".$day."_name char(20) not null, 
					".$day."_ps float not null, ".$day."_pf float not null, ".$day."_ph float not null, ".$day."_pl float not null, ".$day."_ml float not null );";
			$mysql->runSql($sql_create_table);
				
			$sh600url="http://hq.sinajs.cn/list=sh600";
			$sh601url="http://hq.sinajs.cn/list=sh601";
			$sz002url="http://hq.sinajs.cn/list=sz002";
			$sz300url="http://hq.sinajs.cn/list=sz300";
            $sz000url="http://hq.sinajs.cn/list=sz000";
            $str600="sh600";
            $str601="sh601";
            $str002="sz002";
            $str300="sz300";
            $str000="sz000";
            $dex_url="http://hq.sinajs.cn/list=";
            
            $sql_select_dex="select * from dex";
            $big = $mysql->getData( $sql_select_dex );
            for($i=0;$i<count($big);$i++){
            	$r=$big[$i];
            	fetch_dex($dex_url, $r, $mysql, $day);
            }

			
          fetch_data($sh600url,$str600,$mysql,$day);
          fetch_data($sh601url,$str601,$mysql,$day);
          fetch_data($sz002url,$str002,$mysql,$day);
          fetch_data($sz300url,$str300,$mysql,$day);
          fetch_data($sz000url,$str000,$mysql,$day);
          
            
          $sql_drop_last_1="drop table t_big";
	$mysql->runSql($sql_drop_last_1);
	
	$sql_get_day="select * from goodday order by date";
	$day_arr = $mysql->getData( $sql_get_day );
	
	$last=count($day_arr)-1;
	
	$t0=$day_arr[$last]['date'];
	$t1=$day_arr[$last-1]['date'];
	$t2=$day_arr[$last-2]['date'];
	$t3=$day_arr[$last-3]['date'];
	$t4=$day_arr[$last-4]['date'];
	$t5=$day_arr[$last-5]['date'];
	$t6=$day_arr[$last-6]['date'];
	$t7=$day_arr[$last-7]['date'];
	$t8=$day_arr[$last-8]['date'];
	
	$sql_create_table_big="create table t_big select ".$t0.".*, ".$t1.".*, ".$t2.".*, ".$t3.".*, ".
	$t4.".*, ".$t5.".*, ".$t6.".*, ".$t7.".*, ".$t8.".*
          from ".$t0." inner join ".$t1." on ".$t0.".".$t0."_id = ".$t1.".".$t1."_id
          inner join ".$t2." on ".$t0.".".$t0."_id = ".$t2.".".$t2."_id
          inner join ".$t3." on ".$t0.".".$t0."_id = ".$t3.".".$t3."_id
          inner join ".$t4." on ".$t0.".".$t0."_id = ".$t4.".".$t4."_id
          inner join ".$t5." on ".$t0.".".$t0."_id = ".$t5.".".$t5."_id
          inner join ".$t6." on ".$t0.".".$t0."_id = ".$t6.".".$t6."_id
          inner join ".$t7." on ".$t0.".".$t0."_id = ".$t7.".".$t7."_id
          inner join ".$t8." on ".$t0.".".$t0."_id = ".$t8.".".$t8."_id;";
	$mysql->runSql($sql_create_table_big);
	
	$sql_index="alter table t_big add primary key (".$t0."_id);";
	$mysql->runSql($sql_index);
	
	for($l=0;$l<9;$l++){
		$va="t".$l;
		$sql_change_id="alter table t_big change ".$$va."_id ".$va."_id char(10) not null;";
		$mysql->runSql($sql_change_id);
		$sql_change_name="alter table t_big change ".$$va."_name ".$va."_name char(20) not null;";
		$mysql->runSql($sql_change_name);
		$sql_change_ps="alter table t_big change ".$$va."_ps ".$va."_ps float not null;";
		$mysql->runSql($sql_change_ps);
		$sql_change_pf="alter table t_big change ".$$va."_pf ".$va."_pf float not null;";
		$mysql->runSql($sql_change_pf);
		$sql_change_ph="alter table t_big change ".$$va."_ph ".$va."_ph float not null;";
		$mysql->runSql($sql_change_ph);
		$sql_change_pl="alter table t_big change ".$$va."_pl ".$va."_pl float not null;";
		$mysql->runSql($sql_change_pl);
		$sql_change_ml="alter table t_big change ".$$va."_ml ".$va."_m float not null;";
		$mysql->runSql($sql_change_ml);
	}

          
          $mysql->closeDb();
        }

		function fetch_data($fetch_url,$str,$mysql_q,$day){
			for ($i=000; $i <=999; $i++) {                
				$sh600stock=$fetch_url.sprintf("%03d",$i);
				$fp=fopen($sh600stock, 'rb');
				$stockstring=fgets($fp,999);
				if(strlen($stockstring)>190){
					$array0=explode('"',$stockstring);
					$stock=explode(',',$array0[1]);
					$id=$str.sprintf("%03d",$i);
					$sql2="insert into ".$day." (".$day."_id, ".$day."_name, ".$day."_ps, ".$day."_pf, ".$day."_ph, ".$day."_pl, ".$day."_ml) values ('".$id."', '".iconv( 'GBK','UTF-8', $stock[0])."', ".$stock[1].", ".$stock[3].", ".$stock[4].", ".$stock[5].", ".($stock[8]/10000)." );";
                    $mysql_q->runSql($sql2);                  
                }
			}
		}
		
		function fetch_dex($fetch_url,$r,$mysql_q,$day){			
				$sh600stock=$fetch_url.$r['id'];
				$fp=fopen($sh600stock, 'rb');
				$stockstring=fgets($fp,999);
					$array0=explode('"',$stockstring);
					$stock=explode(',',$array0[1]);
					$sql2="insert into ".$r['id']." (date, name, ps, pf, ph, pl, ml) values ('".$day."', '".iconv( 'GBK','UTF-8', $stock[0])."', ".$stock[1].", ".$stock[3].", ".$stock[4].", ".$stock[5].", ".($stock[8]/10000)." );";
					$mysql_q->runSql($sql2);
			}
?>

