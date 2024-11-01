jQuery(document).ready(function(){
	
	jQuery('.staff_grid_list ul li').click(function(){

		jQuery('.staff_grid_list .current_member').html(jQuery(this).html());
		return false;

	});

})