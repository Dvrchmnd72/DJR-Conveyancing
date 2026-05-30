<?php
add_filter('show_admin_bar', '__return_false');

function ava_googlemaps_apikey() {
        if ( ! defined( 'DJR_GOOGLE_MAPS_API_KEY' ) || DJR_GOOGLE_MAPS_API_KEY === '' ) {
                return;
        }

        $prefix = is_ssl() ? 'https' : 'http';
        wp_deregister_script('avia-google-maps-api');
        wp_register_script(
                'avia-google-maps-api',
                $prefix . '://maps.google.com/maps/api/js?key=' . rawurlencode( DJR_GOOGLE_MAPS_API_KEY ),
                array('jquery'),
                '3',
                true
        );
        wp_enqueue_script('avia-google-maps-api');
}
add_action('init', 'ava_googlemaps_apikey');

/* custom login logo */

function my_custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
}
add_action('login_head', 'my_custom_login');

add_action( 'after_setup_theme', 'wpdocs_theme_setup' );
function wpdocs_theme_setup() {
    //add_image_size( 'team-thumb', 500, 370, true );
}

function my_login_logo_url_title() {
	return 'MRM';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function simple_theme_name_scripts() {
	$nonce_style = wp_create_nonce(time());
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' );

   	wp_enqueue_style( 'custom-style', get_bloginfo('stylesheet_directory').'/css/custom-style.css',array('avia-style'),$nonce_style);

    wp_enqueue_script( 'sm-script', get_bloginfo('stylesheet_directory').'/script.js', array('jquery'), '', true);

    $djrVars = array( 
	    'ajaxurl' => admin_url( 'admin-ajax.php' )
	);
    wp_localize_script( 'sm-script', 'DjrAjax', $djrVars );
}
add_action( 'wp_enqueue_scripts', 'simple_theme_name_scripts', 10);

add_theme_support('avia_template_builder_custom_css');

function shortcode_social_media(){
	ob_start();
	echo '<div class="footer-social-box">';
		avia_social_media_icons($args = array(), $echo = true);
	echo '</div>';

	return ob_get_clean();
}

add_shortcode('theme_social_media', 'shortcode_social_media');

// add_filter( 'avf_google_heading_font', 'avia_add_heading_font');
// function avia_add_heading_font($fonts){
// 	$fonts['Open Sans'] = 'Open+Sans:300,400,600';
// 	return $fonts;
// }

// add_filter( 'avf_google_content_font', 'avia_add_content_font');
// function avia_add_content_font($fonts){
// 	$fonts['Open Sans'] = 'Open+Sans:300,400,600';
// 	return $fonts;
// }

//add_action ('simple_main_header','simple_main_header');
function simple_main_header(){
	echo '<div class="my-header-rigth">
			<p>Queenland Conveyancing Service</p>
			<p>DJR Conveyancing is a division of DJR Lawyers Pty Ltd ACN 638 060 634</p>
	</div>';
}

add_shortcode('my_slider_caption','my_slider_caption_func');
function my_slider_caption_func(){
	ob_start();
	echo '<div class="my-con-cap">';

	echo '<div class="my-col">';
	echo '<div class="my-col-iner">';
	echo '<a href="'.get_permalink(47).'"><img src="'.get_bloginfo('stylesheet_directory').'/images/icon-1.jpg"></a>';
	echo '<a href="'.get_permalink(47).'"><h4>Buying</h4></a>';
	echo '</div>';
	echo '</div>';

	echo '<div class="my-col">';
	echo '<div class="my-col-iner">';
	echo '<a href="'.get_permalink(47).'"><img src="'.get_bloginfo('stylesheet_directory').'/images/icon-2.jpg"></a>';
	echo '<a href="'.get_permalink(47).'"><h4>Selling</h4></a>';
	echo '</div>';
	echo '</div>';

	echo '<h3>Get an Instant Quote</h3>';
	echo '</div>';
	return ob_get_clean();
}

function simple_footer_ssmd_func(){
	ob_start();
	echo '<div class="my-sosial-box-icon-ftr">';
		avia_social_media_icons($args = array(), $echo = true);
	echo'</div>';
	return ob_get_clean();
}
add_shortcode('simple_footer_ssmd','simple_footer_ssmd_func');

function djr_blog_func(){
	ob_start();
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args=array(
		'post_type'=>'post',
		'posts_per_page'=>10,
		'paged' => $paged,
		'tax_query'=>array(
			array(
				'taxonomy'=>'category',
				'terms'=>'3',
			)
		)
	);
	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		echo '<div class="post_blog">';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$blog_date = get_the_date( 'l, j F Y', $post->ID );
			?> <div class="blog-box-top">
				<div class="bloc-clm">
					<div class="blog-img">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full') ?></a>
					</div>
					<div class="blog-content">
						<p class="blog-date"><?php echo $blog_date; ?></p>
						<a href="<?php the_permalink(); ?>"><h3 class="blog-title"><?php the_title(); ?></h3></a>
						<div class="blog-excerpt"><?php the_excerpt() ?></div>
						<a class="btn-blog" href="<?php the_permalink(); ?>">Read More</a>
					</div>
				</div>
			</div>
			<?php 
		}
		echo '</div>';
		if (function_exists('wp_pagenavi')) { 
			wp_pagenavi( array( 'query' => $the_query ) ); 
		}
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}
	return ob_get_clean();
} 

add_shortcode('djr_blog', 'djr_blog_func');


add_action('ava_after_main_container', 'blog_breadcrumb');

function blog_breadcrumb(){
	global $post;
	ob_start();
	if( is_single() ){
		echo '<div class="my-bredcrumb">';
		// bcn_display();
		echo '<a href="'.get_permalink(111).'">Blog</a> / <span>'.get_the_title($post->ID).'</span>';
		echo '</div>';
	}
	echo ob_get_clean();
}

$councils = get_posts( array(
	    'post_type'      => 'council',
	    'posts_per_page' => -1
	) );

	$purchasing = get_field_object('field_5e1e8b5afe051', 262);
	$selling = get_sub_field('field_5e1ea67b7853c', 262);

	// echo'<pre>';
	// print_r($purchasing);
	// echo'</pre>';


function djr_quote_form_func($attr){
	ob_start();
	$councils = get_posts( array(
	    'post_type'      => 'council',
	    'posts_per_page' => -1,
	    'orderby' => 'title',
	    'order' => 'asc'
	) );

	$purchasing = get_field_object('field_5e1e8b5afe051', $council[0]->ID);
	$selling = get_field_object('field_5e1ea67b7853c', $council[0]->ID);

	$html = '<div class="mrm_djr_quote_form">';
		$html .= '<div class="mrm_djr_quote_form_header">';
			$html .= '<div class="mrm_djr_quote_form_logo">';
				$html .= '<img src="'.get_bloginfo('stylesheet_directory').'/images/logo-quotes.png">';
			$html .= '</div>';
			$html .= '<div class="mrm_djr_quote_form_headtext">';
				$html .= '<h3>'.__('Get an instant no obligation online quote', 'mrm').'</h3>';
				$html .= '<span>'.__('Engage our services by contacting us or providing our details to your property agent', 'mrm').'</span>';
			$html .= '</div>';
			$html .= '<div class="clear"></div>';
		$html .= '</div>';
		$html .= '<div class="mrm_djr_quote_form_content">';
		$html .= '<table>';
			$html .= '<tr class="hide_mobile">';
				$html .= '<td colspan="3"><label>I am</label></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td width="33.333334%"><label class="hide_desktop">I am</label><input type="radio" value="'.$purchasing['name'].'" name="property_for" id="'.$purchasing['name'].'" checked="checked"><label for="'.$purchasing['name'].'">'.$purchasing['label'].'</label></td>';
				$html .= '<td width="33.333334%"><input type="radio" value="'.$selling['name'].'" name="property_for" id="'.$selling['name'].'"><label for="'.$selling['name'].'">'.$selling['label'].'</label></td>';
				$html .= '<td width="33.333334%"></td>';
			$html .= '</tr>';
			$html .= '<tr class="hide_mobile">';
				$html .= '<td colspan="2"><label>Local Council / Shire</label></td><td><label>Property Type</label></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="2">';
				$html .= '<label class="hide_desktop">Local Council / Shire</label><select name="council">';
				if ( $councils ) {
    				foreach ( $councils as $council ) {
						$html .= '<option value="'.$council->ID.'">'.$council->post_title.'</option>';
					}
				}
				$html .= '</select>';
				$html .= '</td>';
				$html .= '<td>';
				$html .= '<label class="hide_desktop">Property Type</label><select name="property_type">';
				if(!empty($purchasing['sub_fields'])){
					foreach ($purchasing['sub_fields'] as $ptype) {
						$html .= '<option value="'.$ptype['name'].'">'.$ptype['label'].'</option>';
					}
				}
				$html .= '</select>';
				$html .= '</td>';
			$html .= '</tr>';
			// $html .= '<tr class="hide_mobile">';
			// 	$html .= '<td><label>First Name</label></td>';
			// 	$html .= '<td><label>Last Name</label></td>';
			// 	$html .= '<td></td>';
			// $html .= '</tr>';
			// $html .= '<tr>';
			// 	$html .= '<td><label class="hide_desktop">First Name</label><input type="text" name="first_name"></td>';
			// 	$html .= '<td><label class="hide_desktop">Last Name</label><input type="text" name="last_name"></td>';
			// 	$html .= '<td></td>';
			// $html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="3"><button type="submit">QUICK INSTANT QUOTE</button></td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '</div>';
	$html .= '</div>';

	$html .= ob_get_clean();
	return $html;
}

add_shortcode( 'djr_quote_form', 'djr_quote_form_func' );

function djr_search_quote($attr){
	ob_start();

	if($_REQUEST['property_for'] == "purchasing"){
		$data = get_field_object('field_5e1e8b5afe051', $_REQUEST['council']);
		$xtext = 'Purchase';
	}elseif($_REQUEST['property_for'] == "selling"){
		$data = get_field_object('field_5e1ea67b7853c', $_REQUEST['council']);
		$xtext = 'Selling';
	}

	// echo '<pre>';
	// print_r($data);
	// echo '</pre>';

	foreach ($data['sub_fields'] as $sfl) {
		if($_REQUEST['property_type'] == $sfl['name']){
			$xtitle = $sfl['label'];
		}
	}


	$html = '<div class="mrm_djr_quote_result">';
		$html .= '<h2>'.__('Receive Your Fixed Price Quote Via Email', 'mrm').'</h2><br>';
		$html .= '<h4>'.__('Quote Summary', 'mrm').'</h4>';
		$html .= '<div class="mrm_djr_quote_summary">';
			$html .= '<table>';
			$html .= '<tr>';
				$html .= '<td width="50%">';
					$html .= '<div class="mrm_djr_quote_summary_header"><h4>'.__('PROFESSIONAL FEE', 'mrm').'</h4></div>';
					$html .= '<table class="mrm_djr_quote_summary_list">';

						if($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == ''){
							$html .= '<tr>';
								$html .= '<td>'.$xtext.' '.$xtitle.'</td>';
								$fee = $data['value'][$_REQUEST['property_type']]['professional_fee'];
								$html .= '<td>'.number_format($data['value'][$_REQUEST['property_type']]['professional_fee'], 2).'</td>';
							$html .= '</tr>';
						}else{
							$html .= '<tr>';
								$html .= '<td>'.$xtext.' '.$data['sub_fields']['label'].'</td>';
								$html .= '<td><del>'.number_format($data['value'][$_REQUEST['property_type']]['professional_fee'], 2).'</del></td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>*'.(($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == 'fixed') ? '$'.$data['value'][$_REQUEST['property_type']]['discount_amount'] : $data['value'][$_REQUEST['property_type']]['discount_amount'].'%').' website discount applied</td>';
								if($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == 'fixed'){
									$html .= '<td>'.number_format((($data['value'][$_REQUEST['property_type']]['professional_fee']) - ($data['value'][$_REQUEST['property_type']]['discount_amount'])), 2).'</td>';
									$fee = (($data['value'][$_REQUEST['property_type']]['professional_fee']) - ($data['value'][$_REQUEST['property_type']]['discount_amount']));
								}else{
									$html .= '<td>'.number_format((($data['value'][$_REQUEST['property_type']]['professional_fee']) - (($data['value'][$_REQUEST['property_type']]['professional_fee']) * (($data['value'][$_REQUEST['property_type']]['discount_amount']) / 100))), 2).'</td>';
									$fee = (($data['value'][$_REQUEST['property_type']]['professional_fee']) - (($data['value'][$_REQUEST['property_type']]['professional_fee']) * (($data['value'][$_REQUEST['property_type']]['discount_amount']) / 100)));
								}
							$html .= '</tr>';
						}
						
					$html .= '</table>';
					$html .= '<div class="mrm_djr_quote_summary_header"><h4>'.__('DISBURSEMENTS FEE', 'mrm').'</h4></div>';
					$html .= '<table class="mrm_djr_quote_summary_list">';

					foreach ($data['sub_fields'] as $sf) {
						if($sf['name'] == $_REQUEST['property_type']){
							$prc = 0;
							$prctot = 0;
							foreach ($sf['sub_fields'] as $ssf) {
								if($ssf['name'] != ''){
									if($ssf['name'] != 'professional_fee'){
										if($ssf['name'] != 'professional_fee_discount'){
											if($ssf['name'] != 'discount_amount'){

												$meta = $data['name'].'_'.$sf['name'].'_'.$ssf['name'];
												$prc = get_post_meta($_REQUEST['council'], $meta, true);
												if($prc == ''){
													$prctot = ($prctot + 0);
												}else{
													$prctot = ($prctot + $prc);

													$html .= '<tr>';
														$html .= '<td>'.$ssf['label'].'</td>';
														$html .= '<td>'.number_format($prc, 2).'</td>';
													$html .= '</tr>';
												}
												
											}
										}
									}
								}
							}
						}
						
					}

					$total = ($fee + $prctot);

					
					$html .= '</table>';
				$html .= '</td>';
				$html .= '<td width="50%">';
					$html .= '<div class="mrm_djr_quote_summary_header"><h4>'.__('OPTIONAL SERVICES', 'mrm').'</h4></div>';
					$html .= '<table class="mrm_djr_quote_summary_list reverse">';
						$html .= '<tr>';
							$html .= '<td>Pre-signing contract review for standard REIQ contract</td>';
							$html .= '<td></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>Pre-signing contract review for “Off the Plan” contracts<br>
							<small><i>(Our services includes a comprehensive review of the contract and letter to the client summarising the key terms and conditions of any amendments)</i></small></td>';
							$html .= '<td></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>Preparation of the contract for sale<br>
							<small><i>(If you do not have an agent we can prepare the contract of sale)</i></small></td>';
							$html .= '<td></td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td>Rest assured with our no unconditional contract, no fee policy<br>
								<small><i>If your contract does not go unconditional and is terminated early (eg. finance not approved or building and pest not satisfactory) no professional fees will be charged. You will however be liable for any search costs incurred.</i></small></td>';
							$html .= '<td></td>';
						$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>';
					$html .= '<div class="mrm_djr_quote_summary_total"><table><tr><td><h3>'.__('TOTAL', 'mrm').'</h3></td><td><h3>$'.number_format($total, 2).'</h3></td></tr></table></div>';

				$html .= '</td>';
				$html .= '<td width="50%"></td>';
			$html .= '</tr>';
			$html .= '</table>';
		$html .= '</div>';
		$html .= '<h2>'.__('Enter your details or', 'mrm').' <a href="'.get_permalink(82).'" class="djr_contact_button">CONTACT US</a></h2> <br>';
		$html .= '<div class="mrm_djr_quote_step_form">';
		$html .= '<form name="mrm_djr_quote_form" method="post">';
		$html .= '<table>';
			$html .= '<tr class="hide_mobile">';
				$html .= '<td width="50%"><label>First Name*</label></td>';
				$html .= '<td width="50%"><label>Last Name*</label></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><label class="hide_desktop">First Name*</label><input type="text" name="first_name" value="'.$_REQUEST['first_name'].'"></td>';
				$html .= '<td><label class="hide_desktop">Last Name*</label><input type="text" name="last_name" value="'.$_REQUEST['last_name'].'"></td>';
			$html .= '</tr>';
			$html .= '<tr class="hide_mobile">';
				$html .= '<td><label>Email Address*</label></td>';
				$html .= '<td><label>Mobile Number</label></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><label class="hide_desktop">Email Address*</label><input type="text" name="email_address"></td>';
				$html .= '<td><label class="hide_desktop">Mobile Number</label><input type="text" name="mobile_number"></td>';
			$html .= '</tr>';

			$html .= '<tr class="hide_mobile">';
				$html .= '<td colspan="2"><label>Property Address (optional)</label></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="2"><label class="hide_desktop">Property Address (optional)</label><input type="text" name="property_address" placeholder="Street Address, Suburb, Postal Code"></td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td colspan="2" class="mrm_djr_quote_form_response"></td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td colspan="2" align="right"><button type="submit">SUBMIT</button></td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<input type="hidden" name="property_for" value="'.$_REQUEST['property_for'].'">';
		$html .= '<input type="hidden" name="council" value="'.$_REQUEST['council'].'">';
		$html .= '<input type="hidden" name="property_type" value="'.$_REQUEST['property_type'].'">';
		$html .= '</form>';
		$html .= '</div>';
	$html .= '</div>';

	$html .= ob_get_clean();

	$response['html'] = $html;

	echo json_encode($response);
	die();
}

add_action( 'wp_ajax_djr_search_quote', 'djr_search_quote' );
add_action( 'wp_ajax_nopriv_djr_search_quote', 'djr_search_quote' );

function djr_submit_quote(){

	if($_REQUEST['property_for'] == "purchasing"){
		$data = get_field_object('field_5e1e8b5afe051', $_REQUEST['council']);
		$xtext = 'Purchase';
	}elseif($_REQUEST['property_for'] == "selling"){
		$data = get_field_object('field_5e1ea67b7853c', $_REQUEST['council']);
		$xtext = 'Selling';
	}

	foreach ($data['sub_fields'] as $sfl) {
		if($_REQUEST['property_type'] == $sfl['name']){
			$xtitle = $sfl['label'];
		}
	}

	$council_label = get_post($_REQUEST['council']);

	$msg_top = '<html>
	    <head>
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <title>DJR Conveyancing Quote</title>
	    </head>
	    
	    <body style="background-color: #dddddd; font-family: Verdana, Geneva, sans-serif; font-size: 14px; color: #333;">
	    <div style="width: 100%; max-width:800px; margin:0px auto;">
	    	
	        <table width="100%" border="0" align="center" cellpadding="30" cellspacing="0">
	          <tr>
	            <td bgcolor="#000000"><img src="'.get_bloginfo('stylesheet_directory').'/images/email-logo.png" /></td>
	          </tr>
	          <tr>
	            <td bgcolor="#FFFFFF">';

	$msg_content_admin = 'Hi Administrator,
	                
	                <p>'.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' downloaded a new fixed price quote from our website DJR Conveyancing.</p>';

	$msg_content_user = 'Hi '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].',
	                
	                <p>Please see your online quote below - contact us on <a href="mailto:info@DJRconveyancing.com.au">info@DJRconveyancing.com.au</a> for further information.</p>';
	                
	$msg_content = '<h3>QUOTE SUMMARY</h3>
	                <div style="border: #dddddd 1px solid; padding: 3px;">
	                    <div style="background-color: #000000; color: #ffffff; padding: 1px 20px;">
	                        <h4>PROFESSIONAL FEE</h4>
	                    </div>
	                    <table width="100%" border="0" align="center" cellpadding="10" cellspacing="0">
	                        <tbody>';


						if($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == ''){
							$html .= '<tr>';
								$msg_content .= '<td>'.$xtext.' '.$xtitle.' ('.$council_label->post_title.')</td>';
								$fee = $data['value'][$_REQUEST['property_type']]['professional_fee'];
								$msg_content .= '<td align="right">'.number_format($data['value'][$_REQUEST['property_type']]['professional_fee'], 2).'</td>';
							$html .= '</tr>';
						}else{
							$html .= '<tr>';
								$msg_content .= '<td>'.$xtext.' '.$xtitle.' ('.$council_label->post_title.')</td>';
								$msg_content .= '<td align="right"><del>'.number_format($data['value'][$_REQUEST['property_type']]['professional_fee'], 2).'</del></td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$msg_content .= '<td>*'.(($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == 'fixed') ? '$'.$data['value'][$_REQUEST['property_type']]['discount_amount'] : $data['value'][$_REQUEST['property_type']]['discount_amount'].'%').' website discount applied</td>';
								if($data['value'][$_REQUEST['property_type']]['professional_fee_discount'] == 'fixed'){
									$msg_content .= '<td align="right">'.number_format((($data['value'][$_REQUEST['property_type']]['professional_fee']) - ($data['value'][$_REQUEST['property_type']]['discount_amount'])), 2).'</td>';
									$fee = (($data['value'][$_REQUEST['property_type']]['professional_fee']) - ($data['value'][$_REQUEST['property_type']]['discount_amount']));
								}else{
									$msg .= '<td align="right">'.number_format((($data['value'][$_REQUEST['property_type']]['professional_fee']) - (($data['value'][$_REQUEST['property_type']]['professional_fee']) * (($data['value'][$_REQUEST['property_type']]['discount_amount']) / 100))), 2).'</td>';
									$fee = (($data['value'][$_REQUEST['property_type']]['professional_fee']) - (($data['value'][$_REQUEST['property_type']]['professional_fee']) * (($data['value'][$_REQUEST['property_type']]['discount_amount']) / 100)));
								}
							$msg_content .= '</tr>';
						}

	$msg_content .= '</tbody>
	                    </table>
	                    <div style="background-color: #000000; color: #ffffff; padding: 1px 20px;">
	                        <h4>DISBURSEMENTS FEE</h4>
	                    </div>
	                    <table width="100%" border="0" align="center" cellpadding="10" cellspacing="0">
	                        <tbody>';

	                        foreach ($data['sub_fields'] as $sf) {
								if($sf['name'] == $_REQUEST['property_type']){
									$prc = 0;
									$prctot = 0;
									foreach ($sf['sub_fields'] as $ssf) {
										if($ssf['name'] != ''){
											if($ssf['name'] != 'professional_fee'){
												if($ssf['name'] != 'professional_fee_discount'){
													if($ssf['name'] != 'discount_amount'){

														$meta = $data['name'].'_'.$sf['name'].'_'.$ssf['name'];
														$prc = get_post_meta($_REQUEST['council'], $meta, true);
														if($prc == ''){
															$prctot = ($prctot + 0);
														}else{
															$prctot = ($prctot + $prc);

															$msg_content .= '<tr>';
																$msg_content .= '<td>'.$ssf['label'].'</td>';
																$msg_content .= '<td align="right">'.number_format($prc, 2).'</td>';
															$msg_content .= '</tr>';
														}
													}
												}
											}
										}
									}
								}
								
							}

							$total = ($fee + $prctot);


	$msg_content .= '</tbody>
	                    </table>
	                    
	                    <table width="100%" border="0" align="center" cellpadding="10" cellspacing="0" style="background-color: #000000; color: #ffffff;">
	                        <tbody>
	                            <tr>
	                                <td>
	                                    <div style="background-color: #000000; color: #ffffff; padding: 1px 10px;">
	                                    <h4>TOTAL</h4>
	                                    </div>
	                                </td>
	                                <td align="right">
	                                    <div style="background-color: #000000; color: #ffffff; padding: 1px 10px;">
	                                    <h4>$'.number_format($total, 2).'</h4>
	                                    </div>
	                                </td>
	                            </tr>
	                        </tbody>
	                    </table>
	                </div>
	                <div style="border: #dddddd 1px solid; padding: 20px; margin-top:30px;">
	                    <h3>PERSONAL INFORMATION</h3>
	                    <table width="100%" border="0" cellspacing="0" cellpadding="10">
	                          <tr>
	                            <td width="150">First Name</td>
	                            <td width="1">:</td>
	                            <td>'.$_REQUEST['first_name'].'</td>
	                          </tr>
	                          <tr>
	                            <td>Last Name</td>
	                            <td>:</td>
	                            <td>'.$_REQUEST['last_name'].'</td>
	                          </tr>
	                          <tr>
	                            <td>Email Address</td>
	                            <td>:</td>
	                            <td>'.$_REQUEST['email_address'].'</td>
	                          </tr>
	                          <tr>
	                            <td>Mobile Number</td>
	                            <td>:</td>
	                            <td>'.$_REQUEST['mobile_number'].'</td>
	                          </tr>
	                          <tr>
	                            <td>Property Address</td>
	                            <td>:</td>
	                            <td>'.$_REQUEST['property_address'].'</td>
	                          </tr>
	                    </table>
	                </div>

	            </td>
	          </tr>
	          <tr>
	            <td bgcolor="#cd995f"><a href="'.get_bloginfo('url').'" style="text-decoration: none; color: #ffffff;">djrconveyancing.com.au</a></td>
	          </tr>
	        </table>
	    </div>
	    </body>
	</html>';

	

	$site_name = 'DJR Conveyancing';
	$admin_email_address = 'info@djrconveyancing.com.au';
	//$admin_email_address = 'digital@myrobotmonkey.com.au';
	//$admin_email_address = 'webmaster@simple.web.id';

	//Msg fo user
	$msg_user = $msg_top.' '.$msg_content_user.' '.$msg_content;
	$headers[] = 'X-MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = 'X-To: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$_REQUEST['email_address'].'>';
	$headers[] = 'X-From: '.$site_name.' <'.$admin_email_address.'>';
	//$headers[] = 'X-Reply-To: '.$site_name.' <'.$admin_email_address.'>';
	$subject = 'Quote Summary Price from '.$site_name.'.';
	wp_mail( $_REQUEST['email_address'], $subject, $msg_user, $headers );

	// Msg fo administrator
	$msg_admin = $msg_top.' '.$msg_content_admin.' '.$msg_content;
	$headers_admin[] = 'X-MIME-Version: 1.0';
	$headers_admin[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers_admin[] = 'X-To: '.$site_name.' <'.$admin_email_address.'>';
	$headers_admin[] = 'X-From: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$_REQUEST['email_address'].'>';
	//$headers_admin[] = 'X-Reply-To: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$_REQUEST['email_address'].'>';
	$subject_admin = $_REQUEST['first_name'].' '.$_REQUEST['last_name'].' Downloaded a Quote Summary Price from '.$site_name.'.';
	wp_mail( $admin_email_address, $subject_admin, $msg_admin, $headers_admin );

	//$to      = $_REQUEST['email_address'];


	//$headers[] = 'X-MIME-Version: 1.0';
	//$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	//$headers[] = 'X-To: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$to.'>';
	//$headers[] = 'X-From: '.$site_name.' <'.$admin_email_address.'>';
	//$headers[] = 'X-Reply-To: '.$site_name.' <'.$admin_email_address.'>';
	//mail($to, $subject, $msg_user, implode("\r\n", $headers));


	//$headersx[] = 'X-MIME-Version: 1.0';
	//$headersx[] = 'Content-type: text/html; charset=iso-8859-1';
	//$headersx[] = 'X-To: '.$site_name.' <'.$admin_email_address.'>';
	//$headersx[] = 'X-From: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$to.'>';
	//$headersx[] = 'X-Reply-To: '.$_REQUEST['first_name'].' '.$_REQUEST['last_name'].' <'.$to.'>';
	//mail($admin_email_address, $subject_admin, $msg_admin, implode("\r\n", $headersx));

	$response['html'] = "<h3>Thank you for your enquiry, we will be in contact within the next 24 hours.</h3>";
	echo json_encode($response);
	die();
}

add_action( 'wp_ajax_djr_submit_quote', 'djr_submit_quote' );
add_action( 'wp_ajax_nopriv_djr_submit_quote', 'djr_submit_quote' );

function date_func( $atts ){
	return date('Y');
}
add_shortcode( 'date', 'date_func' );