<script>
	jQuery(document).ready(function($){

		//changement d'ordre des images
		jQuery('#members').sortable({
			update: function( event, ui ) {
				//effectuer le changement de position en BDD par Ajax
				jQuery.post(ajaxurl, {action: 'staff_grid_member_order', id: jQuery(ui.item).find('img.thumbnail').attr('rel'), order: (ui.item.index()+1), _ajax_nonce: '<?= wp_create_nonce( "staff_grid_member_order" ); ?>' });
			}
		});
	    
		//choix d'une image
	    jQuery('.upload-btn').click(function(e) {
	    	var _this = this;
	        e.preventDefault();
	        var image = wp.media({ 
	            title: 'Upload Image',
	            // mutiple: true if you want to upload multiple files at once
	            multiple: false
	        }).open()
	        .on('select', function(e){
	            // This will return the selected image from the Media Uploader, the result is an object
	            var uploaded_image = image.state().get('selection').first();
	            // We convert uploaded_image to a JSON object to make accessing it easier
	            // Output to the console uploaded_image
	            var image_url = uploaded_image.toJSON().url;
	            // Let's assign the url value to the input field
	            jQuery(_this).parent().find('input[name="photo"]').val(image_url);
	        });
	    });

	    //ajout du membre
	    jQuery('#form_new_member').submit(function(){
	    	if(jQuery('#form_new_member input[name="photo"]').val() == '')
	    		alert('Please choose a photo!');
	    	else if(jQuery('#form_new_member input[name=name]').val() == '')
	    		alert('Name can\'t be empty !');
	    	else
	    	{
	    		//on ajoute l'image en ajax
	    		jQuery.post(ajaxurl, jQuery(this).serialize(), function(id_img){
	    			window.location.reload();
		    	});
			}
	    	return false;
	    });

		//click suppression
	    jQuery('#members img.remove').click(function(){
	    	var _this = this;
	    	jQuery.post(ajaxurl, {action: 'staff_grid_remove_member', id: jQuery(_this).attr('rel'), _ajax_nonce: '<?= wp_create_nonce( "staff_grid_remove_member" ); ?>'}, function(){
	    		jQuery(_this).parent('li').remove();
	    	});
	    });

	    //click changement image
	    jQuery('#members li img.thumbnail').click(function(){
	    	var li = jQuery(this).parent('li');
	    	if(jQuery(li).hasClass('opened'))
	    		jQuery(li).removeClass('opened');
	    	else
	    	{
	    		var li_opened = jQuery('#members li.opened');
	    		if(li_opened)
	    		{
	    			jQuery(li_opened).find('.form_edit_member').toggle('fast');
	    			jQuery(li_opened).removeClass('opened');
	    		}
	    		jQuery(li).addClass('opened');
	    	}
	    	jQuery(this).parent('li').find('.form_edit_member').toggle('fast');
	    });

	    //click sauvegarde
	    jQuery('.form_edit_member').submit(function(){
	    	var _this = this;
	    	if(jQuery(_this).find('input[name="photo"]').val() == '')
	    		alert('Please choose an image !');
	    	else if(jQuery(_this).find('input[name="name"]').val() == '')
	    		alert('Name can\'t be empty !');
	    	else
	    	{
		    	jQuery(_this).find('img.loading').show();
		    	jQuery.post(ajaxurl, jQuery(this).serialize(), function(){
		    		//récupère la nouvelle photo
		    		var new_image = jQuery(_this).find('input[name="photo"]').val();
		    		jQuery(_this).parent('li').find('img.thumbnail').attr('src', new_image);
		    		jQuery(_this).find('img.loading').hide();
		    	});
		    }
	    	return false;
	    });
	});
</script>

<h2><?php echo $grid->name ?></h2>
<form action="" method="post" id="form_new_member">
	<?php wp_nonce_field( 'staff_grid_add_member' ) ?>
	<input type="hidden" name="id_grid" value="<?php echo $grid->id ?>" />
	<input type="hidden" name="action" value="staff_grid_add_member" />
	<b>Add a new member</b><br />
	<label>Name: </label><input type="text" name="name" /><br />
	<label>Photo: </label><input type="text" name="photo" /><input type="button" name="upload-btn" class="upload-btn button-secondary" value="Choose a photo"><br />
	<label>Job: </label><input type="text" name="job" /><br />
	<label>Mail: </label><input type="text" name="mail" /><br />
	<label>Tel: </label><input type="text" name="tel" /><br />
	<label>Link: </label><input type="text" name="link" /><input type="checkbox" name="blank" value="1" /> Open in a new window ?<br />
	<input type="submit" value="Add the member" />
</form>

<ul id="members">
<?php

	if(sizeof($members) > 0)
	{
		foreach($members as $member)
		{
			echo '<li><img class="thumbnail" rel="'.$member->id.'" src="'.$member->photo.'" /><img class="remove" rel="'.$member->id.'" src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" />
			<form class="form_edit_member">'.
				wp_nonce_field( 'staff_grid_save_member', "_wpnonce", true, false ).'
				<input type="hidden" name="id" value="'.$member->id.'" />
				<input type="hidden" name="action" value="staff_grid_save_member" />
				<label>Name: </label><input type="text" name="name" value="'.$member->name.'" /><br />
				<label>Photo: </label><input type="text" name="photo" value="'.$member->photo.'"><input type="button" name="upload-btn" class="upload-btn button-secondary" value="Change Image"><br />
				<label>Job: </label><input type="text" name="job" value="'.$member->job.'" /><br />
				<label>Mail: </label><input type="text" name="mail" value="'.$member->mail.'" /><br />
				<label>Tel: </label><input type="text" name="tel" value="'.$member->tel.'" /><br />
				<label>Link: </label><input type="text" name="link" value="'.$member->link.'" /><br />
				<input type="checkbox" name="blank" value="1" '.($member->blank == 1 ? 'checked="checked"' : '').' /> Open in a new window?<br />
				<input type="submit" value="Save" /><img src="'.plugins_url( 'images/loading.gif', dirname(__FILE__) ).'" class="loading" />
			</form>
			</li>';
		}
	}
	else
		echo 'No member found for this grid!';

?>
</div>