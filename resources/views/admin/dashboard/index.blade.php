@extends('admin')
@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Dashboard </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Database Connection -->
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-database"></i> Database Connection</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">Active</h3>
        <p class="med-gray-dark font600 no-bottom"><i class="med-orange fa fa-retweet"></i>  Re-check</p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-users"></i> Active Users</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">{{@$users_count}}</h3>
        <p class="med-gray-dark font600 no-bottom"><a href="adminuser/create" target="_blank"><span class="med-orange fa fa-plus-circle"></span> Add User</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-space-shuttle"></i> Last Backup</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">30 Days</h3>
        <p class="med-gray-dark font600 no-bottom"><i class="med-orange fa fa-upload"></i>  Backup Now</p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-bug"></i> Last Error</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">{{@$diffLastErrLogCreated}}</h3>
        <p class="med-gray-dark font600 no-bottom"><i class="med-orange fa fa-eye"></i> <a href="errorlog">View Error Log</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-ticket"></i> Tickets</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">{{@$tickets_count}}</h3>
        <p class="med-gray-dark font600 no-bottom"><i class="med-orange fa fa-eye"></i> <a href="manageticket">View Tickets</a></p>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-pie-chart"></i> Avg. Visits</p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5">242</h3>
        <p class="med-gray-dark font600 no-bottom"><i class="med-orange fa fa-chevron-down"></i>  from last month</p>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding dash-b-r-5" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5" >
                    <div class="box no-shadow no-border">
                        <!-- /.box-body -->
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-list-alt"></i> Key Indicators</h4>
                        </div>
                        <div class="box-body no-b-t  dashboard-table chat ledger-ins">
                            <table class="table table-responsive table-stripped">
                                <tbody>
                                    <tr>                                        
                                        <td>
                                            <a href="{{ url('admin/customer')}}" target="_blank">Customers</a>
                                        </td>
                                        <td class="text-right">{{@$cus_count}}</td>
                                        @if($cus_count >0 )
                                        <?php $cus_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$customers->updated_at);?>
                                        <td class="text-right">{{$cus_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/insurance')}}" target="_blank">Insurance</a>
                                        </td>
                                        <td class="text-right">{{@$insurance_count}}</td>
                                        @if($insurance_count >0 )
                                        <?php $ins_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$insurance->updated_at);?>
                                        <td class="text-right">{{$ins_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/modifierlevel1')}}" target="_blank">Modifiers</a>
                                        </td>
                                        <td class="text-right">{{@$modifier_count}}</td>
                                        @if($modifier_count >0 )
                                        <?php $mod_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$modifier->updated_at);?>
                                        <td class="text-right">{{$mod_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/code')}}" target="_blank">Codes</a>
                                        </td>
                                        <td class="text-right">{{@$codes_count}}</td>
                                        @if($codes_count >0 )
                                        <?php $code_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$codes->updated_at);?>
                                        <td class="text-right">{{$code_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/cpt')}}" target="_blank">CPT</a>
                                        </td>
                                        <td class="text-right">{{@$cpt_count}}</td>
                                        @if($cpt_count >0 )
                                        <?php $cpt_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$cpt->updated_at);?>
                                        <td class="text-right">{{$cpt_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/icd')}}" target="_blank">ICD</a>
                                        </td>
                                        <td class="text-right">{{@$icd_count}}</td>
                                        @if($icd_count >0 )
                                        <?php $icd_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$icd->updated_at);?>
                                        <td class="text-right">{{$icd_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ url('admin/speciality')}}" target="_blank">Specialty</a>
                                        </td>
                                        <td class="text-right">{{@$speciality_count}}</td>
                                        @if($speciality_count >0 )
                                        <?php $spl_updated = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$speciality->updated_at);?>
                                        <td class="text-right">{{$spl_updated}}</td>
                                        @else
                                        <td class="text-right">-Nil-</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding dash-b-r-5" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5" >
                    <div class="box no-shadow no-border">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-list-alt"></i> Practices</h4>
                        </div>
                        <div class="box-body no-b-t  dashboard-table chat ledger-ins" >
                            <table class="table table-responsive table-stripped" >
                                <tbody>
                                    @foreach ($pra_list as $praName=>$praStatus)
                                    <tr>
                                        <?php
                                        $pra_detail = App\Models\Medcubics\Practice::practiceID($praName);
                                        $pra_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pra_detail->id, 'encode');
                                        $cus_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pra_detail->customer_id, 'encode');
                                        ?>
                                        <td>
                                            <a href="{{ url('admin/customer/'.$cus_id.'/customerpractices/'.$pra_id)}}" target="_blank">
                                                {!! $praName; !!}
                                            </a>
                                        </td>
                                        <td>{!! $praStatus; !!}</td>
                                        @if($praStatus == 'Active')
                                        <td>
                                            <i class="fa fa-database med-green font16 margin-r-10 cur-pointer" title="Backup now"></i>
                                            <i class="fa fa-check-square-o med-green font16 margin-r-10" title="Database Connected"></i>
                                        </td>
                                        
                                        @elseif($praStatus == 'inactive')
                                        <td>
                                            <i class="fa fa-refresh med-red font16 margin-r-10 cur-pointer" title="Refresh Connection"></i>
                                            <i class="fa fa-close med-red font16 margin-r-10" title="Database not found"></i>
                                        </td>                                        
                                        @elseif($praStatus == 'In Progress')
                                        <td>
                                            <i class="fa fa-refresh med-red font16 margin-r-10 cur-pointer" title="Refresh Connection"></i>
                                            <i class="fa fa-close med-red font16 margin-r-10" title="Database not found"></i>
                                        </td>                                        
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding " >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box no-shadow no-border">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-list-alt"></i> Active Users</h4>
                        </div>
                        <div class="box-body no-b-t  dashboard-table chat ledger-ins">
                            <table class="table table-responsive table-stripped">                                
                                <tbody>
                                    @foreach ($users as $list)
                                    <?php
                                    $user_id = $list->id;
                                    $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
				?>
                                    <tr class="" id="comment_<?php echo $user_id; ?>" >
                                        <td style="width:78%">
                                            <a href="{{ url('admin/adminuser/'.$id)}}" target="_blank">
                                                {!! $list->name; !!}
                                            </a>
                                        </td>
                                        <td>
                                            <i class="fa fa-check-square-o med-green font16 margin-r-10"></i>
                                        <a  href="javascript:void(0);" >
                                                <i class="fa fa-sign-out med-green font16 margin-r-10 userLogout cur-pointer" title="logout" data-id="{{ $user_id }}"></i>
                                            </a>
                                        </td>
                                              
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding dash-b-r-5" >
                 <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-binoculars"></i> Recent Errors</h4>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-5">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t  dashboard-table chat ledger-ins">
                       <div id="ajax-recent-errors">
                       </div>
                         
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding " >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                     <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-user-times"></i> Pending Approval</h4>
                        </div>           
                    <div class="box no-shadow no-border">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                           <div class="box-body no-b-t  dashboard-table chat ledger-ins">
                            <table class="table table-responsive table-stripped">
                                <tbody>
                                    @if(!empty($userLoginInfo))
                                         @foreach($userLoginInfo as $list) 
                                            <tr>
                                                <td>{{ $list->user->short_name }}</td>
                                                <td>{{ $list->user->email }}</td>
                                                @if(Auth::user()->practice_user_type == 'customer')
                                                    <td>{{ App\Http\Helpers\Helpers::getPracticeNames($list->user->admin_practice_id,$list->user->id) }}</td>
                                                @endif
                                                <td>{{ $list->security_code }}</td>                           
                                            </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="10">
                                                No records found
                                            </td>
                                        </tr>
                                        @endif                              
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<input type="hidden" name="token" value="{{ csrf_token() }}"/>
 <script type="text/javascript">
setInterval(swapErrors,10000);
var ajaxUrl = "<?php echo url("admin/get_recent_errors"); ?>";
 function swapErrors(){
  //console.log(ajaxUrl);
    $.ajax({
            type: "GET",        
            url: ajaxUrl,             
            success: function (result) {
                $("#ajax-recent-errors").html(result);                 
            }
        });    
 }
</script>
@stop