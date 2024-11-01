<div class="staff_grid_grid">
<?php

	if($grid->show_name == 1)
		echo '<h2>'.$grid->name.'</h2>';

	foreach($members as $member)
	{
		echo '<div class="member" style="background-color: '.$grid->bg_color.'; color: '.$grid->desc_text_color.'">';
		echo '<img src="'.$member->photo.'" class="photo" />';
		echo '<div class="infos">';
		echo '<h3 style="color: '.$grid->text_color.'; font-size: '.$grid->text_size.'px; line-height: '.$grid->text_size.'px;">'.$member->name.'</h3>';
		echo '<strong>'.$member->job.'</strong>';
		echo '<p>';
		if(!empty($member->mail))
			echo 'Mail: <a href="mailto:'.$member->mail.'">'.$member->mail.'</a><br />';
		if(!empty($member->tel))
			echo 'Tel: <a href="tel:'.$member->tel.'">'.$member->tel.'</a><br />';
		if(!empty($member->link))
			echo '<a href="'.$member->link.'" '.($member->blank == 1 ? 'target="_blank"' : '').'>View profile</a>';
		echo '</p>';
		echo '</div>';
		if(!empty($member->description))
		{
			echo '<div class="description">'.nl2br($member->description).'</div>';
			echo '<div class="expand"></div>';
		}
		echo '</div>';
	}

?>
</div>