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
  <h2 id="header">Page</h2>
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
      $encode_id = true;
      $page_box = get_target_box($result,$encode_id);
      ?>

      <div><form action="http://apps.facebook.com/postsible/admin/buy.php" method="get" target="_blank" id="setting">
          <table class="form-table">
            <tbody>
              <tr valign="top">
                <th scope="row">
                  <label for="page_text">Buy Target:</label>
                </th>
                <td>
                  <select name='id' id='page_text' class='required fleft dropimg' dir='select1'>
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
                  </select>
                  &nbsp;
                  <input type="submit" class="button-primary" name="submit"  value="Buy Select Target" />
                  <input type="hidden" name="wpb" value="true"/>
                </td>
              </tr>
            </tbody>
          </table> 
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