<div class="wrap">
<h2 id="history_header">History 
  <?php if(isset($total_row) and $total_row > 0):?>
    <a class="add-new-h2" href="http://apps.facebook.com/postsible/admin/setting.php" target="_blank">Manage your post</a>
  <?php endif;?>
</h2>
<form style="width:98%">
<table border="0" class="wp-list-table widefat fixed posts">
  <thead>
  <tr>
    <th scope="col" class="manage-column column-title">Title</th>
    <th scope="col" class="manage-column column-title">Author</th>
    <th scope="col" class="manage-column column-title" style="width:60px;">Target</th>
    <th scope="col" align="left" class="manage-column column-title" style="width:160px;">Date Time</th>
    <th scope="col" class="manage-column column-title" style="width:60px;">Sent</th>
  </tr>
  </thead>
  <?php if($query){?>
  <tbody id="the-list">
    <?php $x= 2;?>
    <?php foreach($query as $data){?>
      <?php $post = get_post_psb($data['psb_id']);?>
      <tr class="<?php echo ($x%2==0?'alternate':'');?>">
        <td><a href="<?php echo $post['guid'];?>" target="_blank"><?php echo $data['psb_title'];?></a></td>
        <td><?php echo get_author_psb($post['post_author']);?></td>
        <td><a href="http://www.facebook.com/<?php echo $data['psb_page'];?>" target="_blank"><img src="https://graph.facebook.com/<?php echo $data['psb_page'];?>/picture?type=square" width="25" height="25" align="absmiddle"/></a></td>
        <td><?php echo get_the_time('Y/m/d', $data['psb_id']); ?><br/><?php echo get_the_time('', $data['psb_id']); ?></td>
        <td style="padding-left:13px;"><img src="<?php echo ($data['psb_success']?plugins_url('postsible/tick_circle.png'):plugins_url('postsible/cross_circle.png'));?>"/></td>
      </tr>
      <?php $x++;?>
    <?php }?>
  </tbody>
  <tfoot>
  <tr>
    <th scope="col" class="manage-column column-title">Title</th>
    <th scope="col" class="manage-column column-title">Author</th>
    <th scope="col" class="manage-column column-title" style="width:60px;">Target</th>
    <th scope="col" align="left" class="manage-column column-title" style="width:160px;">Date Time</th>
    <th scope="col" class="manage-column column-title" style="width:60px;">Sent</th>
  </tr>
  </tfoot>
  <?php } ?>
</table>
  <div class="tablenav">
    <div class="tablenav-pages">
      <span class="displaying-num"><?php echo ($total_row?$total_row:0);?> items</span>
      <span class="pagination-links"><?php echo paginate_links( $args );?></span>
    </div>
  </div>
</form>
</div>