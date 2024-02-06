<?php
	try{
		$conn = mysqli_connect("localhost", "root", "", "rpg_legacy_final");

		ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');

		$from = trim(mysqli_real_escape_string($conn,$_GET['from']));
		$to = trim(mysqli_real_escape_string($conn,$_GET['to']));
		if(is_numeric($from) && is_numeric($to)){
			$claim_sql = "select * from claim_tx_desc_v1 where  transaction_type = 'Responsibility' and claim_id >= '$from' and claim_id <= '$to' and DATE(created_at) <= '2019-10-02' order by claim_id asc";
			$claimRes = mysqli_query($conn, $claim_sql);
			foreach($claimRes as $row){	
				$pmt_res = mysqli_fetch_array(mysqli_query($conn, "select id from pmt_claim_tx_v1 where payment_id = '".$row['payment_id']."'"));
				if(!empty($pmt_res)){
					(mysqli_query($conn, "delete from pmt_claim_tx_v1 where payment_id = '".$row['payment_id']."'"));
					(mysqli_query($conn, "delete from pmt_claim_cpt_tx_v1 where payment_id = '".$row['payment_id']."'"));
					$pmt_info = mysqli_fetch_array(mysqli_query($conn, "select pmt_mode_id from pmt_info_v1 where id = '".$row['payment_id']."'"));
					(mysqli_query($conn, "delete from pmt_info_v1 where id = '".$row['payment_id']."'"));
					(mysqli_query($conn, "delete from pmt_check_info_v1 where id = '".$pmt_info['pmt_mode_id']."'"));
					
					(mysqli_query($conn, "update claim_tx_desc_v1 set payment_id = 0 where id = '".$row['id']."'"));
					(mysqli_query($conn, "update claim_cpt_tx_desc_v1 set payment_id = 0 where claim_tx_desc_id = '".$row['id']."'"));
				}
			}	
		}else{
			echo 'Parameters value is wrong';
		}
	} catch(Exception $e) {
		echo "Error Msg: ".$e->getMessage();
	}
?>