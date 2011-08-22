<?php

if (!isset($item))
	$item = $app->page->item;

if (count($item['comments']) > 0) {

	echo '<span id="comments_'.$item['id'].'">';

	foreach ($item['comments'] as $comment) {
		
		echo '
		<p class="meta">
		"'.$comment['content'].'" - '.$this->link_to($comment['user']['username'], 'users', 'show', $comment['user']['id']);
		if ($comment['user']['id'] == $_SESSION['user']['id']) {
			$url = $this->link_to(NULL, 'comments', 'remove', $comment['id']);
			echo ' &middot; <span style="font-size: 50%;"><a href="#" onclick="comment_remove('.$item['id'].', \''.$url.'\'); return false;">Delete</a></span>';
		}
		echo '
		</p>';
	
	}
	
	echo '</span>';

} else {
	// no comments yet but show empty div fo das ajax

	echo '<span id="comments_'.$item['id'].'"></span>';

}

if ($app->page->show_comment_form == TRUE) {

	if (!isset($item))
		$item = $app->page->item;
	
	if ($app->config->items['comments']['enabled'] == TRUE && ($app->config->private == TRUE || $_SESSION['user'] != NULL)) {
	
		$url = $this->link_to(NULL, 'comments', 'add');
	
		?>
	
		<form action="javascript:comment_add(<?php echo $item['id']; ?>, '<?php echo $url; ?>');" id="comment_form_<?php echo $item['id']; ?>" class="meta" style="margin: 0px; <?php if ($app->page->show_comment_form != TRUE) { echo 'visibility: hidden; height: 0px;'; }?>" method="post">
			<input type="text" name="content" size="30" value="" /> <input type="submit" value="<?php echo $app->config->items['comments']['name']; ?>" />
		</form>
	
	<?php
	
	}

}

?>