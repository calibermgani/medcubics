<div class="col-md-12">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    {!! HTML::image('img/noimage.png',null,['class'=>'img-border']) !!}     
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12 med-right-border">
                <h3>Templates</h3>
                <p class="push">
                <div class="form-group">
                    {!! Form::label('name', 'Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
                    <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('description')) error @endif">
                        {!! Form::text('name',null,['class'=>'form-control','name'=>'name']) !!}
                        {!! $errors->first('name', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <br/>

                <div class="form-group">
                    {!! Form::label('templatetypes', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                    <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('templates_type_id')) error @endif">  
                        {!! Form::select('template_type_id', array('' => '-- Select --') + (array)$templatestype,  $template_type_id,['class'=>'form-control select2']) !!}
                        {!! $errors->first('template_type_id', '<p> :message</p>')  !!}
                    </div> 
                    <div class="col-sm-1"></div>
                </div> 

                <br/>

                <div class="form-group">
                    {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                    <div class="col-lg-3 col-md-6 col-sm-6 @if($errors->first('status')) error @endif">
                        {!! Form::radio('status', 'Active',true,['class'=>'']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'']) !!} Inactive 
                        {!! $errors->first('status', '<p> :message</p>')  !!}
                    </div>                    
                </div>

                </p>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div>
                    <span class="med-green"><i class="fa fa-filter font14"></i> Created by</span>
                    <h5 class="med-header-list-bg">{{ $templates->creator->name }}</h5>
                </div>

                <div>
                    <span class="med-green"><i class="livicon"  data-name="tag" data-animate="false" ></i>Updated by</span>
                    <h5 class="med-header-list-bg">{{ $templates->modifier->name}}</h5>
                </div>

            </div>

        </div><!-- /.box-body -->
        <!-- Sub Menu -->
        <?php 
			$activetab = 'templatetypes'; 
			$routex = explode('.',Route::currentRouteName());
			if(count($routex) > 0) {
				if($routex[0] == 'templates') {
					$activetab = 'templates';
				}
			}
		?>        

        <div class="table-container">
            <div class="col-table-cell col-lg-6 col-md-6 col-sm-6 col-xs-6  left-corner"><i class="livicon tabs" data-name="table" data-size="20"></i><a href="{{ url('templatetypes') }}" @if($activetab == 'templatetypes')class="active" @endif>Template Categories</a></div>
            <div class="col-table-cell col-lg-6 col-md-6 col-sm-6 col-xs-6 right-corner"><i class="livicon" data-name="responsive-menu" data-size="20"></i> <a href="{{ url('templates') }}" @if($activetab == 'templates')class="active" @endif>Templates</a></div>
        </div>  
    </div><!-- /.box -->
</div>