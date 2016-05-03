<?php
require_once('DBManager.php'); // Import the database manage object.
require_once('Util.php');
require_once('Logger.php');

class Pages extends CI_Controller {

    const name = "Stamkas - ";
    private $logged_in;
    private $user_data;
    
    public function index($page = 'home') {
        // Load our own settings.
        $this->config->load('TillManager');
        
        // Create all used instances
        $this->Logger = new Logger($this);
        $this->DBManager = new DBManager($this);
        $this->Util = new Util($this);
        
        // Load all libraries and helpers
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        
        // Check if the user is logged in.
        $this->logged_in = isset($this->session->username) && $this->session->username != NULL;
        
        if($this->logged_in) {
            $user = $this->session->username;
            $this->user_data = $this->DBManager->get_user_data($user);
        }
        
        // Log the user out if he's logged in and wants to log out.
        if($this->logged_in && $page == 'logout') {
            $this->logged_in = false;
            $this->session->unset_userdata('username');
            $this->session->set_flashdata('logout', true);
        }
        
        // If the user is not logged in, prepare the login form.
        if(!$this->logged_in) {
            $this->load->library('form_validation'); // Enable the form validation library.
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>'); // Set the form validation error delimiters.
            
            // Set the form validation rules for the login form.
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            // Check if the form validation was valid.
            if($this->form_validation->run() == TRUE) {
                // Get the post data of the form.
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                
                // Check if the credentials entered by the user are correct.
                $login = $this->DBManager->check_user_credentials($username, $password);
                
                // Check if the login was succesfull, if not show an error message.
                switch($login) {
                    case 'valid':
                        $this->session->set_userdata('username', $username);
                        $this->logged_in = true;
                        break;
                    case 'password':
                        $this->Logger->show_warning('Invalid password.');
                        break;
                    case 'username':
                        $this->Logger->show_warning('Username not found.');
                        break;
                } 
            }
        }
        
        if(!$this->logged_in) {
            if($page == 'logout') {
                $this->view('logout');
            } else {
                $this->view('login');
            }
        } else {
            switch($page) {
                default:
                    $this->view($page);
                    break;
            }
        }
        
        var_dump($this->session->all_userdata()); //#
    }
    
    public function view($page) {
        $data['name'] = self::name;
        $data['logged_in'] = $this->logged_in;
        $data['redirect'] = null;
        
        if($this->logged_in) {
            $data['user_data'] = $this->user_data;
        }
        
        // Show the page which should be loaded.
        switch($page) {
            case 'login':
                $data['navigation'] = false;
                $data['title'] = 'Login';
                $data['username'] = $this->input->post('username');
                
                // Check if the user is already logged in, then there is no point in showing the login form.
                if($this->logged_in) {
                    $data['redirect'] = 'home';
                    
                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/footer', $data);
                } else {
                    // Show the logout message if the user just logged out.
                    if($this->session->flashdata('logout')) {
                        $this->Logger->show_message('You succesfully logged out.', 'info');
                    }

                    $data['log'] = $this->Logger->get_html_log();

                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/login');
                    $this->load->view('templates/footer', $data);
                }
            break;
            case 'logout':
                $data['redirect'] = 'login';
                $data['title'] = 'test';
                $data['navigation'] = false;
                
                $data['log'] = $this->Logger->get_html_log();
                $this->load->view('templates/header', $data);
                $this->load->view('templates/footer', $data);
                break;
            default:
                if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php')) {
                    // Whoops, we don't have a page for that!
                    show_404();
                }

                // Set multiple variables used during page rendering.
                $data['title'] = $page;
                $data['navigation'] = true;
                $data['log'] = $this->Logger->get_html_log();
                
                // Show the page. 
                $this->load->view('templates/header', $data);
                $this->load->view('pages/'.$page, $data);
                $this->load->view('templates/footer', $data);
                break;
        }
    }
}