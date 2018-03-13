<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

// 判断打卡类型（一天内第几次打卡），可以根据此参数判断，在数据同步时是insert？还是update？
function return_register_type( $REGISTER_TIME, $USER_ID ){
	global $connection;
	$PARA_ARRAY=get_sys_para("DUTY_INTERVAL_BEFORE1,DUTY_INTERVAL_AFTER1,DUTY_INTERVAL_BEFORE2,DUTY_INTERVAL_AFTER2" );
	while ( list( $PARA_NAME, $PARA_VALUE ) = each( &$PARA_ARRAY ) ){
		$$PARA_NAME = $PARA_VALUE;
	}
	$query = "SELECT c.DUTY_TYPE,b.DUTY_NAME,b.GENERAL,b.DUTY_TIME1,b.DUTY_TIME2,b.DUTY_TIME3,b.DUTY_TIME4,b.DUTY_TIME5,b.DUTY_TIME6 /* ,b.DUTY_TIME7,b.DUTY_TIME8 */ from USER a left join user_ext c on a.user_id = c.user_id left join ATTEND_CONFIG b on c.DUTY_TYPE=b.DUTY_TYPE where a.USER_ID='".$USER_ID."'";

	$cursor = exequery( $connection, $query );
	if ( $ROW = mysql_fetch_array( $cursor ) ){
		$DUTY_TYPE = $ROW['DUTY_TYPE'];
		$DUTY_NAME = $ROW['DUTY_NAME '];
		$GENERAL = $ROW['GENERAL'];
		$DUTY_TIME1 = $ROW['DUTY_TIME1'];
		$DUTY_TIME2 = $ROW['DUTY_TIME2'];
		$DUTY_TIME3 = $ROW['DUTY_TIME3'];
		$DUTY_TIME4 = $ROW['DUTY_TIME4'];
		$DUTY_TIME5 = $ROW['DUTY_TIME5'];
		$DUTY_TIME6 = $ROW['DUTY_TIME6'];
		//	$DUTY_TIME7 = $ROW['DUTY_TIME7'];
		//	$DUTY_TIME8 = $ROW['DUTY_TIME8'];
	}
	if ( $REGISTER_TIME != "" ){
		$timearray = explode( " ", $REGISTER_TIME );
		$time = $timearray[0];
		$DUTY_TIME1 = $time." ".$DUTY_TIME1;
		$DUTY_TIME2 = $time." ".$DUTY_TIME2;
		$DUTY_TIME3 = $time." ".$DUTY_TIME3;
		$DUTY_TIME4 = $time." ".$DUTY_TIME4;
		$DUTY_TIME5 = $time." ".$DUTY_TIME5;
		$DUTY_TIME6 = $time." ".$DUTY_TIME6;
		//	$DUTY_TIME7 = $time." ".$DUTY_TIME7;
		//	$DUTY_TIME8 = $time." ".$DUTY_TIME8;
					
		if ( strtotime( $DUTY_TIME1 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME1 ) + $DUTY_INTERVAL_AFTER1 * 60 ){
				$REGISTER_TYPE = 1;
		}
		if ( strtotime( $DUTY_TIME2 ) - $DUTY_INTERVAL_BEFORE2*60<=strtotime($REGISTER_TIME)&&strtotime($REGISTER_TIME ) <= strtotime( $DUTY_TIME2 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 2;
		}
		if ( strtotime( $DUTY_TIME3 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME3 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 3;
		}
		if ( strtotime( $DUTY_TIME4 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME4 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 4;
		}
		if ( strtotime( $DUTY_TIME5 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME5 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 5;
		}
		if ( strtotime( $DUTY_TIME6 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME6 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 6;
		}
		/*	if ( strtotime( $DUTY_TIME7 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME7 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 7;
		}
		if ( strtotime( $DUTY_TIME8 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME8 ) + $DUTY_INTERVAL_AFTER2 * 60 )
		{
			$REGISTER_TYPE = 8;
		}	*/

	}
	return $REGISTER_TYPE;
}

include_once( "./auth.php" );
include_once( "inc/utility_all.php" );
ob_end_clean( );
$PARA_ARRAY = get_sys_para( "DUTY_MACHINE" );
$DUTY_MACHINE = $PARA_ARRAY['DUTY_MACHINE'];
if ( $DUTY_MACHINE != 1 ){
	echo "+OK";
	exit( );
}
$query = "SELECT MACHINEID,MACHINE_BRAND,DATABASE_TYPE,ACCESS_PATH,DATABASE_IP,DATABASE_PORT,DATABASE_USER,DATABASE_PASS,DUTY_TABLE,DUTY_USER,DUTY_TIME,DATABASE_NAME from ATTEND_MACHINE where MACHINEID=1";
$cursor = exequery( $connection, $query );
if ( $ROW = mysql_fetch_array( $cursor ) ){
	$MACHINEID = $ROW['MACHINEID'];
	$MACHINE_BRAND = $ROW['MACHINE_BRAND'];
	$DATABASE_TYPE = $ROW['DATABASE_TYPE'];
	$ACCESS_PATH = $ROW['ACCESS_PATH'];
	$DATABASE_IP = $ROW['DATABASE_IP'];
	$DATABASE_PORT = $ROW['DATABASE_PORT'];
	$DATABASE_USER = $ROW['DATABASE_USER'];
	$DATABASE_PASS = $ROW['DATABASE_PASS'];
	$DUTY_TABLE = $ROW['DUTY_TABLE'];
	$DUTY_USER = $ROW['DUTY_USER'];
	$DUTY_TIME = $ROW['DUTY_TIME'];
	$DATABASE_NAME = $ROW['DATABASE_NAME'];
}
$query = "select PARA_VALUE from SYS_PARA where PARA_NAME='SYNC_DUTY_MACHINE_TIME' ";
$cursor = exequery( $connection, $query );
if ( $ROW = mysql_fetch_array( $cursor ) ){
	$LAST_EXEC = $ROW['PARA_VALUE'];
	// $LAST_EXEC = "2016-06-22 15:02:54";
}else{
	$LAST_EXEC = date( "Y-m-d H:i:s", time( ) - 5184000 );
	$query = "insert into SYS_PARA (PARA_NAME,PARA_VALUE)values('SYNC_DUTY_MACHINE_TIME','".$LAST_EXEC."') ";
	exequery( $connection, $query );
}
if ( $MACHINE_BRAND == "ZK_iclock660" && $DATABASE_TYPE == "access" && !file_exists( $ACCESS_PATH ) ){
	echo "+OK";
	exit( );
}
$LAST_EXEC_NEW = $LAST_EXEC;

if ( $MACHINE_BRAND == "ZK_iclock660" && $DATABASE_TYPE == "access" ){
	$conn = new COM( "ADODB.Connection");
	$connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=".realpath( "{$ACCESS_PATH}" );
	$conn->Open( $connstr );
	$rs = new COM( "ADODB.RecordSet");
	//2015-09-09删除了sql语句where后边的条件 CHECKTYPE<>'1' and 
	$query = "select SENSORID,USERID,format(CHECKTIME,'yyyy-mm-dd hh:nn:ss') as CHECKTIME1 from CHECKINOUT where format(CHECKTIME,'yyyy-MM-dd hh:nn:ss') > '".$LAST_EXEC."' order by CHECKTIME desc";
	$rs->Open( $query, $conn, 1, 1 );
	//输出从考勤原始数据表读出的条数
	// echo"共有:".$rs->RecordCount."条记录结果!";
	// exit;
	$CUR_TIME = date( "Y-m-d H:i:s", time( ) );
	while ( !$rs -> eof ){
		$SENSORID = $rs->Fields( "SENSORID" )->value;
		$USERID = $rs->Fields( 1 )->value;
		$CHECKTIME = $rs->Fields['CHECKTIME1']->value;
							
		if ( $LAST_EXEC_NEW == "" || strtotime( $LAST_EXEC_NEW ) < strtotime( $CHECKTIME ) ){
			$LAST_EXEC_NEW = $CHECKTIME;
		}
// echo $LAST_EXEC_NEW;exit;
		$conn1 = new COM( "ADODB.Connection" );
		$conn1->Open( $connstr );
								
		$rs1 = new COM( "ADODB.RecordSet" );
		$query1 = "select Name from USERINFO where USERID=".$USERID;
		$rs1->Open( $query1, $conn1, 1, 1 );
								
		while ( !$rs1->eof ){												
			$USER_NAME = $rs1->Fields( "NAME" )->value;
			$rs1->MoveNext( );
		}
			
		$query1 = "SELECT USER.USER_ID,USER_EXT.DUTY_TYPE from USER,USER_EXT where USER.USER_ID=USER_EXT.USER_ID and USER_NAME='".$USER_NAME."'";
		$cursor1 = exequery( $connection, $query1 );
		if ( $ROW1 = mysql_fetch_array( $cursor1 ) ){
			$DUTY_TYPE = $ROW1['DUTY_TYPE'];
			$USER_ID = $ROW1['USER_ID'];
			$DUTY_TABLE = "";
			if ( $DUTY_TYPE == 99 ){
				$query2 = "insert into ATTEND_DUTY_SHIFT(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK) values('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "考勤机" )."')";
				exequery( $connection, $query2 );
			}else{
				$REGISTER_TYPE = return_register_type( $CHECKTIME, $USER_ID );
				if ( $REGISTER_TYPE != "" ){
					$query_tmp = "SELECT REGISTER_TIME from ATTEND_DUTY where USER_ID='".$USER_ID."' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
					$cursor_tmp = exequery( $connection, $query_tmp );
					if ( !( $ROW_tmp = mysql_fetch_array( $cursor_tmp ) ) ){
						$query_tmp = "insert into ATTEND_DUTY(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK,DUTY_TYPE) values ('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "考勤机" ).( "','".$DUTY_TYPE."')" );
						exequery( $connection, $query_tmp );
					}else{
						$tmp_REGISTER_TIME = $ROW_tmp['REGISTER_TIME'];
						if ( $REGISTER_TYPE % 2 == 0 && strtotime( $tmp_REGISTER_TIME ) < strtotime( $CHECKTIME ) ){
							$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
							exequery( $connection, $query_tmp );
						}else if ( $REGISTER_TYPE % 2 != 0 && strtotime( $CHECKTIME ) < strtotime( $tmp_REGISTER_TIME ) ){
							$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
							exequery( $connection, $query_tmp );
						}
					}
				}
			}
		}
		$rs->MoveNext( );
	}
}
if ( $MACHINE_BRAND == "ZK_iclock660" && $DATABASE_TYPE == "mysql" ){
	$conn_mysql = mysql_connect( "{$DATABASE_IP}:{$DATABASE_PORT}", "{$DATABASE_USER}", "{$DATABASE_PASS}" );
	if ( !$conn_mysql ){
		echo "-ERR "._( "无法连接考勤机数据库服务，请检查数据连接参数" );
		exit( );
	}
	$db_selected = mysql_select_db( "{$DATABASE_NAME}", $conn_mysql );
	if ( !$db_selected ){
		echo "-ERR "._( "未找到考勤机数据库，请确认数据库名是否设置正确" );
		exit( );
	}
	mysql_query( "SET NAMES GBK" );
	$query = "select SENSORID,".$DUTY_USER.",{$DUTY_TIME} from CHECKINOUT where CHECKTYPE<>'1' and CHECKTIME > '{$LAST_EXEC}' order by CHECKTIME desc";
	$CUR_TIME = date( "Y-m-d H:i:s", time( ) );
	$cursor = exequery( $conn_mysql, $query );
	while ( $ROW = mysql_fetch_array( $cursor ) ){
		$SENSORID = $ROW['SENSORID'];
		$USERID = $ROW["{$DUTY_USER}"];
		$CHECKTIME = $ROW["{$DUTY_TIME}"];
		if ( $LAST_EXEC_NEW == "" || strtotime( $LAST_EXEC_NEW ) < strtotime( $CHECKTIME ) ){
			$LAST_EXEC_NEW = $CHECKTIME;
		}
		$query1 = "select NAME from USERINFO where USERID=".$USERID;
		$cursor1 = exequery( $conn_mysql, $query1 );
		if ( $ROW = mysql_fetch_array( $cursor1 ) ){
			$USER_NAME = $ROW['NAME'];
		}
		$query1 = "SELECT USER.USER_ID,USER_EXT.DUTY_TYPE from USER,USER_EXT where USER.USER_ID=USER_EXT.USER_ID and USER_NAME='".$USER_NAME."'";
		$cursor1 = exequery( $connection, $query1 );
		if ( $ROW1 = mysql_fetch_array( $cursor1 ) ){
			$DUTY_TYPE = $ROW1['DUTY_TYPE'];
			$USER_ID = $ROW1['USER_ID'];
			$DUTY_TABLE = "";
			if ( $DUTY_TYPE == 99 ){
				$query2 = "insert into ATTEND_DUTY_SHIFT(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK) values('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "考勤机" )."')";
				exequery( $connection, $query2 );
			}else{
				$REGISTER_TYPE = return_register_type( $CHECKTIME, $USER_ID );
				if ( $REGISTER_TYPE != "" ){
					$query_tmp = "SELECT REGISTER_TIME from ATTEND_DUTY where USER_ID='".$USER_ID."' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
					$cursor_tmp = exequery( $connection, $query_tmp );
					if ( !( $ROW_tmp = mysql_fetch_array( $cursor_tmp ) ) ){
						$query_tmp = "insert into ATTEND_DUTY(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK,DUTY_TYPE) values ('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "考勤机" ).( "','".$DUTY_TYPE."')" );
						exequery( $connection, $query_tmp );
					}else{
						$tmp_REGISTER_TIME = $ROW_tmp['REGISTER_TIME'];
						if ( $REGISTER_TYPE % 2 == 0 && strtotime( $tmp_REGISTER_TIME ) < strtotime( $CHECKTIME ) ){
							$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
							exequery( $connection, $query_tmp );
						}else if ( !( $REGISTER_TYPE % 2 != 0 ) && !( strtotime( $CHECKTIME ) < strtotime( $tmp_REGISTER_TIME ) ) ){
							$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
							exequery( $connection, $query_tmp );
						}
					}
				}
			}
		}
	}
	mysql_close( $conn_mysql );
}
$query = "update SYS_PARA set PARA_VALUE='".$LAST_EXEC_NEW."' where PARA_NAME='SYNC_DUTY_MACHINE_TIME' ";
exequery( $connection, $query );
echo "+OK";
?>
