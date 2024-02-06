<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <h2 class="text-left font16 margin-t-8" style=""><span style="font-size: 20px;">Invoice</span>
            <span class="pull-right p-r-10" style="font-size: 14px;">
                Date : {{ date("m/d/Y") }}</span>
        </h2>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px;">
        <!-- Box Body Starts -->
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 row">
            <div class="box box-info no-shadow no-border" >
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="" class="" style="line-height:24px !important;">
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="font-style: italic !important;color:#767171 !important;">
                                    Invoice To
                                </td>
                            </tr>
                            <tr style="margin-bottom:30px !important;"> 
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border:none;text-align: left !important;font-size: 16px !important;color:#0999cd !important;font-weight: 500;line-height: 16px !important;margin-bottom: 30px !important;">
                                    <input type="hidden" name="practice_id" value="{{$practice->id}}">
                                    {{$practice->practice_name}}
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    {{@$practice->mail_add_1}}, {{@$practice->mail_add_2}}
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    {{$practice->mail_city}}, {{$practice->mail_state}} - {{$practice->mail_zip5}} {{$practice->mail_zip4}}.
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6">Phone : {{($practice->phone !="")?"+ ".$practice->phone:" - Nil - "}}</td>  
                            </tr>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3 pull-right row">
            <div class="box box-info no-shadow no-border">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="" class="table table-striped table-bordered" style="border:1px solid #ccc !important;">
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border:1px solid #ccc;border-top:1px solid #ccc !important;background: #f2f2f2;">Invoice Date</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-top:1px solid #ccc !important;border-bottom:1px solid #ccc !important;">
                                    {!! Form::text('invoice_date',null,['class'=>'form-control js_invoice_date dm-date bg-white no-border form-cursor','readonly'=>'readonly','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr style="background: #f2f2f2;">
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border:1px solid #ccc;background: #f2f2f2;">Invoice #</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-bottom: 1px solid #ccc !important">
                                    {!! Form::text('invoice_no',$invoice_no,['class'=>'select2 no-border form-control js_select_practice_id','readonly'=>'readonly','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border:1px solid #ccc;background: #f2f2f2;">Invoice Period </td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-bottom: 1px solid #ccc !important">
                                    {!! Form::text('invoice_period',null,['class'=>'bg-white no-border form-control form-select invoice_range','readonly'=>'readonly','onkeypress'=>'return is_null(event)','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr style="background: #f2f2f2;">
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border:1px solid #ccc;background: #0999cd; color:#fff;">Total Due Amount ($)</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-bottom: 1px solid #ccc !important">
                                    {!! Form::text('total_due_amount',null,['class'=>'select2 no-border form-control text-right','readonly'=>'readonly','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                        </table><div id="errors"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"></div><!-- Box Body Starts -->
    </div><!-- Box Body Ends -->
    <div class="row" style="margin: 0 auto;">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="box box-info no-shadow no-border no-bottom">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="invoice" class="table table-striped table-bordered mobile-md-width">
                            <thead>
                                <tr class="row">
                                    <th class="col-md-1">Start Date</th>
                                    <th class="col-md-1">End Date</th>
                                    <th class="col-md-6">Products</th>
                                    <th class="col-md-1">Unit Price</th>
                                    <th class="col-md-1">Quantity</th>
                                    <th class="col-md-2">Total ($)</th>  		
                                </tr>
                            </thead>
                            <tbody id="invoiceBody">
                                
                                
                                
                                
                                
                                <tr class="row add">
                                    <td class="col-md-1">{!! Form::text('start_date[]',null,['data-field-type'=>'','class'=>'form-control js_invoice_date bg-white dm-date form-cursor start_date','readonly'=>'readonly', 'autocomplete'=>'off']) !!}</td>
                                    <td class="col-md-1">{!! Form::text('end_date[]',null,['data-field-type'=>'','class'=>'form-control js_invoice_date dm-date bg-white form-cursor end_date','readonly'=>'readonly', 'autocomplete'=>'off']) !!}</td>
                                    <td class="col-md-6">{!! Form::text('product[]',null,['data-field-type'=>'','class'=>'form-control product','autocomplete'=>'off']) !!}
                                    <td class="unit col-md-1">{!! Form::text('units[]',null,['data-field-type'=>'number' ,'class'=>'unit form-control text-right units','onkeypress'=>'return isNumber(event)','autocomplete'=>'off']) !!}</td>
                                    <td class="quantity col-md-1">{!! Form::text('quantity[]',null,['data-field-type'=>'number','class'=>'quan form-control text-right quant','onkeypress'=>'return isNumber(event)','autocomplete'=>'off']) !!}</td>
                                    <td class="col-md-2">{!! Form::text('total[]',null,['data-field-type'=>'','class'=>'total_amount form-control form-cursor text-right','readonly'=>'readonly','autocomplete'=>'off']) !!}</td>
                                </tr>                                                                                                                                
                            </tbody>
                        </table>
                        <a id="add" class="btn btn-medcubics-small">+</a>
                        <a id="remove" class="btn btn-medcubics-small">-</a>
                        <div id="errors1"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>      
    </div>
    <div class="row" style="margin: 0 auto;">
        <div class="col-lg-8 col-md-8 col-sm-6 col-xs-6" ></div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3">
            <div class="box box-info no-shadow no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table id="" class="table table-striped table-bordered" style="border:1px solid #ccc !important;">
                            <tr style="background: #f2f2f2;">
                               <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right" style="border:1px solid #ccc;border-top:1px solid #ccc !important;background: #f2f2f2;">Amount Due ($)</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-top:1px solid #ccc !important;border-bottom:1px solid #ccc !important;">
                                    {!! Form::text('due_amount',null,['class'=>'form-control text-right no-border','readonly'=>'readonly','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right" style="border:1px solid #ccc;background: #f2f2f2;">Previous Amount Due ($)</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-bottom:1px solid #ccc">
                                    {!! Form::text('previous_amount',null,['class'=>'previous_amount text-right form-control no-border text-right','onkeypress'=>'return isNumber(event)','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right" style="border:1px solid #ccc;background: #f2f2f2;">Tax % </td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="border-bottom: 1px solid #ccc;">
                                    {!! Form::text('tax',null,['class'=>'tax text-right form-control text-right no-border','onkeypress'=>'return isNumber(event)','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                            <tr style="background: #f2f2f2;">
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right" style="border:1px solid #ccc;background: #0999cd;color:#fff;">Total Due Amount ($)</td>
                                <td class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    {!! Form::text('total_amount',null,['class'=>'form-control no-border text-right','readonly'=>'readonly','autocomplete'=>'off']) !!}
                                </td>
                            </tr>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
        <input class="btn btn-medcubics" id="js_exit_part_report" value="Download" type="submit">
    </div>
</div>
<!-- /.box Ends-->
<script type="text/javascript">
    $('input[name="invoice_period"]').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment(),
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        showDropdowns: true,
        locale: {
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment()],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf("year"), moment()],
            'Last Year': [moment().subtract(1, "y").startOf("year"), moment().subtract(1, "y").endOf("year")]
        }
    });

    $('input[name="invoice_period"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).keydown(function () {
            return false;
        });
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        invoice_vali();
        $('#invoice_val').bootstrapValidator('revalidateField', 'invoice_period');
    });

    $('input[name="invoice_period"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).keydown(function () {
            return false;
        });
        $(this).val('');
        invoice_vali();
        $('#invoice_val').bootstrapValidator('revalidateField', 'invoice_period');
    });
</script>