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
	//交易量的比例
	$now = getdate();
	  	$h=$now[hours];
	  	$min=$now[minutes];
	  	$clickTime=0;
	  	if($h<=11 && $h>=9){
	  		$clickTime=($h-9.5)*60+$min;
	  	} else if ($h>=13 && $h<15){
	  		$clickTime = ($h-13)*60+$min+120;
	  	} else if ($h>=15){
	  		$clickTime = 240;
	  	}
	  	$lum=$clickTime/240;
	  	$lume=number_format($lum,3);

	$mysql = new SaeMysql();
	$sql_insert_chosenday = "insert into chosenday ( name) values ('s_".$today."');";
	$mysql->runSql($sql_insert_chosenday);

	$sql_drop_sday="drop table s_".$today;
	$mysql->runSql($sql_drop_sday);


	$sql_create_stocks="create table s_".$today." ( id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, iid char(10) not null, name char(20) not null,picture char(10) not null, code int unsigned not null, start float not null );";
	$mysql->runSql($sql_create_stocks);
	$url_1="http://hq.sinajs.cn/list=";
	$sql_select_big="select * from t_big";
	$big = $mysql->getData( $sql_select_big );
	for($i=0;$i<count($big);$i++){
		$r=$big[$i];
		$s = get_data($url_1, $r['t0_id']);
       if ($s && $s['c_now']<$s['c_ps'] && $r['t0_pf']<$r['t1_pf']&& $r['t0_pf']<$r['t0_ps'] && $r['t1_pf']<$r['t2_pf'] && $r['t0_m']<$r['t1_m'] && $r['t1_m']<$r['t2_m'] && $s['c_m']<0.9*$r['t0_m']*$lume){
			$die3=2000000;
			if($s['c_ph']-$s['c_pl']>$r['t0_ph']-$r['t0_pl']){$die3=$die3+7;}
			if($r['t1_pf']<$r['t1_ps']){$die3=$die3+20;}
			if(($s['c_ps']-$s['c_now'])/($s['c_ph']-$s['c_pl'])<0.2){$die3=$die3+5000;}
			insert_stocks($r['t0_id'], $mysql, "跌3", $die3, $r['t0_name'], $today,$s['c_now']);
		}
		if($s && $r['t0_ph']!=$r['t0_pl'] && $s['c_now']>$s['c_ps'] && ($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.08){
			//小红
			$min1=min($s['c_m']/$lume,$r['t0_m'],$r['t1_m'],$r['t2_m'],$r['t3_m'],$r['t4_m'],$r['t5_m'],$r['t6_m']);
			if(($r['t0_m']==$min1 || $r['t1_m']==$min1) && $s['c_now']>$r['t0_pf'] && $r['t1_pf']<$r['t1_ps']){
				$xiaohong=80000000;
				if(haoxingtai($s)){$xiaohong=$xiaohong+1;}
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$xiaohong=$xiaohong+20;}
				insert_stocks($r['t0_id'], $mysql, "小红", $xiaohong, $r['t0_name'], $today,$s['c_now']);
			}
			//小红绿红
			$min2=min($r['t1_m'],$r['t2_m'],$r['t3_m'],$r['t4_m'],$r['t5_m'],$r['t6_m'],$r['t7_m'],$r['t8_m']);
			$min3=min($r['t2_m'],$r['t3_m'],$r['t4_m'],$r['t5_m'],$r['t6_m'],$r['t7_m'],$r['t8_m']);
			if( ($r['t0_pf']<$r['t1_pf'] && $r['t1_pf']>$r['t2_pf'] && ($r['t2_m']==$min2 )&& $r['t3_pf']<$r['t3_ps'] && $r['t1_m']>1.6*$r['t2_m'])
				||($r['t0_pf']<$r['t1_pf'] && $r['t1_pf']<$r['t2_pf'] && $r['t2_pf']>$r['t3_pf'] && ($r['t3_m']==$min3 ) && $r['t4_pf']<$r['t4_ps'] && $r['t2_m']>1.6*$r['t3_m'])
			){
				$xhlh=90000000;
                if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']>0.01){$xhlh=$xhlh+900000;}
				if(haoxingtai($s)){$xhlh=$xhlh+1; }
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$xhlh=$xhlh+20;}
                if($s['c_m']/$lume>$r['t0_m']){$xhlh=$xhlh+300;}
				insert_stocks($r['t0_id'], $mysql, "小红绿红", $xhlh, $r['t0_name'], $today,$s['c_now']);
			}
			//起风
			$average=($r['t0_m']+$r['t1_m']+$r['t2_m']+$r['t3_m']+$r['t4_m']+$r['t5_m']+$r['t6_m'])/7;
			if($s['c_m']/$lume>$average * 2 && $s['c_now']>$r['t0_pf']){
				$fangliang=550000000;
                if($s['c_m']/$lume>1.7*$r['t0_m']){$fangliang=$fangliang+500000;}
                if($r['t0_pf']<$r['t0_ps']||$r['t1_pf']<$r['t1_ps']){$fangliang=$fangliang+5000000;}
				if(haoxingtai($s)){$fangliang=$fangliang+1; }
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$fangliang=$fangliang+20;}
				insert_stocks($r['t0_id'], $mysql, "起风", $fangliang, $r['t0_name'], $today,$s['c_now']);
			}
			//反转
			if(xiajiang($r)
			&& $s['c_ps']<$r['t0_pf'] && $s['c_now']>($r['t0_ps']+$r['t0_pf'])/2 && ($r['t0_ps']-$r['t0_pf'])/$r['t0_pf']>0.01){
				$fanzhuan=70000000;
				
				if(haoxingtai($s)){$fanzhuan=$fanzhuan+1;}
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$fanzhuan=$fanzhuan+20;}
				if($s['c_m']/$lume>$r['t0_m']){$fanzhuan=$fanzhuan+300;}
				if($r['t1_m']<$r['t2_m']){ $fanzhuan=$fanzhuan+4000;}
				if($s['c_m']/$lume<2*$r['t0_m']){$fanzhuan=$fanzhuan+50000;}
				insert_stocks($r['t0_id'], $mysql, "反转", $fanzhuan, $r['t0_name'], $today,$s['c_now']);
			}
			//启明
			if(xiajiang($r)
			&& ((($r['t0_pf'] - $r['t0_ps'])*($r['t0_pf'] - $r['t0_ps']))/(($r['t0_ph'] - $r['t0_pl'])*($r['t0_ph'] - $r['t0_pl'])) < 0.04 )
			&& ($r['t1_ps']-$r['t1_pf'])/$r['t1_pf']>0.01
			&& $r['t0_pl']<$r['t1_pl']
			&& $r['t0_ps']+$r['t0_pf']<$r['t1_ps']+$r['t1_pf']
			&& $s['c_now']>$r['t1_pf'] && ($s['c_now']-$s['c_ps'])/$s['c_ps']>0.01 && $s['c_pl']>$r['t0_pl']
			){
				$qiming=60000000;
				
				if(haoxingtai($s)){$qiming=$qiming+1;}
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$qiming=$qiming+20;}
				if($s['c_m']/$lume>$r['t0_m']){$qiming=$qiming+300;}
				if($r['t1_m']<$r['t2_m']){ $qiming=$qiming+4000;}
				if($s['c_m']/$lume<2*$r['t0_m']){$fanzhuan=$fanzhuan+50000;}
				if($r['t0_ps']<$r['t1_pf']){$qiming=$qiming+6000000;}
				if($s['c_ps']>$r['t0_pf']){$qiming=$qiming+600000;}
				insert_stocks($r['t0_id'], $mysql, "启明", $qiming, $r['t0_name'], $today,$s['c_now']);
			}
			//惯性
			if($s['c_m']/$lume>$r['t0_m']
			&& ($s['c_now']-$s['c_ps'])*($s['c_now']-$s['c_ps'])>($r['t0_pf']-$r['t0_ps'])*($r['t0_pf']-$r['t0_ps'])
			&& $s['c_pl']>$r['t0_pl']
			&& ($s['c_now']-$r['t0_pf'])/$r['t0_pf']>0.01){
				$guan=10000000;
				if(($s['c_now']-$r['t0_pf'])/$r['t0_pf']<0.05){$guan=$guan+20;}
				if($s['c_m']/$lume<2*$r['t0_m']){$guan=$guan+4000;}
				if(haoxingtai($s)){$guan=$guan+1;}
				if($s['c_ph']-$s['c_pl']>$r['t0_ph']-$r['t0_pl']){$guan=$guan+70000;}
				if($r['t0_pf']<$r['t0_ps'] || $r['t1_pf']<$r['t1_ps']){$guan=$guan+800000;}
				insert_stocks($r['t0_id'], $mysql, "惯性", $guan, $r['t0_name'], $today,$s['c_now']);
			}
		}		
	}
	$mysql->closeDb();
}
function get_data($url_get, $id_get){
	$url_now_ti=$url_get.$id_get;
	$fp_ti=fopen($url_now_ti, 'rb');
	$stockstring_ti=fgets($fp_ti,999);
	if(strlen($stockstring_ti)>190){
		$array0_ti=explode('"',$stockstring_ti);
		$stock_ti=explode(',',$array0_ti[1]);
		$now_array = array ('c_ps'=>$stock_ti[1], 'c_now'=>$stock_ti[3], 'c_ph'=>$stock_ti[4], 'c_pl'=>$stock_ti[5], 'c_m'=>($stock_ti[8]/10000));
		return $now_array;
	}else{
		return false;
	}
}
function haoxingtai($arr){
	if(
	($arr['c_now']-$arr['c_pl'])/($arr['c_ph']-$arr['c_pl'])>0.9
	|| (($arr['c_ph']-$arr['c_now']<$arr['c_ps']-$arr['c_pl']) && (($arr['c_now']-$arr['c_ps'])/($arr['c_ph']-$arr['c_pl'])>0.6))
	){
		return true;
	}else{
		return false;
	}
}
function insert_stocks($a1, $mysql_s,$picture, $code,$name,$today,$p_now){
	$sql_insert_stocks = "insert into s_".$today." (iid,name,picture,code,start) values  ('".$a1."', '".$name."', '".$picture."', ".$code.", ".$p_now.");";
	$mysql_s->runSql($sql_insert_stocks);
}
function xiajiang($arr_big){
	if(
	($arr_big['t1_ps']-$arr_big['t0_pf'])/$arr_big['t0_pf']>0.01
	&& (($arr_big['t2_ps']-$arr_big['t0_pf'])/$arr_big['t0_pf']>0.04
			|| ($arr_big['t3_ps']-$arr_big['t0_pf'])/$arr_big['t0_pf']>0.04
			|| ($arr_big['t4_ps']-$arr_big['t0_pf'])/$arr_big['t0_pf']>0.04
	)
	){
		return true;
	}else{
		return false;
	}
}
?>