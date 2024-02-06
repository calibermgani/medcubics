<div class="col-md-12" style="margin-top:-10px;">
    <div class="box box-info no-shadow" style="border: 1px solid #85E2E6">
        <div class="box-body" style="padding-bottom: 20px;">


            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tab-border-bottom">
                {!! HTML::image('img/profile-pic.jpg',null,['class'=>'img-border  margin-r-20','style'=>'display:inline;float:left;margin-top:10px;']) !!}

                <h3 style=" font-size: 18px;">{{ @$patient_detail->first_name  }}{{ @$patient_detail->last_name  }}  </h3>
                <span class="med-orange sm-size"><i class="med-orange med-gender fa fa-male margin-r-5 "></i> Feb 12,1975, 41 years</span> 
                <h5><span class="med-green">SSN: </span>{{ @$patient_detail->ssn  }}</h5>

                <h5><span class="med-green">Acc No: </span>{{ @$patient_detail->account_no  }}</h5>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12  med-ts-separator">

                <h5><span class="" style="background: #F9EFD3; padding: 2px 10px; color:#D98400">Pri </span> &nbsp;Medicare - <span class="med-orange">$145</span></h5>
                <h5><span style="background: #F2F9C8; padding: 2px 10px; color:#798C01">Sec</span> Magna Health - <span class="med-orange">$218.5</span></h5>
                <h5><span style="background: #D2EDF9; padding: 2px 10px;  color:#0572A1">Ter</span> Medicare - <span class="med-orange">$145</span></h5>
                <h5><span style="background: #F6E3FC; padding: 2px 10px;  color:#720294">Pat</span> <span class="med-orange">$218.5</span></h5>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <h5><span class="med-green">Status </span><span class="patient-status-bg pull-right label-success ">Active</span></h5>
				<h5><span class="med-green">AR Days :</span> <span class="patient-status-bg pull-right label-success">120 +</span></h5>
                <h5><span class="med-green">Last Appt :</span> <span class="patient-status-bg pull-right label-success">Jan 29, 2015</span></h5>
                <h5><span class="med-green">Last Statement :</span> <span class="patient-status-bg pull-right label-success">Feb 16, 2015</span></h5>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>