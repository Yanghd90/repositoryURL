<?php
function return_register_type( $REGISTER_TIME, $USER_ID ){
	// echo $REGISTER_TIME."  ".$USER_ID;
    // global $connection;//������2015��֮����Ҫ
    $PARA_ARRAY = get_sys_para( "DUTY_INTERVAL_BEFORE1,DUTY_INTERVAL_AFTER1,DUTY_INTERVAL_BEFORE2,DUTY_INTERVAL_AFTER2" );
    while ( list( $PARA_NAME, $PARA_VALUE ) = each( $PARA_ARRAY ) )
    {
        $$PARA_NAME = $PARA_VALUE;
    }
    $query = "SELECT c.DUTY_TYPE,b.DUTY_NAME,b.GENERAL,b.DUTY_TIME1,b.DUTY_TIME2,b.DUTY_TIME3,b.DUTY_TIME4,b.DUTY_TIME5,b.DUTY_TIME6\n\t from USER a\n\t left join user_ext c on a.user_id = c.user_id\n\t left join ATTEND_CONFIG b on c.DUTY_TYPE=b.DUTY_TYPE\n\t where a.USER_ID='".$USER_ID."'";
    // $cursor = exequery( $connection, $query );
	$cursor = exequery( TD::conn(), $query );
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
        if ( strtotime( $DUTY_TIME1 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME1 ) + $DUTY_INTERVAL_AFTER1 * 60 ){
            $REGISTER_TYPE = 1;
        }
        if ( strtotime( $DUTY_TIME2 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME2 ) + $DUTY_INTERVAL_AFTER2 * 60 ){
            $REGISTER_TYPE = 2;
        }
        if ( strtotime( $DUTY_TIME3 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME3 ) + $DUTY_INTERVAL_AFTER2 * 60 ){
            $REGISTER_TYPE = 3;
        }
        if ( strtotime( $DUTY_TIME4 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME4 ) + $DUTY_INTERVAL_AFTER2 * 60 ){
            $REGISTER_TYPE = 4;
        }
        if ( strtotime( $DUTY_TIME5 ) - $DUTY_INTERVAL_BEFORE1 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME5 ) + $DUTY_INTERVAL_AFTER2 * 60 ){
            $REGISTER_TYPE = 5;
        }
        if ( strtotime( $DUTY_TIME6 ) - $DUTY_INTERVAL_BEFORE2 * 60 <= strtotime( $REGISTER_TIME ) && strtotime( $REGISTER_TIME ) <= strtotime( $DUTY_TIME6 ) + $DUTY_INTERVAL_AFTER2 * 60 ){
            $REGISTER_TYPE = 6;
        }
    }
    return $REGISTER_TYPE;
}

include_once( "auth.php" );
include_once( "inc/utility_all.php" );
ob_end_clean( );
// ��ȡ���ڷ�ʽ���жϣ�DUTY_MACHINE��1���ڻ���0�ֶ����ڣ�2�Զ����ڣ�
$PARA_ARRAY = get_sys_para( "DUTY_MACHINE" );
$DUTY_MACHINE = $PARA_ARRAY['DUTY_MACHINE'];
// echo $DUTY_MACHINE;exit;
if ( $DUTY_MACHINE != 1 ){
    echo "+OK";
    exit( );
}

/*
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
*/
/*
*	����ͬ���пصĴ��룬��SYNC_DUTY_MACHINE_TIME��ͬ��ʱ��ڵ㣩Ϊ��������д�������ͬ��
*��2016-9-30 10:48:54�������⣺
*
*���⣺�д�������©
*ԭ���ж���ʱ���£���俼������뿼�ڻ�ͬ����OAϵͳ���俼�ڻ�sqlserver���ݿ�ͬ����֮�����ʱ��
*�޸����޸�ԭ�е��ж���������ʱ�䣩����SqlServer���ݿ⿼�����ݱ��id��������Ϊ����
*
*��ʱ�벻����ʲô©�����޸�֮������һ��ʱ�俴Ч����
*
*
*/

/*
// ��ѯ����SYNC_DUTY_MACHINE_TIME��ͬ��ʱ��ڵ㣩��û��ʱ����
$query = "select PARA_VALUE from SYS_PARA where PARA_NAME='SYNC_DUTY_MACHINE_TIME' ";
$cursor = exequery( $connection, $query );
if ( $ROW = mysql_fetch_array( $cursor ) ){
    $LAST_EXEC = $ROW['PARA_VALUE'];
}else{
    $LAST_EXEC = date( "Y-m-d H:i:s", time( ) - 5184000 );
    $query = "insert into SYS_PARA (PARA_NAME,PARA_VALUE)values('SYNC_DUTY_MACHINE_TIME','".$LAST_EXEC."') ";
    exequery( $connection, $query );
}
*/

// ��ѯ����SYNC_DUTY_ID������û��ʱ����
$query = "select PARA_VALUE from SYS_PARA where PARA_NAME='SYNC_DUTY_ID' ";
// $cursor = exequery( $connection, $query );
$cursor = exequery(TD::conn(), $query);
if ( $ROW = mysql_fetch_array( $cursor ) ){
    $LAST_EXEC = $ROW['PARA_VALUE'];
}else{
    $LAST_EXEC = "1";
    $query = "insert into SYS_PARA (PARA_NAME,PARA_VALUE)values('SYNC_DUTY_ID','".$LAST_EXEC."') ";
    // exequery( $connection, $query );
	exequery(TD::conn(), $query);
}

$LAST_EXEC_NEW = $LAST_EXEC;
// echo $LAST_EXEC_NEW;exit;
// ���ƿ��������ݿ�ΪSqlServer

// ���ӿ��ڻ����ݿ�sqlserver,php3+���ӷ�ʽ��
 $serverName = "192.168.10.212";
 $connectionInfo =  array("UID"=>"sa","PWD"=>"dcn123456.","Database"=>"hhtct");
 $conn = sqlsrv_connect( $serverName, $connectionInfo);
 if( $conn ){
    // echo "Connection established.\n";
 }else{
    echo "Connection could not be established.\n";
	// die( var_dump(sqlsrv_errors()));
}

// $server ="192.168.10.212";  //������IP��ַ,����Ǳ��أ�����д��localhost
// $uid ="sa";  //�û���
// $pwd ="dcn123456."; //����
// $database ="hhtct";  //���ݿ�����
// $conn3 = mssql_connect($server,$uid,$pwd) or die ("connect failed");//�������ݿ�����
// mssql_select_db($database,$conn3);
// echo "+OK";exit;
 
//ִ�в�ѯ���
$query = "select * from App_AttLog where ID >='".$LAST_EXEC_NEW."' order by ID";
// echo $query;exit;
$res = sqlsrv_query( $conn, $query, $params);
// print_r($res);exit;
// $res = sqlsrv_query($query);
//��ӡ�����ѯ���
while($ROW = sqlsrv_fetch_array($res,SQLSRV_FETCH_ASSOC)){
	// print_r($ROW);exit;
	// 2017-2-28 20:18:27����ע�͵���һ�д�ӡ����ע�ͣ�$CHECKTIME����ȡ������Ī�����
	
	// 2017-7-26 17:02:07ͨ����������ķ�ʽ�����ǲ��ܽ������
	// $datetime = new DateTime();
	// $datetime = $ROW['AttDateTime'];	
	// $CHECKTIME = $datetime->{'date'}; //��ȡ����
	
	// 2018��1��10��16:03:23ͨ��ǿ��ת�������飬Ȼ���ȡ���ԣ��������
	$AttDateTime = (array)$ROW['AttDateTime'];
	$CHECKTIME = $AttDateTime['date'];
	// print_r($ROW['AttDateTime']);
	// $CHECKTIME = $ROW['AttDateTime']->{'date'};
	// echo $CHECKTIME;exit;
	$id = $ROW['ID'];
	$CardID = $ROW['CardID'];
	$SENSORID = $ROW['DeviceId'];
	if(strlen($CardID) == 4){
		$CardID = "0".$CardID;
	}
	$USER_ID = $CardID;
	// �㲻����±ߵ�if�к����壬������Ϊ�����壬��ʱ���Ű�2016-9-30 11:30:03
	if ( $LAST_EXEC_NEW == "" || $LAST_EXEC_NEW <= $id){
		$LAST_EXEC_NEW = $id;
	}
	$query1 = "SELECT DUTY_TYPE from USER_EXT where USER_ID ='".$USER_ID."'";
	// $cursor1 = exequery( $connection, $query1 );
	$cursor1 = exequery(TD::conn(), $query1);
	if ( $ROW1 = mysql_fetch_array( $cursor1 ) ){
		$DUTY_TYPE = $ROW1['DUTY_TYPE'];
		$DUTY_TABLE = "";
		if ( $DUTY_TYPE == 99 ){
			$query2 = "insert into ATTEND_DUTY_SHIFT(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK) values('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "���ڻ�" )."')";
			// exequery( $connection, $query2 );
			exequery(TD::conn(), $query2);
		}else{
			$REGISTER_TYPE = return_register_type( $CHECKTIME, $USER_ID );
			if ( $REGISTER_TYPE != "" ){
				$query_tmp = "SELECT REGISTER_TIME from ATTEND_DUTY where USER_ID='".$USER_ID."' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
				// $cursor_tmp = exequery( $connection, $query_tmp );
				$cursor_tmp = exequery(TD::conn(), $query_tmp);
				if ( !( $ROW_tmp = mysql_fetch_array( $cursor_tmp ) ) ){
					$query_tmp = "insert into ATTEND_DUTY(USER_ID,REGISTER_TYPE,REGISTER_TIME,REGISTER_IP,REMARK,DUTY_TYPE) values ('".$USER_ID."','{$REGISTER_TYPE}','{$CHECKTIME}','{$SENSORID}','"._( "���ڻ�" ).( "','".$DUTY_TYPE."')" );
					// echo $query_tmp;
					// exequery( $connection, $query_tmp );
					exequery( TD::conn(), $query_tmp );
				}else{
					$tmp_REGISTER_TIME = $ROW_tmp['REGISTER_TIME'];
					if ( $REGISTER_TYPE % 2 == 0 && strtotime( $tmp_REGISTER_TIME ) < strtotime( $CHECKTIME ) ){
						$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
						// exequery( $connection, $query_tmp );
						exequery( TD::conn(), $query_tmp );
					}else if ( !( $REGISTER_TYPE % 2 != 0 ) && !( strtotime( $CHECKTIME ) < strtotime( $tmp_REGISTER_TIME ) ) ){
						$query_tmp = "update ATTEND_DUTY set REGISTER_TIME='".$CHECKTIME."',REGISTER_IP='{$SENSORID}' where USER_ID='{$USER_ID}' and REGISTER_TYPE='{$REGISTER_TYPE}' and to_days(REGISTER_TIME)=to_days('{$CHECKTIME}')";
						// exequery( $connection, $query_tmp );
						exequery( TD::conn(), $query_tmp );
					}
				}
				// echo $query_tmp;exit;
			}
		}
	}
}
sqlsrv_close( $conn);
$query = "update SYS_PARA set PARA_VALUE='".$LAST_EXEC_NEW."' where PARA_NAME='SYNC_DUTY_ID' ";
exequery( TD::conn(), $query );

update_office_task($TASK_ID, "1", date("Y-m-d H:i:s", time()));
echo "+OK";
?>
