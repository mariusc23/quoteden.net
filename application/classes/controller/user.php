<?php
class Controller_User extends Controller_Template {
    public $template = 'base/template';
    public $auth_role     = array('login');
    public $secure_actions     = array();
    public $referer = '';

    function action_register() {
        $view = $this->template->content = View::factory('user/register');
        // if user already logged in
        if (Auth::instance()->logged_in() != 0){
            Request::instance()->redirect('user/login');
        }

 
        // if posted data
        if ($_POST) {
            Request::instance()->redirect('user/login');
            return;
            $user = ORM::factory('user');
 
            // validate data
            $post = $user->validate_create($_POST);
 
            if ($post->check()) {
                // feed the data
                $user->values($post);
 
                // and save it
                $user->save();
 
                // add the login role
                $login_role = new Model_Role(array('name' => 'login'));
                $user->add('roles', $login_role);
 
                // sign the user in
                Auth::instance()->login($post['username'], $post['password']);
 
                // show their account
                Request::instance()->redirect($this->referer);
            } else {
                // show the registration errors
                $view->errors = $post->errors('register');
            }
        }
    }

    public function action_login() {
        $view = $this->template->content = View::factory('user/login');
        // if user already logged in
        if (Auth::instance()->logged_in() != 0){
            if ($_POST) Request::instance()->redirect($this->referer);
            $view->user = $this->template->user;
        }


        // if posted data
        if ($_POST) {
            $user = ORM::factory('user');

            // check auth
            if ($user->login($_POST)) {
                Request::instance()->redirect($this->referer);
            } else {
                $view->errors = $_POST->errors('login');
                $this->template->title = 'Error logging in';
                return ;
            }
        }

        $this->template->title = 'Log in';
    }

    public function action_logout() {
        // log out
        Auth::instance()->logout();

        Request::instance()->redirect($this->referer);
    }

    public function before() {
        parent::before();
        $this->template->user = Auth::instance()->get_user();
        $this->template->model = 'user';
        $this->template->action = Request::instance()->action;
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
   }

}