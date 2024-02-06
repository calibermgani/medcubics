<?php namespace App\Models\Payments;

use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class PMTWalletV1 extends Model
{
    use SoftDeletes;
    protected $table = 'pmt_wallet_v1';
    protected $fillable = ['patient_id', 'pmt_info_id', 'wallet_Ref_Id', 'tx_type', 'amount', 'applied', 'updated_by', 'created_by', 'balance'];
    public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value)) {
                    if($name=='deleted_at')
                        $table->{$name} = Null;
                    else
                        $table->{$name} = '';
                }
            }
            return true;
       });
    }

    public static  function getPatientWalletData($patientId)
    {
        $balanceAmount = DB::raw('(sum(pmt_amt)-sum(amt_used)) as wallerBalanceAmount');
        $walletBalanceData = PMTInfoV1::select($balanceAmount)
                            ->where('patient_id', $patientId)
                            ->where('pmt_info_v1.pmt_method', 'Patient')
                            ->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])
                            ->where('pmt_info_v1.void_check', NULL)
                            ->where('pmt_info_v1.pmt_amt', '>', 0)
                            ->get()->toArray();

        if (!empty($balanceAmount) && !empty($walletBalanceData)){
            $returnData = ($walletBalanceData[0]['wallerBalanceAmount'] > '0.00')?$walletBalanceData[0]['wallerBalanceAmount'] : '0.00';
            return $returnData;
        }
    }
    
    public  static  function  updatePmtWalletAmount($pmtInfoId,$datas){
        if(!empty($pmtInfoId)) {
            $paymentTblData = PMTInfoV1::select( DB::raw('pmt_amt - amt_used as balance'))
                              ->where('id', $pmtInfoId)->first();
             $walletData =    PMTWalletV1::where('pmt_info_id', $pmtInfoId)
                              ->where('tx_type', 'Credit')->get()->first();
             if(isset($walletData)) {
                 $walletData->update(['amount' => $paymentTblData['balance']]);
             }else if (empty($walletData) && $datas['pmt_method'] =='Patient'){
                 $walletData = array(
                     'patient_id' => $datas['patient_id'],
                     'pmt_info_id' => $pmtInfoId,
                     'tx_type' => 'Credit',
                     'amt_pop' => $datas['pmt_amt'],
                     'wallet_ref_id' => $pmtInfoId,
                     'claimId' => ''
                 );
                 $paymentV1 = new PaymentV1ApiController();
                 $paymentV1->storeWalletData($walletData, false, false);
             }
        }
    }
}