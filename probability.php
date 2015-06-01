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
$pf=$today."_pf";
$ph=$today."_ph";
$mysql = new SaeMysql();
$sql_select_day="select * from chosenday order by name";
$days=$mysql->getData( $sql_select_day );
for($i=count($days)-2;$i>=count($days)-17;$i--){
	$table1=$days[$i]['name'];
	$sql_alter="alter table ".$table1. " add (".$pf." float not null, ".$ph." float not null)";
	$mysql->runSql($sql_alter);
	$sql_select_table="select * from ".$table1;
	$arr=$mysql->getData( $sql_select_table );
	$n_days=(count($arr[0])-6)/2;
	for($j=0;$j<count($arr);$j++){
		$r=$arr[$j];
		$sql_select_data="select ".$pf.", ".$ph." from ".$today." where ".$today."_id = '".$r['iid']."';";
		$arr_data=$mysql->getData( $sql_select_data);
		$pfph=$arr_data[0];
		$sql_update_s="update ".$table1." set ".$pf." = ".number_format((($pfph[$pf]-$r['start'])/$r['start']),3)." , ".$ph." = ".number_format((($pfph[$ph]-$r['start'])/$r['start']),3)." where id = '".$r['id']."';";
		$mysql->runSql($sql_update_s);
	}
	//计算概率
	if($n_days < 16){
		$sql_alter_chance="alter table chance_".$n_days." add ( ".$pf." float, ".$ph." float, ".$pf."_p float, ".$ph."_p float )";
		$mysql->runSql($sql_alter_chance);
		$sql_select_code="select distinct code from ".$table1;
		$code_select=$mysql->getData( $sql_select_code );
		for($k=0;$k<count($code_select);$k++){
			$co=$code_select[$k]['code'];
			$sql_select_phpf="select * from ".$table1." where code = ".$co;
			$code_data=$mysql->getData( $sql_select_phpf);
			$n_code=count($code_data);
			$n_ph=0;$n_pf=0;$ph_av=0.0;$pf_av=0.0;
			for($l=0;$l<count($code_data);$l++){
				$ph_av=$ph_av+$code_data[$l][$ph];
				$pf_av=$pf_av+$code_data[$l][$pf];
				if($code_data[$l][$ph]>0.01*$n_days){
					$n_ph++;
				}
				if($code_data[$l][$pf]>0.01*$n_days){
					$n_pf++;
				}
			}
			$peidui=0;
			$sql_select_r_code="select code from chance_".$n_days;
			$code_re=$mysql->getData( $sql_select_r_code);
			for($m=0;$m<count($code_re);$m++){
				if($code_re[$m]['code']==$co){
					$sql_update_code="update chance_".$n_days." set ".$pf." = ".number_format($pf_av/$n_code,3)." , ".$ph." = ".number_format($ph_av/$n_code,3)." , ".$pf."_p = ".number_format($n_pf/$n_code,3)." , ".$ph."_p = ".number_format($n_ph/$n_code,3)." where code = ".$co.";";
					$mysql->runSql($sql_update_code);
					$peidui=1;
				}
			}
			if($peidui==0){
				$sql_insert_code="insert into chance_".$n_days." (code, ".$pf.", ".$ph.", ".$pf."_p, ".$ph."_p ) values ( ".$co." ,".number_format($pf_av/$n_code,3).", ".number_format($ph_av/$n_code,3).", ".number_format($n_pf/$n_code,3).", ".number_format($n_ph/$n_code,3)." );";
				$mysql->runSql($sql_insert_code);
			}
		}
	}	
}


$mysql->closeDb();
}
?>