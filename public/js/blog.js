      // blog favourite
        $(document).on("click",".blog_favourite",function(){      
            var blogid = $(this).data("id");
            $.ajax({
                    type : 'GET',
                    url  : api_site_url+'/profile/blog/favourite',
                    data : {'page':'1','blogid':blogid},
                    success :  function(result){
                        if(result == '1'){
                            $('#blog_fav'+blogid).html('<i class="fa tooltips fa-star font16 med-orange font600" data-toggle="tooltip" data-original-title="Remove from favourite"></i>');
                        }
                        else {
                            $('#blog_fav'+blogid).html('<i class="fa tooltips fa-star-o font16 med-orange font600" data-toggle="tooltip" data-original-title="Add to favourite"></i>');
                        }
                        
                        $.ajax({
							type : 'GET',
							url  : api_site_url+'/profile/blog/favourite',
							data : {'page':'3','blogid':blogid},
							success :  function(result) {
							   $('.favourite_count'+blogid).html(result);
							}
                        });
                  }
            });
        });
        
        // Blog comments
        
        $( ".comments" ).keyup(function() {
            if($(this).val()=='')
                $('#error_comment').show(); 
            else    
                $('#error_comment').hide(); 
        });
		
		
		$(document).on("keyup",".js_search_blog_list",function(){
			$('#error_keyword').hide(); 
		});
        
        $(document).on("click",".get_commentimg",function(){    
            var img = $(this).data("commentimg");
            var comment_url = $('#selector_comment_url').data("commenturl");
            $('.comments_img').html('<img src="'+comment_url+'/'+img+'">'); 
           var preloader = new Image();
            preloader.src = comment_url+'/'+img;
            preloader.onload = function(){
            var width = preloader.width+30;
            $('.set_comment_width').width(width);
            }
        });
        
        // Delete the comments
        var j = 1;
        $(document).on("click",".js_check_delete",function(){
            
            var commentid = $(this).data("id");
            var blogid 	  = $(this).data("blogid");
                
             $.confirm({
                text: "Are you sure you want to delete the comment?",
                confirm: function() {
                     $.get(api_site_url+'/profile/blog/deletecomments/'+commentid+'/'+blogid,function(getcommentfav, status){
					   setTimeout(function(){
						  $(".parentcomment"+commentid).hide();
						  
						  if(getcommentfav == '0') {   
								$('#loadMore').hide();
						   }
					   },300); 
                     });
                },
                cancel: function() {
                    // nothing to do
                }
            });
            j++;
            
        });
		
		// Delete the comments
        var j = 1;
        $(document).on("click",".js_delete_reply",function(){
            
            var parentid = $(this).data("parentid");
            var replyid 	  = $(this).data("replyid");
                
             $.confirm({
                text: "Are you sure you want to delete the comment?",
                confirm: function() {
                     $.get(api_site_url+'/profile/blog/delreplycomments/'+replyid+'/'+parentid,function(getcommentfav, status){
					   setTimeout(function(){
						  $(".childcomment"+replyid).hide();
						  if(getcommentfav == '0') {   
								$('#loadMore').hide();
						   }
					   },300); 
                     });
                },
                cancel: function() {
                    // nothing to do
                }
            });
            j++;
        });
		
		// Delete the reply comments
        var j = 1;
        $(document).on("click",".js_delete_reply",function(){
            
            var id 			  = $(this).data("id");
            var commentid 	  = $(this).data("commentid");
			
             $.confirm({
                text: "Are you sure you want to delete the comment?",
                confirm: function() {
                     $.get(api_site_url+'/profile/blog/delreplycomments/'+commentid+'/'+id,function(getcommentfav, status){
					   setTimeout(function(){
						  $(".childcomment"+commentid).hide();
						  
						  if(getcommentfav == '0') {   
								$('#loadMore').hide();
						   }
					   },300); 
                     });
                },
                cancel: function() {
                    // nothing to do
                }
            });
            j++;
            
        });
        
        // Privacy option for add/edit blog page
        
        $(document).on("change","#privacy",function(){
            
            $('#group').hide();
            $('#selectuser').hide();
            if($(this).val()=='Group'){
                $('#group').show();
            }
            if($(this).val()=='User'){
                $('#selectuser').show();
            }
        });
		
		$(document).on("click","#js-blogorder",function(){
			
			if($('.blog-border').length>0)
			{
				$('#listingimg').show();
				$('.import_order').hide();
				var ordervalue 	  = ($(this).data('value')!='')? $(this).data('value') :'0';
				var searchkeyword = $("input[name='search_blog_keyword']").val();
				if(searchkeyword == '')
					var dataurl = api_site_url+'/profile/blogs/'+ordervalue;
				else
					var dataurl = api_site_url+'/profile/blogs/'+ordervalue+'/'+searchkeyword;
				
				$.ajax({
					type : 'GET',
					url  : dataurl,
					success :  function(result) {
					   $('.js_blog_order').html(result);
					   $('#load_more_button').attr('data-order',ordervalue);
					   $('#load_more_button').attr('data-keyword',searchkeyword);
					   $('#listingimg').hide();
					 }
				});	 
			}
        });
		
		$(document).on('click', '.js_search_blog_button',function () {
			var searchkeyword = $("input[name='search_blog_keyword']").val();
			 $('.import_order').hide();
			if(searchkeyword == '') {
				$('#error_keyword').show();
			}
			else
			{
				$('#error_keyword').hide();
				$('#listingimg').show();
				$.ajax({
					type : 'GET',
					url  : api_site_url+'/profile/blogs/0/'+searchkeyword,
					success :  function(result) {
					   if(result!='' && $.trim(result)!='<div id="results"></div>')	
							$('.js_blog_order').html(result);
						else
							$('.js_blog_order').html('No results found');
						
					   $('.load_more').attr('data-keyword',searchkeyword);
					   $('#listingimg').hide();
					 }
				});
			}
		 });
		 $(document).on('click', '.js_search_update_button',function () {
            var searchkeyword = $("input[name='search_blog_keyword']").val();
             $('.import_order').hide();
            if(searchkeyword == '') {
                $('#error_keyword').show();
            }
            else
            {
                $('#error_keyword').hide();
                $('#listingimg').show();
                $.ajax({
                    type : 'GET',
                    url  : api_site_url+'/admin/updates/0/'+searchkeyword,
                    success :  function(result) {
                       if(result!='' && $.trim(result)!='<div id="results"></div>') 
                            $('.js_blog_order').html(result);
                        else
                            $('.js_blog_order').html('No results found');
                        
                       $('.load_more').attr('data-keyword',searchkeyword);
                       $('#listingimg').hide();
                     }
                });
            }
         });
	   $("#js-updates-sort").change(function(){
           if($('.blog-border').length>0){
                $('#listingimg').show();
                var ordervalue    = ($(this).val()!='')? $(this).val() :'0';
                var searchkeyword = $("input[name='search_blog_keyword']").val();
                 $('.import_order').hide();
                /*if(searchkeyword == '')
                    var dataurl = api_site_url+'/admin/updates/'+ordervalue;
                else*/
                    var dataurl = api_site_url+'/admin/updatesorder/'+ordervalue+'/'+searchkeyword;
                
                $.ajax({
                    type : 'GET',
                    url  : dataurl,
                    success :  function(result) {
                        
                       $('.js_blog_order').html(result);
                       $('#load_more_button').attr('data-order',ordervalue);
                       $('#load_more_button').attr('data-keyword',searchkeyword);
                       $('#listingimg').hide();
                     }
                }); 
           }    
        });
		 
	   $("#js-blog-sort").change(function(){
		   if($('.blog-border').length>0)
		   {
				$('#listingimg').show();
				var ordervalue 	  = ($(this).val()!='')? $(this).val() :'0';
				var searchkeyword = $("input[name='search_blog_keyword']").val();
				 $('.import_order').hide();
				if(searchkeyword == '')
					var dataurl = api_site_url+'/profile/userblog/'+ordervalue;
				else
					var dataurl = api_site_url+'/profile/userblog/'+ordervalue+'/'+searchkeyword;
				
				$.ajax({
					type : 'GET',
					url  : dataurl,
					success :  function(result) {
						
					   $('.js_blog_order').html(result);
					   $('#load_more_button').attr('data-order',ordervalue);
					   $('#load_more_button').attr('data-keyword',searchkeyword);
					   $('#listingimg').hide();
					 }
				});	
		   }	
        });
		
		$(document).on('click', '.js_search_indblog_button',function () {
			
			var searchkeyword = $("input[name='search_blog_keyword']").val();
			if(searchkeyword == '') {
				$('#error_keyword').show();
			}
			else
			{
				$('#error_keyword').hide();
				$('#listingimg').show();
				 $('.import_order').hide();
				$.ajax({
					type : 'GET',
					url  : api_site_url+'/profile/userblog/0/'+searchkeyword,
					success :  function(result) {
						if(result!='' && $.trim(result)!='<div id="results"></div>')	
							$('.js_blog_order').html(result);
						else
							$('.js_blog_order').html('No results found');
					   
					   $('#load_more_button').attr('data-keyword',searchkeyword);
					   $('#listingimg').hide();
					 }
				});	 
			}
		 });
        
       function date_format(datestring)  {
           month = [];
            month[0] = "Jan";
            month[1] = "Feb";
            month[2] = "Mar";
            month[3] = "Apr";
            month[4] = "May";
            month[5] = "Jun";
            month[6] = "Jul";
            month[7] = "Aug";
            month[8] = "Sep";
            month[9] = "Oct";
            month[10] = "Nov";
            month[11] = "Dec";
            
           var d = new Date(datestring.replace("-", " ", "g"));
           var month = month[d.getMonth()]; 
           var day = d.getDate() >= 10 ? d.getDate() : '0'+d.getDate();
           var year = d.getFullYear(); 
           var hour =  d.getHours() >= 10 ? d.getHours() : '0'+d.getHours();
           var mini = d.getMinutes() >= 10 ? d.getMinutes() : '0'+d.getMinutes();
           var ampm = hour >= 12 ? 'PM' : 'AM';
         
           return month+' '+day+','+' '+year+' '+hour+':'+mini+' '+ampm;
       }
       
   
   
   // load more blog option
   
    $(".load_more").attr("disabled", false);
	$(document).on('click', '.load_more',function () {
          //track user click on "load more" button, righ now it is 0 click
        var blog_track_click = $(this).attr("data-value");  
        var total_pages = $(this).data('totalpage');
        var total_record = $(this).data('totalrecord');
        var check_page   = $(this).data('checkpage');
		var check_order   = $(this).data('order');
		var check_keyword   = $(this).data('keyword');
		
        $(this).hide(); //hide load more button on click
        $('.animation_image').show(); //show loading image

        if(blog_track_click <= total_pages) //user click number is still less than total pages
        {
            //post page number and load returned data into result element
            $.get(api_site_url+'/profile/blog/getblog',{'page': blog_track_click,'individual':check_page,'getorder':check_order,'getkeyword':check_keyword}, function(data) {
                
                $(".load_more").show(); //bring back load more button
               
                $("#results").append(data); //append data received from server
               
                //scroll page smoothly to button id
                $("html, body").animate({scrollTop: $("#load_more_button").offset().top}, 500);
               
                //hide loading image
                $('.animation_image').hide(); //hide loading image once data is received
   
                 //user click increment on load button
                 
                 $(".js-delete-confirm").confirm();
                 
             blog_track_click++;    
           
            }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
                js_alert_popup(thrownError); //alert with HTTP error
                $(".load_more").show(); //bring back load more button
                $('.animation_image').hide(); //hide loading image once data is received
            });
           
           
            if(blog_track_click >= total_pages-1) //compare user click with page number
            {
                //reached end of the page yet? disable load button
                $(".load_more").attr("disabled", "disabled");
            }
         }
		 $(this).attr('data-value',parseInt(blog_track_click)+1);
        });
        
        
   // Leave a reply
   
   $('.replylink').click(function() {
	   $(".leavereplybox").toggle();
   });
	   
	// load more comment option
    var commenttrack_click = 1;
    $(".load_more_comment").attr("disabled", false);
    
	$(document).on('click', '.load_more_comment',function () {
       //track user click on "load more" button, righ now it is 0 click
          
        var total_pages = $(this).data('totalpage');
        var total_record = $(this).data('totalrecord');
		var blogid 		 = $(this).data('blogid');
		var ownerid 		 = $(this).data('ownerid');
		
		
        $(this).hide(); //hide load more button on click
        $('.animation_image').show(); //show loading image

        if(commenttrack_click <= total_pages) //user click number is still less than total pages
        {
            //post page number and load returned data into result element
            $.get(api_site_url+'/profile/blog/getcomments',{'page': commenttrack_click,'blogid':blogid,'blogownid':ownerid}, function(data) {
                
                $(".load_more_comment").show(); //bring back load more button
               
                $("#results").append(data); //append data received from server
               
                //scroll page smoothly to button id
                $("html, body").animate({scrollTop: $("#load_more_button").offset().top}, 500);
               
                //hide loading image
                $('.animation_image').hide(); //hide loading image once data is received
   
                 //user click increment on load button
                 
                 $(".js-delete-confirm").confirm();
                 
             commenttrack_click++;    
           
            }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
                js_alert_popup(thrownError); //alert with HTTP error
                $(".load_more_comment").show(); //bring back load more button
                $('.animation_image').hide(); //hide loading image once data is received
            });
           
           
            if(commenttrack_click >= total_pages-1) //compare user click with page number
            {
                //reached end of the page yet? disable load button
                $(".load_more_comment").attr("disabled", "disabled");
            }
         }
        });
		
		// load more reply comment option
		
		$(".load_more_replycomment").attr("disabled", false);
		
		$(document).on('click', '.load_more_replycomment',function () {
        //track user click on "load more" button, righ now it is 0 click
		//var track_click = $(this).parents('.js_load_more_control').find("#reply_load_more_button").attr("data-value");
		var track_click = $(this).attr("data-value");
		
        var total_pages  = $(this).data('totalpage');
        var total_record = $(this).data('totalrecord');
		var parentcommentid = $(this).data('commentid');
		var blogownid 	 = $(this).data('blogownerid');
		
        $(this).hide(); //hide load more button on click
        $('.animation_image'+parentcommentid).show(); //show loading image

        if(track_click <= total_pages) //user click number is still less than total pages
        {
            //post page number and load returned data into result element
            $.get(api_site_url+'/profile/blog/getreplycomments',{'page': track_click,'blogownid':blogownid,'parentcommentid':parentcommentid}, function(data) {
                
                $(".replycomment"+parentcommentid).show(); //bring back load more button
               
                $("#replyresults"+parentcommentid).append(data); //append data received from server
               
                //scroll page smoothly to button id
              //  $("html, body").animate({scrollTop: $("#reply_load_more_button").offset().top}, 1000);
               
                //hide loading image
                $('.animation_image'+parentcommentid).hide(); //hide loading image once data is received
   
                 //user click increment on load button
                 
                 $(".js-delete-confirm").confirm();
                 
             }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
                js_alert_popup(thrownError); //alert with HTTP error
                $(".replycomment"+parentcommentid).show(); //bring back load more button
                $('.animation_image'+parentcommentid).hide(); //hide loading image once data is received
            });
           
           
            if(track_click >= total_pages-1) //compare user click with page number
            {
                $(".replycomment"+parentcommentid).attr("disabled", "disabled");
            }
         }
		 $(this).attr("data-value",parseInt(track_click)+1)
        });
		
        
        // Post comment
        $(".js-post-comment").click(function(){
             var comments = $('.comments').val();
             var blog_id = $('#blog_id').val();
             var owner_id = $('#owner_id').val();
             var user_id = $('#user_id').val();
             
             var image    = $('input[name=attachment]').val().split('.').pop().toLowerCase();
             var image_arr = ["jpeg","jpg","png","gif","doc","docx","txt","pdf"];
			 
             $('#js_error_comment').hide();
             $("#error_attachment").hide();
             if(comments == '') {
                 $('#js_error_comment').show();
             }
             else if(image_arr.indexOf(image)==-1 && image!=''){
				$("#error_attachment").show();
             }
             else {
				$('.blogcommentload').show(); 
                var formData = new FormData();
                formData.append('attachment', $('input[name=attachment]')[0].files[0]);
                formData.append('comment', comments);
                formData.append('blog_id', blog_id);

                $.ajax({
                url: api_site_url+'/profile/blog/comments', // Url to which the request is send
                 headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                },
                type: "POST",             // Type of request to be send, called as method
                data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                cache: false,             // To unable request pages to be cached
                processData:false,        // To send DOMDocument or non processed data file it is set to false
                success: function(result)   // A function to be called if request succeeds
                {
                     $('.blogcommentload').hide();
                    setTimeout(function(){ 
                        $('.comments').val('');
						$('#blogreplypanel').hide();
						$("input[name=comments]").val("");
                        $("input[type=file]").val("");
                     },300); 
                     $('.add_comments').prepend(result);
                }
                });
             }
        });
		
	// Post reply comment
	$(document).on("click",".js-post-replycomment",function(){ 
	
		 var comment_id = $(this).data('commentid');
		 var blog_id 	= $('#blog_id').val();
		var comments = $('.reply_comments'+comment_id).val();
		 
		 $('#js_error_replycomment'+comment_id).hide();
		 if(comments == '') {
			 $('#js_error_replycomment'+comment_id).show();
		 }
		 else {
			$('.blogreplycommentload'+comment_id).show();
			var formData = new FormData();
			formData.append('comments', comments);
			formData.append('blog_id', blog_id);
			formData.append('comment_id', comment_id);

			$.ajax({
			url: api_site_url+'/profile/blog/replycomments', // Url to which the request is send
			 headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",             // Type of request to be send, called as method
			data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(result)   // A function to be called if request succeeds
			{
				 $('.blogreplycommentload'+comment_id).hide();
				setTimeout(function(){ 
					$('.reply_comments'+comment_id).val('');
				 },300); 
				 $('.add_replycomments'+comment_id).prepend(result);
				 $('#commentreplypanel'+comment_id).hide();
			}
			});
			 
		 }	
	});	
		
      // vote for blog
      $(document).on("click",".js-vote",function(){ 
            var name    = $(this).attr("name");
            var blogid = $(this).data("id");
            if(name == 'up'){
                   var votecount = $('.vote_up'+blogid).text();
                $.get(api_site_url+'/profile/blog/favourite',{'page':'4','blogid':blogid,'type':'up'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                      $('.up'+blogid).removeClass('med-green');
                      $('.up'+blogid).addClass('med-orange');
                      $('.vote_up'+blogid).html(parseInt(votecount)+1+' Votes');
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
            
            if(name == 'down'){
                 var votecount = $('.vote_down'+blogid).text();
                $.get(api_site_url+'/profile/blog/favourite',{'page':'4','blogid':blogid,'type':'down'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                       $('.down'+blogid).removeClass('med-green');
                      $('.down'+blogid).addClass('med-orange');
                       $('.vote_down'+blogid).html(parseInt(votecount)+1+' Votes');
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
        });
        
        // vote for comments
        
        $(document).on("click",".js-vote_comments",function(event){ 
            var name    = $(this).attr("name");
            var blogid = $(this).data("id");
            var commentid = $(this).data("commentid");
            if(name == 'up'){
                   var votecount = $('.vote_up'+commentid).text();
                $.get(api_site_url+'/profile/blog/commentsfavourite',{'page':'5','blogid':blogid,'commentid':commentid,'type':'up'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                      $('.vote_up'+commentid).html(parseInt(votecount)+1);
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
            
            if(name == 'down'){
                 var votecount = $('.vote_down'+commentid).text();
                $.get(api_site_url+'/profile/blog/commentsfavourite',{'page':'5','blogid':blogid,'commentid':commentid,'type':'down'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                       $('.vote_down'+commentid).html(parseInt(votecount)+1);
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
        });
		
		// vote for reply comments
        $(document).on("click",".js_vote_replycomments",function(event){ 
            var name    = $(this).attr("name");
            var parentcommentid = $(this).data("id");
            var commentid = $(this).data("commentid");
            if(name == 'up'){
                   var votecount = $('.vote_upreply'+commentid).text();
                $.get(api_site_url+'/profile/blog/commentsfavourite',{'page':'7','parentid':parentcommentid,'commentid':commentid,'type':'up'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                      $('.vote_upreply'+commentid).html(parseInt(votecount)+1);
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
            
            if(name == 'down'){
                 var votecount = $('.vote_downreply'+commentid).text();
                $.get(api_site_url+'/profile/blog/commentsfavourite',{'page':'7','parentid':parentcommentid,'commentid':commentid,'type':'down'},function(getthumb, status){
                  if(getthumb=='1') {
                      js_alert_popup('Thanks for the vote!');
                       $('.vote_downreply'+commentid).html(parseInt(votecount)+1);
                  }
                  else if(getthumb=='2') {
                      js_alert_popup('Already voted!');
                  }
                });
            }
        });
		
		
		$(document).on('click', '.js_show_commentreply_box',function () {
			var commentid = $(this).data("id");
			$('#commentreplypanel'+commentid).show();
		});
		
		$(document).on('click', '#js_show_blogreply_box',function () {
			document.getElementById("blogreplypanel").style.display = "block";
		});
		
		$(document).on('change','input[name="attachment"]', function(){
			 var file   = $(this).val();
			 $('.js-display-attachment').html('&nbsp;&nbsp;'+file); 
		});