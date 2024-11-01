jQuery(document).ready(function(){
	
	jQuery('.staff_grid_grid .expand').click(function(){

		if(!jQuery(this).hasClass('opened'))
		{
			jQuery(this).parent().find('.description').slideDown();
			jQuery(this).addClass('opened');
		}
		else
		{
			jQuery(this).parent().find('.description').slideUp();
			jQuery(this).removeClass('opened');
		}
		return false;

	});

})