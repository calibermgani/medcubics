@if(Session::has('success'))
	<?php /*<p class="alert alert-success margin-t-m-20 margin-b-25" id="success-alert">{{ Session::get('success') }}</p> */ ?>
	@push('view.scripts1')
	<script type="text/javascript">
		/* $(document).ready(function(){
			msg = '<?php echo Session::get('success'); ?>';
			js_sidebar_notification('success',msg); 
		}) */
	</script>
	@endpush
@endif

@if(Session::has('error'))
	<?php /*<p class="alert alert-danger margin-t-m-20 margin-b-20" id="error-alert"><button class="close " data-dismiss="alert">×</button>{{ Session::get('error') }}</p>*/ ?>
	@push('view.scripts1')
	<script>
		/* $(document).ready(function(){
			msg = '<?php echo Session::get('error'); ?>';
			js_sidebar_notification('error',msg); 
		}) */
	</script>
	@endpush
@endif

@if(Session::has('info'))
	<?php /*<p class="alert alert-info"><button class="close" data-dismiss="alert">×</button>{{ Session::get('info') }}</p>*/ ?>
	@push('view.scripts1')
	<script>
		/* $(document).ready(function(){
			msg = '<?php echo Session::get('info'); ?>';
			js_sidebar_notification('info',msg); 
		}) */
	</script>
	@endpush
@endif