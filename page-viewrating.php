<?php
/**
 * This file is responsible for the view of ratings.
 * 
 */
acf_form_head(); // Acf Header

 if(isset($_REQUEST['userid']) && !empty($_REQUEST['userid'])){
	$userid = sanitize_text_field($_REQUEST['userid']);
}
$selectedmonth = '';
if(isset($_REQUEST['month']) && !empty($_REQUEST['month'])){
	$selectedmonth = sanitize_text_field($_REQUEST['month']);
}
	
?>
<div class="nt_rating_main" id="wpbody" role="main">
  <div class="wrap">
	<div class="maintable-bordered">

<?php

// Get User By User Id
$user = get_user_by( 'id', $userid );
// Get USre Name
$username = $user->user_login;
$username = sanitize_user($username);
 
//get your custom posts ids as an array
$posts = get_posts(
	array(
    'post_type'   => 'employee-ratings',
    'post_status' => 'published',
    'meta_key' => 'user_name',
    'meta_value' => $userid
   ) 

   );
?>	
<!---- Print Form --->	      			  	
<table class="wp-list-table widefat fixed striped">
	<thead>
	  <tr>
	    <th>
	    <span><?php echo esc_html__('User','navthemes_employee_ratings'); ?></span>
	    </th>
	    <th><?php echo esc_html__('Month','navthemes_employee_ratings'); ?></th>  
	  </tr>
	</thead>

	<tbody>
	  <tr>
	  <td><span><?php echo $username; ?></span></td>
	  <td>
	     <form>	
		    <div>
				<input type="text" id="selectedmonth" name="selectedmonth">
				<select name="monthname" class="form-control" id="getmonth"  onchange="month(this.value)">
					<option><?php echo esc_html__('Select','navthemes_employee_ratings'); ?></option> 
						<?php
						/* 
						 Get month
						*/
						for($m=1; $m<=12; ++$m){
							// Month number
						    $month =  date('F', mktime(0, 0, 0, $m, 1));
						    // Month Name
						    $monthnum =  date('m', mktime(0, 0, 0, $m, 1));
					?>
					<option value='<?php echo $monthnum; ?>' <?php if(isset($_REQUEST['month']) && !empty($_REQUEST['month'])){ if(($_REQUEST['month'])== $monthnum) echo "selected";} ?>><?php echo $month ?></option>
					<?php } ?>
				</select>
			</div>
		</form>
	  </td>
	</tr>
  </tbody>
</table>

<div style=" margin-top:20px;">
<?php

//loop over each post
$noofweek = 0;
$monthlyavg='';
$numposts = count($posts); // count post
$indexcount = 0;
if(isset($posts) && !empty($posts)){
foreach($posts as $postsfields){
	$noofweek++;
	 // Post Meta
    $username = get_post_meta($postsfields->ID,"user_name",true);
    // Current Date And Time
    $datetime = date("Y/m/d");
    // Returns a string Year.	
   	$monthyear = substr($datetime,5,2);
    $monthnumeric= $monthyear;
    // Get Month 
	$dateObj   = DateTime::createFromFormat('!m', $monthnumeric);
	$monthName = $dateObj->format('F');
	// Get Weeks
    $week = get_post_meta($postsfields->ID,"week",true);
    // Get professionalism Data  
    $professionalism = get_post_meta($postsfields->ID,"categories_professionalism",true);
    // Technical knowledge Data
    $knowledge = get_post_meta($postsfields->ID,"categories_efficiency_and_technical_knowledge",true);
    // Get Proactiveness Data
    $proactiveness = get_post_meta($postsfields->ID,"categories_proactiveness",true);
    // Help Teammates Data
    $helpingteamates = get_post_meta($postsfields->ID,"categories_helping_your_teammates",true);
    // Leavs Data
    $leaves = get_post_meta($postsfields->ID,"categories_leaves",true);
    // Weeklay Total rating
   	$weeklyavg = $professionalism+ $knowledge+$proactiveness+$helpingteamates+$leaves; 
   	//Average Rating
   	$monthlyavg += round($weeklyavg/5,2); 
  ?>
	<?php 

	// Conduction Caheck For Select month And Give Month
 	 if($monthnumeric == $selectedmonth){ 
 	?>

 	<!--------------------------------------------------------------------------------------------------------------------- Print Tabel  ------------------------------------------------------------------------------------------------->

	<table class="wp-list-table widefat fixed striped">
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Month','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $monthName; ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Week','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $week ?></th>
	 	</tr>
	 	<tr>
	 		<td ><strong><?php echo esc_html__('Categories : ','navthemes_employee_ratings'); ?></strong></td>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Professionalism','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $professionalism; ?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Efficiency and technical knowledge','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $knowledge; ?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Helping your Teammates','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $proactiveness; ?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Proactiveness','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $helpingteamates ;?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Leaves','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><?php echo $leaves; ?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
	 	</tr>
	 	<tr>
	 		<td colspan="2"><?php echo esc_html__('Weekly Rating','navthemes_employee_ratings'); ?></td>
	 		<th colspan="2"><font size="2"><?php echo round($weeklyavg/5,2); ?><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></font></th>
	 	</tr>

	 	<?php 
	 	  if(++$indexcount === $numposts) { ?>
			<tr>
		 		<td colspan="2"><?php echo esc_html__('Monthly Average','navthemes_employee_ratings'); ?></td>
		 		<th colspan="2"><font size="5"><?php 
		 		echo round($monthlyavg/$noofweek,2); ?></font><?php echo esc_html__(' out of 10','navthemes_employee_ratings'); ?></th>
		 	</tr>
		<?php } ?>
   	</table>
 	<?php 
 		} // Close Month Conduction
	   } // Close Foreach
	 } // Close isset Conduction
   ?>	

	</br >
		
	</div>
 </div>
</div>
</div>

<script type="text/javascript">
    
   $ = jQuery;

</script>

<script type="text/javascript">
/*
	Redireact Same Page
*/
function month(month){
 document.getElementById('selectedmonth').value=month;
 window.location.href = "<?php echo site_url(); ?>/wp-admin/edit.php?post_type=employee-ratings&page=rating-view&action=view-rating&userid=<?php  echo $userid; ?>&month=" + month; 
}
</script>

<?php 