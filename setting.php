<?php
## Check User
if ($result = get_fb_user()) {
  if (!$profile = check_token(@$result['uid'], @$result['token'])) {
    $login_url = '<a href="' . url_login() . '"><img src="'.plugins_url('postsible-facebook-content-management/fbconnect.png').'" boder="0"/></a>';
  }
} else {
  $profile = false;
  $login_url = '<a href="' . url_login() . '"><img src="'.plugins_url('postsible-facebook-content-management/fbconnect.png').'" boder="0"/></a>';
}
?>
<div class="wrap">
  <h2 id="header">Setting</h2>
  <div id="pi_body">
    <?
    if ($profile) {
      ### If all corrected display this zone
      ### API to get user data

      $user_data = api('user', 'get', $result);

      ### Timezone Data
      $all_timezone = api('all_timezone', 'get', $result);
      $all_timezone[''] = "--- Select Timezone ---";

      ### Display Current User
      if (is_admin()) {

        $change_account = ' <a href="' . current_url() . '&change_account=true"/>(Change Facebook Account)</a>';
      }

      //echo profile_picture($result['uid'], 50) . ' ' . $profile['name'] . $change_account;
      $page_box = get_target_box($result);
      ?>

      <div><form action="" method="post" id="setting">
          <table class="form-table">
            <tbody>
              <tr valign="bottom">
                <th scope="row">
                  <label for="account">Account:</label>
                </th>
                <td>
                  <?php echo profile_picture($result['uid'], 25);?> <?php echo $profile['name'] . $change_account; ?>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label for="page_text">Default Target:</label>
                </th>
                <td>
                  <select name='pages_id' id='page_text' class='required fleft dropimg' dir='select1'>      
                  <option value='false'>-- Select Target --</option>
                  <?php if(@$page_box['profile']):?>
                  <optgroup label="-- Profile --">
                  <?php echo $page_box['profile'];?>
                  </optgroup>
                  <? endif;?>
                  <?php if(@$page_box['page']):?>
                  <optgroup label="-- Page --">
                  <?php echo $page_box['page'];?>
                  </optgroup>
                  <? endif;?>
                  <?php if(@$page_box['group']):?>
                  <optgroup label="-- Group --">
                  <?php echo $page_box['group'];?>
                  </optgroup>
                  <? endif;?>
                  <?php if(@$page_box['app']):?>
                  <optgroup label="-- Application --">
                  <?php echo $page_box['app'];?>
                  </optgroup>
                  <? endif;?>
                  </select>
                  <span class="description">&nbsp;Set your default target here.</span>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label for="contact_email">Contact Email:</label>
                </th>
                <td>
                  <input type='text' name='contact_email' id="contact_email" style="width:286px;" value='<?php echo ($user_data['data'][0]['contact_email'] ? $user_data['data'][0]['contact_email'] : $user_data['data'][0]['fb_email']); ?>'>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label for="user_notify">Email Notify: </label>
                </th>
                <td>
                  <?php echo form_checkbox('user_notify', 1, @$user_data['data'][0]['user_notify'], 'id="user_notify"'); ?>
                  <span class="description">&nbsp;Send email to me when post has been sent.</span>
                </td>
              </tr>
            </tbody>
          </table> 
          
          <p class="submit">
            <input type="submit" class="button-primary" name="submit"  value="Save Changes" />&nbsp;<?php echo(@$notify?'<b id="notify">'.$notify.'</b>':'');?>
          </p>
          <?php echo form_hidden('action', 'save_setting') . form_hidden('current_url', current_url()) . form_hidden('token', $result['token']); ?>
        </form>
      </div>
    <?php } else {### If wrong token or empty?>
    <?php $data = array('name'=> 'agree','id'=> 'agree','value'=> '1','checked'=>false);?>
    <div>
    <?php echo form_checkbox($data);?> I agree <a href="http://www.postsible.com/terms.php" target="_blank">terms and conditions</a>
    </div>
    <br/>
    <div id="deactive">
      <img src="<?php echo plugins_url('/fbconnectdsb.png',__FILE__);?>" boder="0"/>
    </div>
    <div id="active" style="display: none;">
      <?php echo $login_url; ?>
    </div>
    <span class="description">Please wait a few for fetching data.</span>
    <?php } ?>
  </div>
</div>