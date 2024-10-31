<?php
  $result = get_fb_user();
  $user_data = api('user', 'get', @$result);
?>
<div class="wrap">
  <h2 id="header">Contact</h2>
  <div id="pi_body">
    <div><form action="" method="post" id="setting">
          <table class="form-table">
            <tbody>
              <tr valign="bottom">
                <th scope="row">
                  <label for="subject">Subject:</label>
                </th>
                <td>
                  <? $options = array(
                                                'general' => 'General Question'
                                                ,'problem' => 'Problem Question'
                                                ,'suggest_feature' => 'Suggest a feature'
                                                );?>
                  <?php echo form_dropdown('subject', $options, (@$_POST['subject'] != ''?$_POST['subject']:'general'), 'id="subject"');?>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label for="from">From:</label>
                </th>
                <td>
                  <input type='text' name='from' id="from" style="width:286px;" value='<?php echo ($user_data['data'][0]['contact_email'] ? $user_data['data'][0]['contact_email'] : $user_data['data'][0]['fb_email']); ?>'>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row">
                  <label for="description">Description:</label>
                </th>
                <td>
                  <textarea id="description" class="required" name="description" style="width:400px; height:100px;"></textarea>
                </td>
              </tr>
            </tbody>
          </table>
          <?php echo form_hidden('token', @$result['token']);?>
          <p class="submit">
            <input type="submit" class="button-primary" name="submit" id="contact" value="Send" />&nbsp;<?php echo(@$notify?'<b id="notify">'.$notify.'</b>':'');?>
          </p>
          <?php echo form_hidden('action', 'contact'); ?>
        </form>
      </div>
  </div>
</div>