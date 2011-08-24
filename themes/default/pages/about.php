<?php

foreach ($this->config->admin_users as $value) {
	$author = User::get($value);
	if ($this->config->private != TRUE || $_SESSION['user'] != NULL) {
		$authors .= '<a href="/'.$author['username'].'">'.$author['username'].'</a>, ';
	} else {
		$authors .= $author['username'].', ';
	}

}
$authors = substr($authors, 0, -2);

$content = '<p>'.$this->config->name.' is a web app created by '.$authors.' based on the <a href="http://github.com/DHS/rat">rat</a> framework. ';

if ($this->config->beta == TRUE)
	$content .= 'It is currently in beta.';

$content .= "</p>\n";

$content .= '<p>It lets you create '.$this->config->items['name_plural'];

if ($this->config->items['titles']['enabled'] == TRUE)
	$content .= ' with '.strtolower($this->config->items['titles']['name_plural']);

if ($this->config->items['comments']['enabled'] == TRUE || $this->config->items['likes']['enabled'] == TRUE) {

	$content .= ' and then ';

	if ($this->config->items['comments']['enabled'] == TRUE)
		$content .= ' add '.strtolower($this->config->items['comments']['name_plural']).' ';

	if ($this->config->items['comments']['enabled'] == TRUE && $this->config->items['likes']['enabled'] == TRUE)
		$content .= ' and ';

	if ($this->config->items['likes']['enabled'] == TRUE)
		$content .= ' \''.strtolower($this->config->items['likes']['name']).'\' ';

	$content .= 'them';

}

$content .= ". </p>\n";

if ($this->config->invites['enabled'] == TRUE)
	$content .= "<p>It also has an invite system so that you can invite your friends.</p>\n";

if (isset($app->plugins->points)) {

	$content .= '<p>It also has a points system';

	if ($app->plugins->points['leaderboard'] == TRUE)
		$content .= ' and a leaderboard so you can see how you\'re doing relative to everyone else';

	$content .= ".</p>\n";

}

if (isset($app->plugins->gravatar))
	$content .= '<p>'.$this->config->name.' is <a href="http://gravatar.com/">Gravatar</a>-enabled.</p>'."\n";

echo $content;

?>