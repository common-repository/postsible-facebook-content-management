<?php

/**
 * @package Postsible
 * @version 1.2
 */
/*
  Plugin Name: Postsible : Facebook Content Management
  Plugin URI: http://www.postsible.com/wordpress.php
  Description: Postsible For Wordpress will help you to integrate your post somewhere else to facebook Profile or Page.  It will help notifying your friend on facebook that you have posted a new feed and help increasing your blog traffic. Many more option at Postsible App on Facebook. The Postsible service is free of use. <a href="http://www.postsible.com">Postsible Website</a> | <a href="http://apps.facebook.com/postsible/">Postsible App On Facebook</a> | <a href="http://apps.facebook.com/postsible/admin">Postsible Admin On Facebook</a> | <a href="http://www.facebook.com/apps/application.php?id=114031942020751">Postsible Fan Page</a>
  Author: Postsible Team
  Version: 1.2
  Author URI: http://www.postsible.com/
 */

### Include Function
include_once('fn.php');
include_once('form_helper.php');

### Install ###
global $psb_db_version;
$psb_db_version = "1.0";
register_activation_hook(__FILE__, 'postsible_install');

function postsible_install() {
  global $wpdb;
  global $jal_db_version;

  $table_name = $wpdb->prefix . "psb_setting";
  $table_name2 = $wpdb->prefix . "psb_data";

  $sql = "CREATE TABLE " . $table_name . " (
	    psb_sid int(10) unsigned NOT NULL auto_increment,
             psb_sname varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
             psb_svalue text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
             PRIMARY KEY  (psb_sid),
             UNIQUE KEY psb_sname (psb_sname)
    );";

  $sql2 = "CREATE TABLE " . $table_name2 . " (
	     psb_id bigint(20) unsigned NOT NULL,
              psb_page bigint(20) unsigned NOT NULL,
              psb_text text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_title text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_media_des text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_share_link text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_share_thumb text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_success tinyint(1) unsigned NOT NULL,
              psb_date_gmt int(11) NOT NULL,
              psb_delay VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              psb_api_id VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              PRIMARY KEY  (psb_id),
              KEY psb_api_id (psb_api_id),
              KEY psb_page (psb_page),
              KEY psb_success (psb_success),
              KEY psb_date_gmt (psb_date_gmt)
    );";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
  dbDelta($sql2);
  add_option("jal_db_version", $psb_db_version);
}

### Add Jquery to Post Page ###

function my_scripts_method() {
  wp_deregister_script('jquery');
  wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js');
  wp_enqueue_script('jquery');

  /* Register and Enqueue Custom Script */
  wp_register_script('custom_script', plugins_url('/script.js', __FILE__));
  wp_enqueue_script('custom_script');

  wp_register_script('msdd_script', plugins_url('/dropdown/js/jquery.dd.js', __FILE__));
  wp_enqueue_script('msdd_script');

  wp_enqueue_style('msdd_style', plugins_url('/dropdown/dd.css', __FILE__));
  wp_enqueue_style('msdd_style');

  wp_enqueue_style('psb_style', plugins_url('/style.css', __FILE__));
  wp_enqueue_style('psb_style');
}

add_action('wp_enqueue_scripts', 'my_scripts_method');

/* Add Jquery to Plugin Page */
add_action('admin_init', 'my_scripts_method');


### SideBar and Screen ###
add_action('admin_menu', 'postsible_admin_menu');

### Delete Permanent Event
add_action('deleted_post', 'check_deleted');
function check_deleted(){
  if(@$_GET['post'] != '' and @$_GET['action'] == 'delete' and @$_GET['_wpnonce'] != ''){
    delete_psb_data($_GET['post']);
  }
}

function delete_plugin(){
  global $wpdb;  
  $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "psb_data");
  $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "psb_setting");
}

function postsible_admin_menu() {
  $appName = 'Postsible';
  $appID = 'postsible';
  $main_slug = $appID . '-main';
  $history_slug = $appID . '-history';
  $contact_slug = $appID . '-contact';
  $page_slug = $appID . '-page';

  add_menu_page($appName, $appName, 'administrator', $main_slug, 'main_screen', plugins_url('/icon.png', __FILE__));
  add_submenu_page($main_slug, 'Setting', 'Setting', 'administrator', $main_slug, 'main_screen');
  add_submenu_page($main_slug, 'History', 'History', 'administrator', $history_slug, 'history_screen');
  add_submenu_page($main_slug, 'Contact', 'Contact', 'administrator', $contact_slug, 'contact_screen');
  add_submenu_page($main_slug, 'Page', 'Page', 'administrator', $page_slug, 'page_screen');
  add_action('save_post', 'myplugin_save_postdata');
}

function main_screen() {
  ## If have login data
  update_fb_user();
  
  if (isset($_POST['action'])) {
    action_switch($_POST);
    $notify = 'Data has been saved.';
  }

  ## If change account
  if (isset($_GET['change_account']) and $_GET['change_account'] == 'true' and is_admin()) {
    remove_fb_db();
  }

  include_once ('setting.php');
}

function history_screen() {
  global $wpdb;
  
  $per_page = 20;
  if(!@$_GET['paged']){
    $paged = 1;
  }else{
    $paged = $_GET['paged'];
  }
  $data = get_psb_history($paged,$per_page);
  $query = $data['query'];
  $total_row = $data['total'];
  if($total_row > $per_page){
    $total = ceil($total_row/$per_page);
  }else{
    $total = 1;
  }
  
  $args = array(
                  'base' => str_replace( $total, '%#%', get_pagenum_link( $total ) ),
                  'format' => '&paged=%#%',
                  'current' => max( $paged, get_query_var('paged') ),
                  'total' => $total
              );
  
  include_once('history.php');

}

function contact_screen(){
  if (isset($_POST['action'])) {
    $return = action_switch($_POST);
    if($return == 'true'){
      $notify = 'Contact has been sent.';
    }elseif($return == 'data'){
      $notify = 'Please insert form data.';
    }elseif($return == 'email'){
      $notify = 'Please insert valid email.';
    }else{
      $notify = 'Contact Limit.';
    }
  }
  include_once ('contact.php');
}

function page_screen(){
    if ($result = get_fb_user()) {
      if (!$profile = check_token(@$result['uid'], @$result['token'])) {
        echo '<script>top.location.href = "'.admin_url().'admin.php?page=postsible-main'.'"</script>';
      }
    }
    
    include_once ('page.php');
}

### Metabox ###
add_action('add_meta_boxes', 'myplugin_add_custom_box');

function myplugin_add_custom_box() {
  add_meta_box(
          'myplugin_sectionid', __('Postsible', 'myplugin_textdomain'), 'myplugin_inner_custom_box', 'post'
  );
  add_meta_box(
          'myplugin_sectionid', __('Postsible', 'myplugin_textdomain'), 'myplugin_inner_custom_box', 'page'
  );
}

/* Prints the box content */

function myplugin_inner_custom_box($post) {

  // Use nonce for verification
  wp_nonce_field(plugin_basename(__FILE__), 'postsible');

  ### Get user data
  $result = get_fb_user();
  $user_data = api('user', 'get', $result);
  $profile = check_token(@$result['uid'], @$result['token']);

  /*$user_data['data'][0]['timezone'] and */
  include_once('custom_box.php');
}

/* When the post is saved, saves our custom data */

function myplugin_save_postdata($post_id) {
  global $post;
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if (!wp_verify_nonce(@$_POST['postsible'], plugin_basename(__FILE__)))
    return;


  // Check permissions
  if ('page' == $_POST['post_type']) {
    if (!current_user_can('edit_page', $post_id))
      return;
  }
  else {
    if (!current_user_can('edit_post', $post_id))
      return;
  }
  
  ### Ignore blank page id
  if ($_POST['pages_id'] == 'false' OR $_POST['pages_id'] == '') {
    return;
  }

  // OK, we're authenticated: we need to find and save the data
  if ($_POST['psb_share_thumb'] == "%auto%") {
    $share_thumb = auto_thumbs($_POST['content']);
  } else {
    $share_thumb = prep_url($_POST['psb_share_thumb']);
  }
  
  ### Post Date
  if (isset($_POST['aa'])) {
    $date_str = $_POST['aa'].'-'.$_POST['mm'].'-'.$_POST['jj'].' '.$_POST['hh'].':'.$_POST['mn'].':00';
    
    $user_GMT = strtotime($date_str.' -'.get_option('gmt_offset').' hours');
  }

  ### Plus Delay
  if (isset($_POST['psb_delay']) and $_POST['psb_delay'] != '') {
    $user_GMT = strtotime($_POST['psb_delay'], $user_GMT);
    $delay_time = $_POST['psb_delay'];
  }else{
    $delay_time = '';
  }
  
  ### Make GMT 0
  $user_GMT = local_to_gmt($user_GMT);
  $original_GMT =& $user_GMT;
  
  ### Get API Key if available
  $psb_data = get_psb_data($_POST['ID']);
  $result = get_fb_user();
  
  $api['pages_id'] = $_POST['pages_id'];
  $api['share_name'] = mktitle($_POST['post_title'], $_POST['psb_title']);
  $api['share_text'] = str_replace('%url%',mk_post_url($_POST['post_ID']),$_POST['psb_text']);
  $api['media_des'] = mkdes($_POST['content'], $_POST['psb_media_des']);
  $api['share_link'] = mk_post_url($_POST['post_ID']) ;
  $api['share_thumb'] = $share_thumb;
  $api['psb_success'] = 0;
  $api['original_gmt'] = $original_GMT;
  $api['delay'] = $delay_time;
  $api['time_schedule'] = $user_GMT;
  $api['wp_id'] = $_POST['ID'];
  $api['psb_api_id'] = (@$psb_data['psb_api_id']?@$psb_data['psb_api_id']:'');
  $api['token'] = @$result['token'];

  ### Save data to database
  set_psb_data($api);

  ### Ignore send to API When draft
  if($_POST['post_status'] != 'publish'){
    return;
  }
  
  if($api['share_name'] == '' OR $api['media_des'] == ''){
    return;
  }

  ### Send to API
  $return = api('feed', 'post', $api);

  if($return){
    update_psb_api_id($_POST['ID'], $return);
  }
  
  
  // Do something with $mydata 
  // probably using add_post_meta(), update_post_meta(), or 
  // a custom table (see Further Reading section below)
}

?>
