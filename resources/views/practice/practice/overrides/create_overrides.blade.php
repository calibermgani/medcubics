@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('overrides') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('practice/practice/practice-tabs')  
@stop

@section('practice')
  
	{!! Form::open(array('route' => 'overrides.store','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform')) !!}
	@include ('practice/practice/overrides/form',['submitBtn'=>'Save'])							
	{!! Form::close() !!}
                 
    <!--End-->

    <!-- Script for Exploding billing provider from TAX ID and NPI -->
    <script>
        function splitToUpdate(source, to1, to2, to3) {
            if (!source || !to1 || !to2 || !to3) {
                return false;
            } else {
                source = source.nodeType == 1 ? source : document.getElementById(source);
                to1 = to1.nodeType == 1 ? to1 : document.getElementById(to1);
                to2 = to2.nodeType == 1 ? to2 : document.getElementById(to2);
                to3 = to3.nodeType == 1 ? to3 : document.getElementById(to3);

                var selOpt = source.selectedIndex,
                        vals = source.getElementsByTagName('option')[selOpt].value;
                if(vals != ''){
                    to1.value = vals.split(';')[0];
                    to2.value = vals.split(';')[1];
                    to3.value = vals.split(';')[2];
                } else {
                    to1.value = '';
                    to2.value = '';
                    to3.value = '';                   
                }
            }
        }

        var sel = document.getElementById('billingprovider'),
                opt1 = document.getElementById('billing_provider'),
                opt2 = document.getElementById('tax_id');
        opt3 = document.getElementById('npi');

        sel.onchange = function () {
            splitToUpdate('billingprovider', 'billing_provider', 'tax_id', 'npi');
        };
    </script>
    <!-- Script for Exploding billing provider from TAX ID and NPI -->
@stop 