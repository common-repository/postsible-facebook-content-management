<?php

function current_url() {
  $url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  return $url;
}

function url_login() {
  $url_login = 'http://www.postsible.com/api/data/login?wp_page=' . urlencode(current_url());
  return $url_login;
}

function update_fb_user() {
  global $wpdb;
  if (@$_GET['token'] != '' and @$_GET['uid'] != '') {
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_setting WHERE psb_sname = 'token'");
    $wpdb->insert($wpdb->prefix . 'psb_setting', array('psb_sname' => 'token', 'psb_svalue' => addslashes($_GET['token'])));
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_setting WHERE psb_sname = 'uid'");
    $wpdb->insert($wpdb->prefix . 'psb_setting', array('psb_sname' => 'uid', 'psb_svalue' => addslashes($_GET['uid'])));
    redirect_pi();
  }
}

function redirect_pi($url = 'postsible-main') {
  $url = admin_url() . 'admin.php?page=' . $url;
  echo "<script type='text/javascript'>top.location.href = '" . $url . "';</script>";
  exit();
}

function remove_fb_db() {
  global $wpdb;
  $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_setting WHERE psb_sname = 'token'");
  $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_setting WHERE psb_sname = 'uid'");
  redirect_pi();
}

function get_fb_user() {
  global $wpdb;
  $query = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "psb_setting ");
  $query = result($query);
  return $query;
}

function get_psb_data($id) {
  global $wpdb;
  $query = $wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "psb_data WHERE psb_id = " . $id, ARRAY_A);
  if ($query) {
    return $query[0];
  }
}

function delete_psb_data($id) {
  global $wpdb;
  $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_data WHERE psb_id = $id");
}

function get_psb_history($page = 1, $per_page = 30) {
  global $wpdb;
  if (!$page) {
    $page = 1;
  }
  $limit = ($page - 1) * $per_page;
  $query = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "psb_data ORDER BY psb_id DESC LIMIT $limit, $per_page", ARRAY_A);
  $total = $wpdb->get_results("SELECT COUNT(*) FROM " . $wpdb->prefix . "psb_data", ARRAY_A);
  if ($query) {
    $data['query'] = $query;
    $data['total'] = (int) $total[0]['COUNT(*)'];
    return $data;
  }
}

function set_psb_data($data) {
  global $wpdb;
  $data_set['psb_id'] = $data['wp_id'];
  $data_set['psb_page'] = $data['pages_id'];
  $data_set['psb_text'] = $data['share_text'];
  $data_set['psb_title'] = $data['share_name'];
  $data_set['psb_media_des'] = $data['media_des'];
  $data_set['psb_share_link'] = $data['share_link'];
  $data_set['psb_share_thumb'] = $data['share_thumb'];
  $data_set['psb_success'] = 0;
  $data_set['psb_date_gmt'] = $data['original_gmt'];
  $data_set['psb_delay'] = $data['delay'];
  if (get_psb_data($data['wp_id'])) {
    $wpdb->update($wpdb->prefix . "psb_data", $data_set, array('psb_id' => $data_set['psb_id']));
    return true;
  } else {
    $wpdb->insert($wpdb->prefix . "psb_data", $data_set);
    return true;
  }
}

function result($query) {
  foreach ($query as $value) {
    $result[$value->psb_sname] = $value->psb_svalue;
  }
  if (isset($result)) {
    return $result;
  }
}

function check_token($uid, $token) {
  $url = "https://graph.facebook.com/$uid?access_token=$token";
  $result = @file_get_contents($url);
  if ($result) {
    return @json_decode($result, true);
  }
}

function update_psb_api_id($post_id, $key) {
  global $wpdb;
  $data_set['psb_api_id'] = $key;
  $data_set['psb_success'] = 1;
  $wpdb->update($wpdb->prefix . "psb_data", $data_set, array('psb_id' => $post_id));
  return true;
}
 
function profile_picture($facebookID, $size) {
  return '<img src="https://graph.facebook.com/' . $facebookID . '/picture?type=square" width="' . $size . '"/> ';
}

function api($url, $method, $data_string) {
  $url = "http://www.postsible.com/api/restful/" . strtolower($method) . '_' . strtolower($url);
  $data_string = "data=" . json_encode($data_string);
  $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERAGENT, $agent);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $result = curl_exec($ch);
  curl_close($ch);
  return json_decode($result, true);
}

function action_switch($data) {
  global $wpdb;
  switch ($data['action']) {
    case 'save_setting':
      api('user', 'post', $data);
      $wpdb->query("DELETE FROM " . $wpdb->prefix . "psb_setting WHERE psb_sname = 'default_page'");
      $wpdb->insert($wpdb->prefix . 'psb_setting', array('psb_sname' => 'default_page', 'psb_svalue' => addslashes($_POST['pages_id'])));
      $_SESSION['notify'] = 'Saved';
      break;
    case 'contact':
      if(!@$data['from'] OR !@$data['subject'] OR !@$data['description']){
        return 'data';
      }
      if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $data['from'])){ 
        return 'email';
      }
      return api('contact', 'post', $data);
      break;
    default:
      break;
  }
}

function mk_post_url($post_id) {
  return site_url() . '/?p=' . $post_id;
}

function get_post_psb($id) {
  return get_post($id, ARRAY_A);
}

function get_author_psb($uid) {
  global $wpdb;
  $query = $wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "users WHERE ID = " . $uid, ARRAY_A);
  return $query[0]['display_name'];
}

function foreach_option_list($data, $result,$encode = false) {
  global $psb_data;
  if ($psb_data) {
    $selected = @$psb_data['psb_page'];
  } else {
    $selected = @$result['default_page'];
  }
  $dd = '';
  if (is_array($data)) {
    foreach ($data as $key => $val) {
        if($encode){
            $en_key = encode($key);
        }else{
            $en_key = $key;
        }
        $dd .= '<option value="' . $en_key . '" title="https://graph.facebook.com/' . $key . '/picture?type=square" ' . (@$selected == $key ? 'selected="selected"' : '') . '>' . stripslashes($val) . '</option>';
    }
    return $dd;
  }
}

function get_target_box($result, $encode = false) {
  $result['type'] = 'profile';
  $page_profile = api('page_list', 'get', $result);
  $result['type'] = 'page';
  $page_page = api('page_list', 'get', $result);
  $result['type'] = 'group';
  $page_group = api('page_list', 'get', $result);
  $result['type'] = 'app';
  $page_app = api('page_list', 'get', $result);
  $page_box['profile'] = foreach_option_list($page_profile, $result, $encode);
  $page_box['page'] = foreach_option_list($page_page, $result, $encode);
  $page_box['group'] = foreach_option_list($page_group, $result, $encode);
  $page_box['app'] = foreach_option_list($page_app, $result, $encode);
  return $page_box;
}

function mktitle($data, $pattern) {
  return str_replace('%title%', $pattern, $data);
}

function mkdes($data, $pattern) {
  return stripslashes(strip_tags(str_replace('%description%', $pattern, $data)));
}

function auto_thumbs($data) {
  $image_regex = '/<img[^>]*' . 'src=[\"|\'](.*)[\"|\']/Ui';
  @preg_match_all($image_regex, stripslashes($data), $img, PREG_PATTERN_ORDER);
  @$images_array = @$img[1];
  if (@$images_array[0]) {
    return $images_array[0];
  } else {
    return '';
  }
}

function encode($code)
    {
        return base64_encode($code.'_'.'thisispost');    
    }

function prep_url($str = '') {
  if ($str == 'http://' OR $str == '') {
    return '';
  }

  $url = parse_url($str);

  if (!$url OR !isset($url['scheme'])) {
    $str = 'http://' . $str;
  }

  return $str;
}

function delay_data() {
  $data[''] = 'No Delay';
  $data['+15 minutes'] = '+15 Minutes';
  $data['+30 minutes'] = '+30 Minutes';
  $data['+45 minutes'] = '+45 Minutes';
  $data['+1 hour'] = '+1 Hour';
  $data['+4 hour'] = '+4 Hour';
  $data['+8 hour'] = '+8 Hour';
  $data['+12 hour'] = '+12 Hour';
  $data['+1 day'] = '+1 Day';
  $data['+3 day'] = '+3 Day';
  $data['+5 day'] = '+5 Day';
  $data['+1 week'] = '+1 Week';
  $data['+2 week'] = '+2 Week';
  $data['+3 week'] = '+3 Week';
  $data['+1 month'] = '+1 Month';
  return $data;
}

function local_to_gmt($time = '') {
  if ($time == '')
    $time = time();

  return mktime(gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
}

?>
