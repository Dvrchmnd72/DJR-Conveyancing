jQuery(document).ready(function($){

	var searchQuoteForm = null;
	$(document).on('click', '.mrm_djr_quote_form button', function(event){
		event.preventDefault();

		if(searchQuoteForm !== null) {
	    	searchQuoteForm.abort();
	    }

	    var property_for = $(".mrm_djr_quote_form input[name='property_for']:checked").val();
	    var council = $(".mrm_djr_quote_form select[name='council']").val();
	    var property_type = $(".mrm_djr_quote_form select[name='property_type']").val();

	    var data = {
			'action': 'djr_search_quote',
			'property_for': property_for,
			'council': council,
			'property_type': property_type,
		};

	    //Ajax request
		searchQuoteForm = $.ajax({
	        type: "POST",
	        url: DjrAjax.ajaxurl,
	        data: data,
	        dataType: 'JSON',
	        cache: false,
	        beforeSend: function(jQxhr, settings) {
			    $('.mrm_djr_quote_result_wrap').html('<i class="fa fa-cog fa-spin fa-fw"></i> Please wait..');
			    $('.mrm_djr_quote_form button').html('<i class="fa fa-cog fa-spin fa-fw"></i> Please wait..').attr('disabled', true);
			},
	        success: function( data, textStatus, jQxhr ){
	           	$('.mrm_djr_quote_result_wrap').html(data.html);
	           	$('.mrm_djr_quote_form button').html('QUICK INSTANT QUOTE').attr('disabled', false);
	           	var mrm_djr_quote_result_wrap = $('.mrm_djr_quote_result_wrap');
	           	var offset = mrm_djr_quote_result_wrap.offset();
	    		
	    		if ( $(window).width() > 767 ) {
			   		$('html, body').animate({scrollTop:(offset.top - 110)}, 1800);
			   	}else{
			   		$('html, body').animate({scrollTop:(offset.top + 10)}, 1800);
			   	}
	        },
	        error: function( jqXhr, textStatus, errorThrown ){
	            console.log( errorThrown );
	        }

	    });

	});


	var submitQuoteForm = null;
	$(document).on('click', 'form[name=mrm_djr_quote_form] button', function(event){
		event.preventDefault();

		var holder = 0;

		if(submitQuoteForm !== null) {
	    	submitQuoteForm.abort();
	    }

	    var property_for = $("form[name=mrm_djr_quote_form] input[name='property_for']").val();
	    var council = $("form[name=mrm_djr_quote_form] input[name='council']").val();
	    var property_type = $("form[name=mrm_djr_quote_form] input[name='property_type']").val();
	    var first_name = $("form[name=mrm_djr_quote_form] input[name='first_name']").val();
	    var last_name = $("form[name=mrm_djr_quote_form] input[name='last_name']").val();
	    var email_address = $("form[name=mrm_djr_quote_form] input[name='email_address']").val();
	    var mobile_number = $("form[name=mrm_djr_quote_form] input[name='mobile_number']").val();
	    var property_address = $("form[name=mrm_djr_quote_form] input[name='property_address']").val();


	    if(first_name == ""){
	    	$("form[name=mrm_djr_quote_form] input[name='first_name']").addClass('form_error');
	    	holder = 1;
	    }else{
	    	$("form[name=mrm_djr_quote_form] input[name='first_name']").removeClass('form_error');
	    }

	    if(last_name == ""){
	    	$("form[name=mrm_djr_quote_form] input[name='last_name']").addClass('form_error');
	    	holder = 1;
	    }else{
	    	$("form[name=mrm_djr_quote_form] input[name='last_name']").removeClass('form_error');
	    }

	    if(email_address == ""){
	    	$("form[name=mrm_djr_quote_form] input[name='email_address']").addClass('form_error');
	    	holder = 1;
	    }else{
	    	if( !validateEmail(email_address)) { 
	    		$("form[name=mrm_djr_quote_form] input[name='email_address']").addClass('form_error');
	    		holder = 2;
	    	}else{
	    		$("form[name=mrm_djr_quote_form] input[name='email_address']").removeClass('form_error');
	    	}
	    	
	    }

	    var data = {
			'action': 'djr_submit_quote',
			'property_for': property_for,
			'council': council,
			'property_type': property_type,
			'first_name': first_name,
			'last_name': last_name,
			'email_address': email_address,
			'mobile_number': mobile_number,
			'property_address': property_address,
		};


		if(holder >= 1){
			if(holder == 1){
				$(".mrm_djr_quote_form_response").html('<span style="color: red;">All fields with * is required!</span>');
			} else if(holder == 2){
				$(".mrm_djr_quote_form_response").html('<span style="color: red;">Your email is wrong format!</span>');
			}
			return false;
		}

	    //Ajax request
		submitQuoteForm = $.ajax({
	        type: "POST",
	        url: DjrAjax.ajaxurl,
	        data: data,
	        dataType: 'JSON',
	        cache: false,
	        beforeSend: function(jQxhr, settings) {
			    $('form[name=mrm_djr_quote_form] .mrm_djr_quote_form_response').html('<i class="fa fa-cog fa-spin fa-fw"></i> Please wait..');
			    $('form[name=mrm_djr_quote_form] button').attr('disabled', true);
			},
	        success: function( data, textStatus, jQxhr ){
	        	//alert(data.msg_admin);
	           	$('.mrm_djr_quote_result').html(data.html);

	           	$(".mrm_djr_quote_form input[name='first_name']").val('');
	           	$(".mrm_djr_quote_form input[name='last_name']").val('');

	           	var mrm_djr_quote_result_wrap = $('.mrm_djr_quote_result_wrap');
	           	var offset = mrm_djr_quote_result_wrap.offset();
	    		$('html, body').animate({scrollTop:(offset.top - 110)}, 1800);
	        },
	        error: function( jqXhr, textStatus, errorThrown ){
	            console.log( errorThrown );
	        }

	    });


	});


	$(document).on('click', '.box-quotes', function(event){
		event.preventDefault();
		var $target = $(this).attr('id');
		var $res = $target.replace("quote-", "");

		$('.mrm_djr_quote_form input[type=radio]#'+$res).attr('checked',true);

		var mrm_djr_quote_form = $('.mrm_djr_quote_form');
	    var offset = mrm_djr_quote_form.offset();
	    if ( $(window).width() > 767 ) {
	   		$('html, body').animate({scrollTop:(offset.top - 200)}, 1800);
	   	}else{
	   		$('html, body').animate({scrollTop:(offset.top - 10)}, 1800);
	   	}
	});

	function validateEmail($email) {
  		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  		return emailReg.test( $email );
	}
	
});