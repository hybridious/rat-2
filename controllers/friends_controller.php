<?php

class FriendsController extends Application {

  protected $requireLoggedIn = array('add', 'remove');

  function add($friend_id) {

    $user = User::get_by_id($_SESSION['user_id']);
    $friend = User::get_by_id($friend_id);

    $user->friend_add($friend_id);

    if (isset($this->plugins->log)) {
      $this->plugins->log->add($user->id, 'friend', $friend_id, 'add');
    }

    if ($this->config->send_emails == TRUE) {
      // Send 'new follower' email to writer

      $admin = User::get_by_id($this->config->admin_users[0]);

      $to      = array('email' => $email, 'friend' => $friend);
      $link    = $this->config->url . 'users/show/' . $user->id;
      $subject  = '[' . $this->config->name . '] Your ' . $this->config->name . ' invite is here!';
      $body    = $this->twig_string->render(file_get_contents("themes/{$this->config->theme}/emails/follower_new.html"), array('link' => $link, 'app' => $this));

      // Email user
      $this->email->send_email($to, $subject, $body);

    }

    $session['user_id'] = $_SESSION['user_id'];
    $friends = TRUE;

    $this->user = $friend;

    // Copying the work of loadView
    $params = array(
      'app'    => $this,
      'session'  => $_SESSION
    );

    $params['session']  = $session;
    $params['user']    = $friend;
    $params['friends']  = $friends;

    echo $this->twig->render("partials/friend.html", $params);

  }

  function remove($friend_id) {

    $user = User::get_by_id($_SESSION['user_id']);
    $friend = User::get_by_id($friend_id);

    // Check that frienship is legit
    if ($friend->friend_check($_SESSION['user_id']) == TRUE) {

      // Remove friendship
      $user->friend_remove($friend_id);

      // Log parting of ways
      if (isset($this->plugins->log)) {
        $this->plugins->log->add($user->id, 'friend', $friend_id, 'remove');
      }

    }

    $session['user_id'] = $_SESSION['user_id'];
    $friends = FALSE;

    $this->user = $friend;

    // Copying the work of loadView
    $params = array(
      'app'    => $this,
      'session'  => $_SESSION
    );

    $params['session']  = $session;
    $params['user']    = $friend;
    $params['friends']  = $friends;

    echo $this->twig->render("partials/friend.html", $params);

  }

}
