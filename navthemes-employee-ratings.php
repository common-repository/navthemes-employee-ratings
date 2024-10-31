<?php
/**
 * Plugin Name:  NavThemes Employee Ratings
 * Description:  This Plugin provides you function so you can submit ratings for employees. which can further be used to evaluvate employee performance. Ratings been done on 5 pre-defined Parameters.
 * Version:      1.1.1
 * Author:       NavThemes
 * Author URI:   https://www.navthemes.com/
 * License:      GPL2
 * Text Domain:  navthemes_employee_ratings
 *
 */
ob_start(); 
if( ! class_exists('ACF') ) :
  include_once('admin/acf.php' ); 
  add_filter('acf/settings/show_admin', '__return_false');
endif; 
if( function_exists('acf_add_local_field_group') ): 
  include('fields.php');
endif; 
/*
   Enqueue Admin Styles And Scripts
 */
if(!function_exists('navthemes_employee_ratings_enqueue')):
function navthemes_employee_ratings_enqueue() { 
      if( isset( $_REQUEST['post_type'] ) && ($_REQUEST['post_type'])=='employee-ratings' ) {
         wp_enqueue_style( 'ntratingtyle', plugins_url( '/assets/style.css', __FILE__ ) );
      }      
    }
add_action( 'admin_enqueue_scripts', 'navthemes_employee_ratings_enqueue' );
endif;
/*
   Enqueue Timesheet FrontEnd Styles
 */
if(!function_exists('navthemes_employee_ratings_timesheet_enqueue')):
function navthemes_employee_ratings_timesheet_enqueue() { 
         wp_enqueue_style( 'nttimesheetstyle', plugins_url( '/assets/timesheetstyle.css', __FILE__ ) );      
    }
add_action( 'wp_enqueue_scripts', 'navthemes_employee_ratings_timesheet_enqueue' );
endif;
/*
   Register Employee Rating Custom Post Type
 */
if(!function_exists('navthemes_employee_ratings_custom_post_type')):
  function navthemes_employee_ratings_custom_post_type() {
    register_post_type( 'employee-ratings',
      array(
        'labels' => array(
          'name' => __( 'Employee Ratings','navthemes_employee_ratings' ),
          'singular_name' => __( 'employee-ratings','navthemes_employee_ratings'),
        ),
        'public' => true,
         'supports' => array('title','custom-fields'),
        )
    );
  }

add_action( 'init', 'navthemes_employee_ratings_custom_post_type' );
endif;
/*
   Register Employee Rating Custom Post Type
 */
if(!function_exists('navthemes_employee_ratings_project_custom_post_type')):
  function navthemes_employee_ratings_project_custom_post_type() {
    register_post_type( 'timesheet-project',
      array(
        'labels' => array(
          'name' => __( 'Timesheet Project','navthemes_employee_ratings' ),
          'singular_name' => __( 'timesheet-project','navthemes_employee_ratings'),
        ),
        'public' => true,
         'supports' => array('title','custom-fields'),
        )
    );
  }
add_action( 'init', 'navthemes_employee_ratings_project_custom_post_type' );
endif;
if(!function_exists('read_data')){
function read_data(){
  $valuetime = $_REQUEST['post_id'];
  return $valuetime;
 }
}
if(!function_exists('navthemes_employee_ratings_cpt_save_fields')):

function navthemes_employee_ratings_cpt_save_fields( $post_id  ) {

if(read_data()==''){    
  $name = get_field('user_name', $post_id);
   $user = get_user_by( 'id', $name );
   $nt_post = array(
        'ID'           => $post_id,
        'post_title'   => get_the_title()
     );
  wp_update_post( $nt_post );
}
else {
    /*
        Task update from front end 
    */
  if(isset($_REQUEST['acf'])){
      $time_sheet_current_user = get_current_user_id();
      $time_sheet_date = date("m/d/Y");
      $time_sheet_data = $_REQUEST['acf'];
      $time_sheet_values = $time_sheet_data['field_5c0540c3f0867'];
      $data_count = 0;
      $val = count(get_user_meta($time_sheet_current_user));
      $user = "user_".$time_sheet_current_user;
      if(isset($time_sheet_values) && !empty($time_sheet_values)){
      foreach ($time_sheet_values as $value) {
          $val++;
       update_user_meta( $time_sheet_current_user , 'tasks_'.$val.'_task_name', $value['field_5c0540d6f0868']);
       update_user_meta( $time_sheet_current_user , 'tasks_'.$val.'_description', $value['field_5c0540e7f0869']);
       update_user_meta( $time_sheet_current_user , 'tasks_'.$val.'_time', $value['field_5c0540f6f086a']);
       update_user_meta( $time_sheet_current_user , 'tasks_'.$val.'_date', $time_sheet_date);
       update_user_meta( $time_sheet_current_user , 'tasks_'.$val.'_project', $value['field_5c29a4774085b']);
      }
	  wp_update_post( $post_id ); 
    }
  }
}

}
add_action('acf/save_post', 'navthemes_employee_ratings_cpt_save_fields', 19);
endif;
/*
   add menu page
 */
if(!function_exists('navthemes_employee_ratings_add_pages')):
function navthemes_employee_ratings_add_pages(){  
add_submenu_page( "edit.php?post_type=employee-ratings", __( 'Rating Reports', 'navthemes_employee_ratings' ) , __( 'Reports', 'navthemes_employee_ratings' ), "manage_options", "rating-view", "navthemes_employee_ratings_view" ); 
add_submenu_page( "edit.php?post_type=employee-ratings", __( 'Time Sheet Reports', 'navthemes_employee_ratings' ) , __( 'Time Sheet Reports', 'navthemes_employee_ratings' ), "manage_options", "timesheet-view", "navthemes_employee_ratings_time_view" ); 
}
add_action('admin_menu', 'navthemes_employee_ratings_add_pages');
endif;
if(!function_exists('navthemes_employee_ratings_view')):
function navthemes_employee_ratings_view(){
 if( isset($_REQUEST['action']) && sanitize_text_field($_REQUEST['action'])=='view-rating') : 
  include('page-viewrating.php');
 else:  
 ?>
<div class="nt_rating_main" id="wpbody" role="main">
  <div class="wrap">
      <div class="container">
           <div class="left-borders">
              <h1>
               <?php echo esc_html__('View Rating','navthemes_employee_ratings'); ?>
              </h1>
           </div>
      </div>
      <div>
      <table class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th>
              <span><?php echo esc_html__('Users','navthemes_employee_ratings'); ?></span>
              </th>
              <th><?php echo esc_html__('Action','navthemes_employee_ratings'); ?></th>
            </tr>
          </thead>
            <?php
              $user = get_users();
              if(isset($user) && !empty($user)){
              foreach ($user as $value) {
                $alluser = $value->user_login;
                $userid =  $value->ID;
                ?>
                    <tbody>
                      <tr>
                      <td><span><?php echo $alluser; ?><span></td>
                      <td><a href="<?php echo esc_url(site_url() . '/wp-admin/edit.php?post_type=employee-ratings&page=rating-view&action=view-rating&userid='.$userid); ?>"><?php echo esc_html__('View Rating','navthemes_employee_ratings'); ?></a></td>
                      </tr>
                    </tbody>
                <?php 
              } }
            ?>
         </table>
      </div>
  </div>
</div>
<?php endif; } endif; 

/*
   Timesheet 
 */
function navthemes_employee_ratings_timesheet_view() {
  ob_start();
  include('page-timesheet.php');
  return ob_get_clean(); 
 }
 ?>
<?php
add_shortcode( 'nt_timesheet' , 'navthemes_employee_ratings_timesheet_view' ); 
if(!function_exists('navthemes_employee_ratings_time_view')):
function navthemes_employee_ratings_time_view(){
  include('page-viewtimesheet.php');
} endif;
add_action( 'admin_footer', 'navthemes_employee_ratings_javascript' ); 
function navthemes_employee_ratings_javascript() { ?>
    <script type="text/javascript" >
    jQuery("#ajax-time-sheet-form").submit(function (e){
      e.preventDefault();
        var date = jQuery('#date').val();
        var userid = jQuery('#userid option:selected').val();
        var data = {
            'action': 'my_action',
            'userid': userid,
            'date': date,
          };
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('.time_sheet_body').html(response); 
        });
    });
    </script> <?php
}
add_action( 'wp_ajax_my_action', 'navthemes_employee_ratings_timesheet_js' );
function navthemes_employee_ratings_timesheet_js() {
    global $wpdb; // this is how you get access to the database
    $time_sheet_user_meta = get_user_meta( $_POST['userid']);
  ?>
           <table class="wp-list-table widefat fixed striped">
              <tr>
                <th colspan="2"><?php echo esc_html__('Task','navthemes_employee_ratings'); ?></th>
                <th colspan="2"><?php echo esc_html__('Description','navthemes_employee_ratings'); ?></th>
                <th colspan="2"><?php echo esc_html__('Time','navthemes_employee_ratings'); ?></th>
                <th colspan="2"><?php echo esc_html__('Date','navthemes_employee_ratings'); ?></th>
                <th colspan="2"><?php echo esc_html__('Project','navthemes_employee_ratings'); ?></th>
              </tr>
              <?php
              $numdata = count($time_sheet_user_meta);
              $indexcount = 0;
              if(isset($time_sheet_user_meta) && !(empty($time_sheet_user_meta))){
               foreach ($time_sheet_user_meta as $value) {
                $data_count++;
                $project_title = get_the_title( $time_sheet_user_meta['tasks_'.$data_count.'_project']['0']);
                $date=$time_sheet_user_meta['tasks_'.$data_count.'_date']['0'];
                if(strtotime($_POST['date']) == strtotime($date)){
                ?>
              <tr>
                <td colspan="2"><?php echo $time_sheet_user_meta['tasks_'.$data_count.'_task_name']['0']; ?></td>
                <td colspan="2"><?php echo $time_sheet_user_meta['tasks_'.$data_count.'_description']['0']; ?></td>
                <td colspan="2"><?php echo $time_sheet_user_meta['tasks_'.$data_count.'_time']['0']; ?></td>
                <td colspan="2"><?php echo $time_sheet_user_meta['tasks_'.$data_count.'_date']['0']; ?></td>
                <td colspan="2"><?php echo $project_title; ?></td>
              </tr>
              <?php }else{ 
                if(++$indexcount === $numdata) { ?>
              <tr>
                <td colspan="2"><?php echo esc_html__('No data exists','navthemes_employee_ratings'); ?></td>
              </tr>
                 <?php  } } } } ?> 
            </table>
<?php  
  wp_die(); // this is required to terminate immediately and return a proper response
}