<?php
return [
	"claim_txn"	=> [		
		"claim_created_desc"				=> "Claim Created",
		"submitted_edi_ins_desc"			=> "Submitted-EDI: VAR_SHORT_NAME",
		"submitted_paper_ins_desc"			=> "Submitted-Paper:VAR_SHORT_NAME",

		"resubmitted_edi_ins_desc"			=> "ReSubmitted-EDI: VAR_SHORT_NAME",			
		"resubmitted_paper_ins_desc"		=> "ReSubmitted-Paper: VAR_SHORT_NAME",			

		"transfer_to_ins_desc"				=> "Transfer to VAR_INS_NAME",	// "Transfer to Patient/Insurance(VAR_SHORT_NAME)"
		"transfer_to_pat_desc"				=> "Transfer to VAR_PAT_NAME",	// "Transfer to Patient/Insurance(VAR_SHORT_NAME)"
		"pat_pmt_paid_desc"					=> "PMT: VAR_PAT_NAME",
		"ins_pmt_paid_desc"					=> "PMT: VAR_INS_NAME",
		"charge_created_desc"				=> "Claim Created",	
		"charge_updated_desc"				=> "Claim Modified",	
		"ins_pmt_ded_txn_desc"				=> "PR01: VAR_TXN_AMOUNT",
		"ins_pmt_coins_txn_desc"			=> "PR02: VAR_TXN_AMOUNT",
		"ins_pmt_coppay_txn_desc"			=> "PR03: VAR_TXN_AMOUNT",
		"ins_pmt_adj_txn_desc"				=> "CO45: VAR_TXN_AMOUNT",
		"pat_adj_txn_desc"					=> "Pat Adj: VAR_MSG",
		"ins_adj_txn_desc"					=> "Ins Adj: VAR_MSG",
		"denial_code_desc"					=> "VAR_CODES",
		"pmt_withheld_desc"					=> "With Held: VAR_TXN_AMOUNT",		
		"pmt_takeback_txn_desc"				=> "CO45: Insurance(Short Name)",
		"excess_wallet_transfer_desc"		=> "Transfer to Wallet: VAR_AMOUNT",
		"ins_adj_txn_desc"					=> "Ins Adj: VAR_REASON",
		"pat_adj_txn_desc"					=> "Pat Adj: VAR_REASON",
		"refund_txn_desc"					=> "Refund: VAR_SHORT_NAME",	
		"pat_cr_bal_txn_desc"				=> "Pat Cr: Patient",		
		"denial_txn_desc"					=> "Claim Denied",
		"pmt_transfer_wallet_txn_desc"		=> "Transfer to Wallet",
		"pmt_reversal_txn_desc"				=> "Reversal: Patient/Insurance(VAR_SHORT_NAME)",			
		// Clearing house responses
		"clearinghouse_acc_desc"			=> "EDI Accepted: VAR_SHORT_NAME",
		"clearinghouse_rej_desc"			=> "EDI Rejection: VAR_SHORT_NAME",
		"clearinghouse_payer_acc_desc"		=> "Payer Accepted: VAR_SHORT_NAME",
		"clearinghouse_payer_rej_desc"		=> "Payer Rejection: VAR_SHORT_NAME",
		"void_check_desc"					=> "Void Check: VAR_TXN_AMOUNT",
		"void_ins_check_desc"				=> "Void Insurance Refund Check: VAR_TXN_AMOUNT",
	],

	"cpt_txn" => [
		"charge_created_desc"				=> "Claim Created",	
		"charge_updated_desc"				=> "Claim Modified",
		"ins_pmt_ded_txn_desc"				=> "PR01: VAR_TXN_AMOUNT",
		"ins_pmt_coins_txn_desc"			=> "PR02: VAR_TXN_AMOUNT",
		"ins_pmt_coppay_txn_desc"			=> "PR03: VAR_TXN_AMOUNT",
		"pat_pmt_paid_desc"					=> "PMT: VAR_PAT_NAME",
		"ins_pmt_paid_desc"					=> "PMT: VAR_INS_NAME",
		"pmt_withheld_desc"					=> "With Held: VAR_TXN_AMOUNT",
		"ins_pmt_adj_txn_desc"				=> "CO45: VAR_TXN_AMOUNT",
		"pmt_modified_desc"					=> "",
		"transfer_to_ins_desc"				=> "Transfer to VAR_INS_NAME",
		"transfer_to_pat_desc"				=> "Transfer to VAR_PAT_NAME",
		"claim_denied_desc"					=> "Claim Denied Pmt : VAR_INS_NAME",
		"refund_txn_desc"					=> "Refund: VAR_SHORT_NAME",	// Insurance / Patient refund.
		"pat_adj_txn_desc"					=> "Pat Adj: VAR_REASON",
		"ins_adj_txn_desc"					=> "Ins Adj: VAR_REASON",
		"wallet_txn_desc"					=> "",
		"denial_code_desc"					=> "VAR_CODES",
		"pat_cr_bal_txn_desc"				=> "Pat Cr: Patient",

	]
];