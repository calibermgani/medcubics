<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-20 no-padding" style="display:none">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding" >
        <?php	
			$coverphoto = App\Http\Controllers\Profile\BlogController::getCoverPhoto();
			$coverphoto = json_decode(json_encode($coverphoto), True);
			$filename = '';
		?>
      
        <i class="fa fa-spinner fa-spin coverloadingimg" id="coverimg" style="display:none" ></i>
		@if($coverphoto['status'] == 'unavailable')
			<?php $avatar_url = 'img/blog_cover.jpg';	?>
			{!! HTML::image($avatar_url,null,['class'=>'img-responsive full-width','id'=>'coverphotimg']) !!} 
        @else
			<?php $filename = $coverphoto['get_photo']['0']['coverphoto'];
				$img_details = [];
				$img_details['module_name']='cover_photo';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="ProfileUserCover";				
				$img_details['class']='img-responsive full-width';
				$img_details['id']='coverphotimg';
				$img_details['alt']='blog-image';
				$image_tag = App\Http\Helpers\Helpers::checkandgetavatar($img_details);
			?>	
			{!! $image_tag !!}		
        @endif
        {!! Form::open(['url'=>'profile/addCoverPhoto','id'=>'js-bootstrap-validator','files'=>true]) !!}
		
		<span class="js_addcover js_cover" @if($coverphoto['status'] != 'unavailable') style="display:none;" @endif >
        <a class="btn btn-mail btn-flat blog-change-cover">Add Cover</a>
        <input type="file" class="custom" id="add" data-action="add" />
        </span>
		
		<span class="js_editcover js_cover" @if($coverphoto['status'] == 'unavailable') style="display:none;" @endif >
        <a class="btn btn-mail btn-flat blog-change-cover cur-pointer">Change Cover</a>
        <input type="file" class="custom" id="edit" data-action="edit" />
        <a class="btn btn-mail btn-flat blog-remove-cover blog-change-cover js_removecover" data-coverimg="{{ $filename }}">Remove Cover</a>
        </span>

        {!! Form::close() !!}  	  	
        @if(Auth::user()->lastname != '')		
        <a class="med-orange blog-uname">{{ Auth::user()->lastname }}, {{ Auth::user()->firstname }}</a>
        @endif
		
	<!--
        @if(Auth::user()->role_id != '' && Auth::user()->role_id != 0 )		
		    <a class="blog-role">{{ @$coverphoto['rolelist'][Auth::user()->role_id] }}</a>		
        @endif
        -->
    </div>

    <?php  $userid = Auth::user()->id; ?>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 blog-profile-icon">


        @if(Auth::user()->avatar_name != '')
        <?php $userfilename = Auth::user()->avatar_name.'.'.Auth::user()->avatar_ext; ?>
        @else
        <?php $userfilename = 'img/blog-img.jpg'; ?>
        @endif		
		<?php 
			$img_details = [];
			$img_details['module_name']='user';
			$img_details['file_name']=$userfilename;
			$img_details['practice_name']="admin";
			$img_details['class']='img-responsive blog-img';
			$img_details['alt']='blog-image';
			$useravatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details); 
		?>
        <a href="#"> {!! $useravatar_url !!}	</a>          
    </div>
</div>
<style>
    .custom{
        position: absolute;
        right: 0;
        width: 11%;
        bottom: 10px;
        opacity: 0;     
        cursor: pointer !important;
    }

    .coverloadingimg {
        color: green;
        font-size: 30px;
        left: 50%;
        position: absolute;
        top: 20%;
        z-index: 99;
    }
   
</style>