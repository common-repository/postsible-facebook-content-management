 <?php if ($profile) { 
    ### If can get user data
    $psb_data = get_psb_data($post->ID);
    ### Get target box
    $page_box = get_target_box($result);
  ?>
  <div class="psbdiv">
  <label for="pages_id" class="psb fleft">Select Pages:</label>
  <select name='pages_id' id='page_text' class='required fleft dropimg' dir='select1'>      
  <option value='false'>No Posting</option>
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
  </select>
  </div> 

<div class="psbdiv">
     <label for="psb_text" class="psb fleft">Message:</label>
     <div class="fleft">
       <input type="text" id="psb_text" name="psb_text" class="fleft" value="<?php echo stripslashes(@$psb_data['psb_text']);?>" size="70" /><br/>
        <span class="description"> Use %url% Variable to define url of post</span>
     </div>
    
</div>
    
  <div class="psbdiv">
    <label for="psb_delay" class="psb fleft">Delay Post:</label>
      <?php echo form_dropdown('psb_delay', delay_data(), @$psb_data['psb_delay'], ' id="psb_delay" class="dropimg" style="width:250px;"');?>
  </div>
  
<fieldset id="content_box_set" class="fleft">
  <legend>Content</legend>
  <div align="right"><a href="<?php echo plugins_url('/wp.png', __FILE__);?>" target="_blank">Preview Zone</a> | <a href="http://apps.facebook.com/postsible/admin/" target="_blank">Main Site</a></div>
    <div class="psbdiv">
      <label for="psb_share_thumb" class="psb fleft">Thumbnails (1):</label>
        <input type="text" id="psb_share_thumb" name="psb_share_thumb" class="fleft" value="<?php echo (@$psb_data['psb_share_thumb']?stripslashes(@$psb_data['psb_share_thumb']):'%auto%');?>" size="70" /></div>
        
    <div class="psbdiv">
      <label for="psb_title" class="psb fleft">Title (2):</label>
    <input type="text" id="psb_title" name="psb_title" class="fleft" value="<?php echo (@$psb_data['psb_title']?stripslashes(@$psb_data['psb_title']):'%title%');?>" size="70" /></div>

    <div class="psbdiv"><label for="psb_media_des" class="psb fleft">Description (3):</label>
    <textarea name="psb_media_des" id="psb_media_des" cols="48" rows="5"><?php echo (@$psb_data['psb_media_des']?stripslashes(@$psb_data['psb_media_des']):'%description%');?></textarea></div>
  
</fieldset>
    <div class="clear"></div>
  <?php } else {### If Wrong user data?>
    <div id='wrong_data'><p>Please setting <a href='admin.php?page=postsible-main'>Postsible Plugin</a> at first time.</p></div>
  <?php } ?>