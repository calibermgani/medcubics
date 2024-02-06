<?php
	try{
		$conn = mysqli_connect("localhost", "root", "", "rpg_old");

		ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        
		//$sql = "select TicketNum, SvcProcID, TransDesc, TransDate, TransAmt, InsName, TrsfFromInsName, TrsfToInsName from allscript_payments where TicketNum =590 order by id asc";

		// Group by trsanction type for use in claim transaction description
		// select *,sum(TransAmt) from allscript_payments where TicketNum = 590 group by TicketNum,TransDesc,TransDate,TransType 

		//$claim_sql = "select *,sum(TransAmt) from allscript_payments where TicketNum = 590 group by TicketNum,TransDesc,TransDate,TransType order by id asc";
		//$claimDesc = mysqli_query($conn, $claim_sql);
		//$sql_cpt = "SELECT * FROM `allscript_payments` WHERE `TicketNum` = '590' AND `TransDesc` = 'Medicare Payment' AND `TransDate` = '2018-07-24' AND `TransType` = 'P'";

		
		$claim_sql = "select *,sum(TransAmt) as txAmt from allscript_payments where (`TicketNum` > '1000' AND TicketNum <= '1500' )AND `TransDesc` != 'Rebilled Claim' group by TicketNum,TransDesc,TransDate,TransType order by id asc";
		$claimRes = mysqli_query($conn, $claim_sql);			
		$i=1;
		foreach($claimRes as $row){	
			$pmt_type = $pmt_method = $transaction_type = "";
			echo "<br>CL: ".$row['TicketNum'];
			if($row['TransDesc']=="Administrative Adjustment/Courtesy"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Aetna Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Aetna Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Aetna Transfer"){
				$pmt_type = "Payment";
				$transaction_type = "Responsibility";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
			} elseif($row['TransDesc']=="Arch Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Bad Debt Write Off"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Blue Cross Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Blue Cross Blue Shield Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Blue Cross Blue Shield Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Blue Cross Blue Shield Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Blue Cross Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Blue Shield Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Capitation Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Charity Discount per Facility Approval"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Cigna Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Cigna Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Cigna Transfer"){
				$pmt_type = "Payment";		
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Collection Agency Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Collection Agency Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Payment";
			} elseif($row['TransDesc']=="Commercial Insurance Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Commercial Insurance Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Commercial Insurance Refund"){
				$pmt_type = "Refund";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Refund";
			} elseif($row['TransDesc']=="Commercial Insurance Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Credit/Debit Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Deceased"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Greater Newport Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Humana Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Humana Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Humana Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Interest Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Interest Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Medicaid Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Medicaid HMO Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Medicaid HMO Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Medicaid Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Medicaid Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Medicare Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Medicare Advantage Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Medicare Advantage Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Medicare Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Medicare Transfer"){
				$pmt_type = "Payment";		
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Off Duty Military Provider Service"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Prior Deposit"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Rebilled Claim"){
				$pmt_type = "Payment";
				$pmt_method = "Patient";
				$transaction_type = "Resubmitted";
			} elseif($row['TransDesc']=="Self Pay Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Self Pay Cash Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Payment";
			} elseif($row['TransDesc']=="Self Pay Check Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Payment";
			} elseif($row['TransDesc']=="Self Pay Credit Card Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Payment";
			} elseif($row['TransDesc']=="Self Pay Small Balance Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Patient";
				$transaction_type = "Patient Adjustment";
			} elseif($row['TransDesc']=="Self Pay Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="SJHAP Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Tri City Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";		
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Tricare Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="TriCare Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="TriCare Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="United HealthCare Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="United HealthCare Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="United HealthCare Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="Vibra Bundled/Global  Service"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Withheld"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Work Comp Adjustment"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			} elseif($row['TransDesc']=="Work Comp Payment"){
				$pmt_type = "Payment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Payment";
			} elseif($row['TransDesc']=="Work Comp Transfer"){
				$pmt_type = "Payment";
				$pmt_method = ($row['TrsfToInsName']=="Patient") ? "Patient" : "Insurance";
				$transaction_type = "Responsibility";
			} elseif($row['TransDesc']=="WU Medicare Contractor Recovery"){
				$pmt_type = "Adjustment";
				$pmt_method = "Insurance";
				$transaction_type = "Insurance Adjustment";
			}

			$claim = mysqli_fetch_array(mysqli_query($conn, "select id, patient_id, total_charge,insurance_id from claim_info_v1 where id = '".$row['TicketNum']."' order by id asc limit 1"));

			$insurance_id = 0;
			if($row['InsName']!='' || ($row['InsName']!='' && $row['InsName']!='Patient')){
				$insurance = mysqli_fetch_array(mysqli_query($conn, "select id from insurances where insurance_name = '".$row['InsName']."' order by id asc limit 1"));
				$insurance_id = $insurance['id'];
			}elseif($row['InsName']=='' && $row['TrsfFromInsName']!='' && $row['TrsfFromInsName']!='Patient'){
				$insurance = mysqli_fetch_array(mysqli_query($conn, "select id from insurances where insurance_name = '".$row['TrsfFromInsName']."' order by id asc limit 1"));
				$insurance_id = $insurance['id'];
			}else{
				$insurance_id = 0;
			}
			

			$res_new = $res_old = '';
			if($transaction_type=='Responsibility'){

				if($row['TrsfToInsName']=="Patient"){
					$res_new = '';
					$insurance_id = 0;
				} else{
					$res = mysqli_fetch_array(mysqli_query($conn, "select id from insurances where insurance_name = '".$row['TrsfToInsName']."' order by id asc limit 1"));
					$insurance_id = $res_new = $res['id'];
				}
				if($row['TrsfFromInsName']=="Patient"){
					$res_old = '';
				} else{
					$res = mysqli_fetch_array(mysqli_query($conn, "select id from insurances where insurance_name = '".$row['TrsfFromInsName']."' order by id asc limit 1"));
					$res_old = $res['id'];
				}
			}

			//echo "<br>###<br>Insurance : ".$insurance_id." ## RESP OLD".$res_old." ## RESP NEW ".$res_new."## pmt_method ".$pmt_method;

			//pmt_check_info_v1
			//=================
			$pmt_check_info_v1 = "INSERT INTO pmt_check_info_v1(check_no, check_date, bank_name, created_at, updated_at, created_by, updated_by, deleted_at) 
			VALUES(0,'".$row['TransDate']."', '', '".$row['TransDate']."', '".$row['TransDate']."', 0,0,NULL)";
			mysqli_query($conn, $pmt_check_info_v1);
			$check = mysqli_insert_id($conn);
			//pmt_type = 'Payment', 'Refund', 'Adjustment', 'Credit Balance'

			//pmt_info_v1
			//===========
			$pmt_info_v1 = "INSERT INTO pmt_info_v1(pmt_no, pmt_type, patient_id, insurance_id, pmt_amt, amt_used, balance, source, source_id, pmt_method, pmt_mode, pmt_mode_id, reference, void_check, created_at, updated_at, created_by, updated_by, deleted_at)
			VALUES('".time()."','".$pmt_type."','".$claim['patient_id']."','".$insurance_id."','".$row['txAmt']."','".$row['txAmt']."',0,'posting','".$claim['id']."','".$pmt_method."','Check','".$check."','',0,'".$row['TransDate']."','".$row['TransDate']."',0,0,NULL)";
			mysqli_query($conn, $pmt_info_v1);
			$pmt_info = mysqli_insert_id($conn);

			$pmt = $ins_adj = $pat_adj = 0;

			if($pmt_type=='Payment'){
				$pmt = $row['txAmt'];
			}elseif ($pmt_type=='Refund') {
				$pmt = $row['txAmt'];
			}elseif ($pmt_type=='Adjustment') {
				if($pmt_method=="Patient")
					$pat_adj = $row['txAmt'];
				else
					$ins_adj = $row['txAmt'];
			}else{
				$pmt = $row['txAmt'];
			}
			

			$pat_bal = $ins_bal = 0;
			$fin = mysqli_fetch_array(mysqli_query($conn, "select patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj,withheld from pmt_claim_fin_v1 where claim_id = '".$row['TicketNum']."'"));

			$pmt_tx = mysqli_fetch_array(mysqli_query($conn, "select sum(total_paid+total_writeoff+total_withheld) as pmt from pmt_claim_tx_v1 where claim_id = '".$row['TicketNum']."' group by claim_id"));
			if($pmt_method=='Patient'){
				$pat_bal = ($fin['patient_due']+$fin['insurance_due']);
				if($transaction_type !='Responsibility'){
					$pat_bal = $pat_bal-$row['txAmt'];
				}
			}else{
				$ins_bal = ($fin['patient_due']+$fin['insurance_due']); 
				if($transaction_type!='Responsibility'){
					$ins_bal = $ins_bal-$row['txAmt'];
				}
			}
			//echo "<br> method ".$pmt_method." P Bal ".$pat_bal." I Bal ".$ins_bal;

			//pmt_claim_tx_v1
			//===============
			$pmt_claim_tx_v1 = "INSERT INTO pmt_claim_tx_v1(payment_id, claim_id, pmt_method, pmt_type, patient_id, payer_insurance_id, claim_insurance_id, total_allowed, total_deduction, total_copay, total_coins, total_withheld, total_writeoff, total_paid, posting_date, ins_category, created_at, updated_at, created_by, updated_by, deleted_at)
			VALUES('".$pmt_info."','".$claim['id']."','".$pmt_method."','".$pmt_type."','".$claim['patient_id']."','".$insurance_id."',0,0,0,0,0,'".$ins_adj."','".$pat_adj."','".$pmt."','".$row['TransDate']."','Primary','".$row['TransDate']."','".$row['TransDate']."',0,0,NULL)";
			mysqli_query($conn, $pmt_claim_tx_v1);
			$pmt_claim_tx = mysqli_insert_id($conn);
		
			
			//claim_tx_desc_v1
			//================
			$claim_tx_desc_v1 = "INSERT INTO claim_tx_desc_v1(transaction_type, claim_id, payment_id, txn_id, responsibility, pat_bal, ins_bal, value_1, value_2, created_at, updated_at, created_by, updated_by, deleted_at) VALUES('".$transaction_type."','".$claim['id']."','".$pmt_info."','".$pmt_claim_tx."','".$insurance_id."','".$pat_bal."','".$ins_bal."','".$res_old."','".$res_new."','".$row['TransDate'].' '.date("H:i:s",time())."','".$row['TransDate'].' '.date("H:i:s",time())."',0,0,NULL)";
			mysqli_query($conn, $claim_tx_desc_v1);
			$claim_tx_desc = mysqli_insert_id($conn);


			$cpt_sql = (mysqli_query($conn, "SELECT * FROM `allscript_payments`  where TicketNum = '".$row['TicketNum']."' 
						AND TransDesc = '".$row['TransDesc']."'
						AND TransDate = '".$row['TransDate']."'
						AND TransType = '".$row['TransType']."' order by id asc"));
			foreach($cpt_sql as $crow){

				$cpmt = $cins_adj = $cpat_adj = 0;
				if($pmt_type=='Payment'){
					$cpmt = $crow['TransAmt'];
				}elseif ($pmt_type=='Refund') {
					$cpmt = $crow['TransAmt'];
				}elseif ($pmt_type=='Adjustment') {
					if($pmt_method=="Patient")
						$cpat_adj = $crow['TransAmt'];
					else
						$cins_adj = $crow['TransAmt'];
				}else{
					$cpmt = $crow['TransAmt'];
				}

				$cpt_pat_bal = $cpt_ins_bal = 0;

				$cpt_fin = mysqli_fetch_array(mysqli_query($conn, "select cpt_charge, patient_paid, insurance_paid, insurance_adjusted, patient_adjusted,with_held,patient_balance,insurance_balance from pmt_claim_cpt_fin_v1 where claim_cpt_info_id = '".$crow['SvcProcID']."'"));

				$cpmt_cpt_tx = mysqli_fetch_array(mysqli_query($conn, "select sum(paid+writeoff+withheld) as cpt_pmt from pmt_claim_cpt_tx_v1 where claim_cpt_info_id = '".$crow['SvcProcID']."' group by claim_cpt_info_id"));
				if($pmt_method=='Patient'){
					$cpt_pat_bal = ($cpt_fin['patient_balance']+$cpt_fin['insurance_balance']);
					if($transaction_type !='Responsibility'){
						$cpt_pat_bal = $cpt_pat_bal-$crow['TransAmt']; 
					}
				}else{
					$cpt_ins_bal = ($cpt_fin['patient_balance']+$cpt_fin['insurance_balance']);
					if($transaction_type !='Responsibility'){
						$cpt_ins_bal = $cpt_ins_bal-$crow['TransAmt'];
					}
				}

				//pmt_claim_cpt_tx_v1
				//==================
				$pmt_claim_cpt_tx_v1 = "INSERT INTO pmt_claim_cpt_tx_v1(payment_id, claim_id, pmt_claim_tx_id, claim_cpt_info_id, allowed, deduction, copay, coins, withheld, writeoff, paid, denial_code, created_at, updated_at, created_by, updated_by, deleted_at)
				VALUES('".$pmt_info."','".$claim['id']."','".$pmt_claim_tx."','".$crow['SvcProcID']."',0,0,0,0,'".$cins_adj."','".$cpat_adj."','".$cpmt."','','".$crow['TransDate']."','".$crow['TransDate']."',0,0,NULL)";
				mysqli_query($conn, $pmt_claim_cpt_tx_v1);
				$pmt_claim_cpt_tx = mysqli_insert_id($conn);

				//claim_cpt_tx_desc_v1
				//====================
				$claim_cpt_tx_desc_v1 = "INSERT INTO claim_cpt_tx_desc_v1(claim_tx_desc_id, transaction_type, claim_id, claim_cpt_info_id, payment_id, txn_id, responsibility, pat_bal, ins_bal, value_1, value_2, created_at, updated_at, created_by, updated_by, deleted_at)	VALUES('".$claim_tx_desc."','".$transaction_type."','".$claim['id']."',	'".$crow['SvcProcID']."'	,'".$pmt_info."','".$pmt_claim_cpt_tx."',	'".$insurance_id."'	,	'".$cpt_pat_bal."',	'".$cpt_ins_bal."', '".$res_old."','".$res_new."', '".$crow['TransDate'].' '.date("H:i:s",time())."','".$crow['TransDate'].' '.date("H:i:s",time())."',0,0,NULL)";
				mysqli_query($conn, $claim_cpt_tx_desc_v1);
				$claim_cpt_tx_desc = mysqli_insert_id($conn);

				
				//pmt_claim_cpt_fin_v1
				//===================
				if($transaction_type != 'Responsibility') {
					if($pmt_type =='Payment'){
						if($pmt_method=="Patient"){
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set patient_paid = '".($cpt_fin['patient_paid']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";

						}else{
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set insurance_paid = '".($cpt_fin['insurance_paid']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";
						}
					}
					elseif($pmt_type =='Refund'){
						if($pmt_method=="Patient"){
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set patient_paid = '".($cpt_fin['patient_paid']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";

						}else{
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set insurance_paid = '".($cpt_fin['insurance_paid']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";
						}
					}
					elseif($pmt_type =='Adjustment'){
						if($pmt_method=="Patient"){
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set patient_adjusted = '".($cpt_fin['patient_adjusted']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";

						}else{
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set insurance_adjusted = '".($cpt_fin['insurance_adjusted']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";
						}
					}
					elseif($pmt_type =='Credit Balance'){
						if($pmt_method=="Patient"){
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set patient_paid = '".($cpt_fin['patient_paid']+$crow['TransAmt'])."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";

						}else{
							$pmt_claim_cpt_fin_v1 = "update pmt_claim_cpt_fin_v1 set insurance_paid = '".$crow['TransAmt']."' , patient_balance = '".$cpt_pat_bal."' , insurance_balance = '".$cpt_ins_bal."' where claim_cpt_info_id = '".$crow['SvcProcID']."'";
						}
					}
				
					mysqli_query($conn, $pmt_claim_cpt_fin_v1);

			
					//pmt_claim_fin_v1
					//=================
					//pmt_type = 'Payment', 'Refund', 'Adjustment', 'Credit Balance'
					if($pmt_type =='Payment'){
						if($pmt_method=="Patient"){
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set patient_paid = '".($fin['patient_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";

						}else{
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set insurance_paid = '".($fin['insurance_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";
						}
					}	elseif($pmt_type =='Refund'){
						if($pmt_method=="Patient"){
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set patient_paid = '".($fin['patient_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";

						}else{
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set insurance_paid = '".($fin['insurance_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";
						}
					}	elseif($pmt_type =='Adjustment'){
						if($pmt_method=="Patient"){
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set patient_adj = '".($fin['patient_adj']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";

						}else{
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set insurance_adj = '".($fin['insurance_adj']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";
						}
					}	elseif($pmt_type =='Credit Balance'){
						if($pmt_method=="Patient"){
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set patient_paid = '".($fin['patient_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";

						}else{
							$pmt_claim_fin_v1 = "update pmt_claim_fin_v1 set insurance_paid = '".($fin['insurance_paid']+$row['txAmt'])."' , patient_due = '".$pat_bal."' , insurance_due = '".$ins_bal."' where claim_id = '".$claim['id']."'";
						}
					}
					mysqli_query($conn, $pmt_claim_fin_v1);
				}	
			}
			$i++;
		}
		echo $i;
	} catch(Exception $e) {
		echo "Error Msg: ".$e->getMessage();
	}
?>