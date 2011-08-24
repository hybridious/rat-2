<?php

class UsersController {
	
	function __construct() {
		
		global $app;
		
		// Check if user is logged in and trying to signup
		if ($app->uri['action'] == 'add' && !empty($_SESSION['user'])) {

			$app->page->name = 'Signup';
			$app->page->message = 'You are already logged in!';
			$app->loadView('partials/header');
			$app->loadView('partials/footer');
			exit;

		}
		
	}
	
	// Show a list of users
	function index() {
		
		global $app;
		
		// Not needed?
		
	}
	
	// Add a user
	function add($code) {
		
		global $app;
		
		if ($_POST['email'] != '') {
			
			if ($_POST['code'] != '') {
				
				$this->do_signup('code');
				
			} else {
				
				if ($app->config->beta == TRUE) {
					
					$this->do_signup('beta');
					
				} else {
					
					$this->do_signup('full');
					
				}
				
			}
			
		} else {
			
			// Show signup form
			
			if ($app->config->beta == TRUE) {
				// Show beta signup form
				$app->loadLayout('users/add_beta');
			} else {
				// Show full signup form
				
				if (isset($code)) {
					$app->page->code = $code;
				}
				
				$app->loadLayout('users/add');
			}
			
		}
		
	}
	
	// Show a user
	function show($id) {
		
		global $app;
		
		$app->page->user = User::get($id);
		$app->page->items = $app->item->list_user($id);
		
		$app->page->name = $app->page->user['username'];
		$app->loadLayout('users/show');
		
	}
	
	function update($id) {
		
		global $app;
		
		$app->page->user = User::get($id);
		
		$app->page->name = 'Settings';
		$app->loadLayout('users/update');
		
	}
	
	function reset($code) {
		
		global $app;
		
		if (!empty($code)) {
			// Process reset
			
			// If two passwords submitted then check, otherwise show form
			if (isset($_POST['password1']) && isset($_POST['password2'])) {
				
				if (User::check_password_reset_code($code) == FALSE)
					exit();
				
				if ($_POST['password1'] == '' || $_POST['password2'] == '')
					$error .= 'Please enter your password twice.<br />';
				
				if ($_POST['password1'] != $_POST['password2'])
					$error .= 'Passwords do not match.<br />';
				
				// Error processing
				if ($error == '') {
					
					$user_id = User::check_password_reset_code($code);
					
					// Do update
					User::update_password($user_id, $_POST['password1']);
					
					$user = User::get($user_id);
					
					// Start session
					$_SESSION['user'] = $user;
					
					// Log login
					if (isset($app->plugins->log))
						$app->plugins->log->add($_SESSION['user']['id'], 'user', NULL, 'login');
					
					// If redirect_to is set then redirect
					if ($_GET['redirect_to']) {
						header('Location: '.$_GET['redirect_to']);
						exit();
					}
					
					// Set welcome message
					$app->page->message = urlencode('Password updated.<br />Welcome back to '.$app->config->name.'!');
					
					// Go forth!
					if (SITE_IDENTIFIER == 'live') {
						header('Location: '.$app->config->url.'?message='.$app->page->message);
					} else {
						header('Location: '.$app->config->dev_url.'?message='.$app->page->message);
					}
					
					exit();
					
				} else {
					// Show error message
					
					$app->page->message = $error;
					$app->loadView('partials/header');
					if (User::check_password_reset_code($code) != FALSE)
						$app->loadView('reset');
					$app->loadView('partials/footer');
					
				}
				
			} else {
				// Code present so show password reset form
				
				$app->loadLayout('users/reset');
				
			}
			
		} else {
			// No code in URL so show new reset form
			
			$app->loadLayout('users/reset_new');
			
		}
		
	}
	
	function confirm($email) {
		
		global $app;
		
		
		
	}
	
	function json($username) {
		
		global $app;
		
		$user['user'] = User::get_by_username($username);
		$user['items'] = $app->item->list_user($user['user']['id']);
		$app->page->json = $user;
		$app->loadView('pages/json');
		
	}
	
}

?>