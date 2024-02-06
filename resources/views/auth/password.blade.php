@extends('auth/app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-pull-3" style="margin-top:30px;">			
            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 forgot-password-bg" style="min-height:500px;">

                        <form class="form-horizontal" id="js-bootstrap-validator" role="form" method="POST" action="{{ url('api/password/email') }}" style="margin-top:240px; margin-left:89px;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group margin-t-22" style="margin-top:40px;">

                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <p style="color:#999; margin-bottom:0px;"><i>Enter your Email ID</i></p>
                                </div>

                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-mail ID">
                                    {!! $errors->first('email', '<span class="no-padding med-red font12" style="position: absolute"> :message</span>')  !!}

                                </div>
                                
                            </div>
                             @if(Session::get('success')!== null) 
                            <div class="form-group">
                                
                                <div class="col-lg-6 font11" style="width: 240px; color: red;font-size:12px;"> 
                                   
                                    {{ Session::get('success') }}
                                   	
                                </div>
                            </div>
                              @endif
                              <div class="form-group margin-t-20" style="">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 margin-t-10">
                                    <button type="submit" class="btn btn-orange">
                                        Submit
                                    </button>
                                    <a href="{{ url('auth/login')}}">{!! Form::button('Cancel', ['class'=>'btn btn-gray']) !!}</a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .panel-heading {
        color: #35acc3;
        font-weight: bold;
        padding-bottom: 20px;
        text-align: center;
    }
</style>
@endsection

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                email: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("common.validation.email") }}'
                        },
                        emailAddress: {
                            message: '{{ trans("common.validation.email_valid") }}'
                        }
                    }
                }
            }
        });
    });

</script>
@endpush
