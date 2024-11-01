<script>

		jQuery(document).ready(function(){

			jQuery('#staff_grids .remove').click(function(){
				var pl = jQuery(this).parent('form').parent('.staff_grid');
				jQuery.post(ajaxurl, {action: 'remove_staff_grid', id: jQuery(this).attr('rel'), _ajax_nonce: '<?= wp_create_nonce( "remove_staff_grid" ); ?>' }, function(){
					jQuery(pl).remove();
				});
			});

			jQuery('input[name=bg_color], input[name=text_color], input[name=desc_text_color]').wpColorPicker();

		});

</script>

<h2>All staff grids</h2>
<form action="" method="post" id="form_new_staff_grid">
<?php wp_nonce_field( 'new_staff_grid' ) ?>
<b>Add a new staff grid</b><br />
	<label>Name : </label><input type="text" name="name" /><br />
	<label>Members name size : </label><input type="text" name="text_size" />px<br />
	<label>Show grid name : </label><input type="checkbox" name="show_name" /><br />
	<input type="submit" value="Add" />
</form>

<div id="staff_grids">
<?php

if(sizeof($grids) > 0)
{
	foreach($grids as $grid)
	{
		echo '<div class="staff_grid"><h3>'.$grid->name.'</h3>';
		echo '<form action="" method="post">';
		echo wp_nonce_field( 'update_staff_grid_'.$grid->id, "_wpnonce", true, false );
		echo '<input type="hidden" name="id" value="'.$grid->id.'" />';
		echo '<label>Name: </label><input type="text" name="name" value="'.$grid->name.'" /><br />';
		echo '<label>Members name size: </label><input type="text" name="text_size" value="'.$grid->text_size.'" />px<br />
		<label>Show name: </label><input type="checkbox" name="show_name" '.($grid->show_name == 1 ? 'checked="checked"' : '').' /><br />';
	echo '<a href="'.admin_url('admin.php?page=staff_grids&id='.$grid->id).'" title="Manage members"><img src="'.plugins_url( 'images/members.png', dirname(__FILE__)).'" /></a>
	 <input type="image" src="'.plugins_url( 'images/save.png', dirname(__FILE__)).'" title"Save" />	  
	 <img title="Remove this grid" class="remove action" rel="'.$grid->id.'" src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" />
	Shortcode : <input type="text" value="[staff-grid id='.$grid->id.']" readonly />
	</form></div>';
	}
}
else
	echo 'No grid found !';

?>
</div>

<div>
	<h3>Need more options ? Look at <a href="http://www.info-d-74.com/produit/staff-grids/" target="_blank">Staff Grids Pro!</a> <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo plugins_url( 'images/fb.png', dirname(__FILE__)) ?>" alt="" /></a></h3>
	<a href="http://www.info-d-74.com/produit/staff-grids/" target="_blank">
		<img src="<?php echo plugins_url( 'images/pro.png', dirname(__FILE__)) ?>" alt="" />
	</a>