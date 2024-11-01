function init_staff_grid_circle()
{
	jQuery('.staff_grid_circle').each(function() {
		//on définit la même largeur que hauteur
		var width = jQuery(this).width();
		jQuery(this).height(width);
		//taille des cercles
		var nb_circles = jQuery(this).find('.member').length;
		var width_circles = width/4;
		jQuery(this).find('.member').css({width: width_circles, height: width_circles});
		//place les cercles suivant leur angle
		var real_width = (width-width_circles)/2;
		var angle = 2*Math.PI/nb_circles;
		jQuery(this).find('.member').css({ left: 0, top: 0, opacity: 0 });
		jQuery(this).find('.member').each(function( i, val ) {
			var current_angle = angle*i-Math.PI/2;
			var x = Math.cos(current_angle);
			var y = Math.sin(current_angle);
			jQuery(this).animate({ left: '+='+(real_width+x*real_width), top: '+='+(real_width+y*real_width), opacity: "+=1" }, 500);
		});
		//définit la hauteur de ligne du contenu centrale
		jQuery(this).parent().find('.current_member').css({ 'line-height': width+'px' });
		jQuery(this).parent().find('.current_member .inner').css({ width: (width/3), height: (width/3) });
	});
}

jQuery(document).ready(function(){

	var resize_timeout;

	init_staff_grid_circle();

	jQuery('.staff_grid_circle .member').click(function(){
		var member = jQuery(this).html();
		jQuery(this).parent().find('.current_member .inner').html(member);
		jQuery(this).parent().find('.current_member').show();
	});

	jQuery(window).resize(function(){

		clearTimeout(resize_timeout);
    	resize_timeout = setTimeout(init_staff_grid_circle, 500);

	});
	
});