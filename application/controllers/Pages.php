<?php
require_once('DBManager.php'); // Import the database manage object.
require_once('Util.php');
require_once('Logger.php');

class Pages extends CI_Controller {

    const name = "Stamkas - ";
    private $logged_in;
    
    public function index($page = 'home') {
        // Create all used instances
        $this->Logger = new Logger($this);
        $this->DBManager = new DBManager($this);
        $this->Util = new Util($this);
        
        // Load all libraries and helpers
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        
        // Check if the user is logged in.
        $this->logged_in = isset($this->session->userID) && $this->session->userID != NULL;
        
        // Log the user out if he's logged in and wants to log out.
        if($this->logged_in && $page == 'logout') {
            $this->logged_in = false;
            $this->session->unset_userdata('userID');
            $this->session->set_flashdata('logout', true);
        }
        
        // If the user is not logged in, prepare the login form.
        if(!$this->logged_in) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            if($this->form_validation->run() == TRUE) {
                $this->session->set_userdata('userID', '123');
                $this->logged_in = true;
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
                // Check if the user tries to acces the login page while already logged in.
                case 'login':
                    $this->view('home');
                    break;
                default:
                    $this->view($page);
                    break;
            }
        }
        
        var_dump($this->session->all_userdata());
    }
    
    public function view($page) {
        $data['name'] = self::name;
        $data['logged_in'] = $this->logged_in;
        $data['redirect'] = null;
        
        $data['prepage'] = $this->session->flashdata('pre-page');
        $this->session->set_flashdata('pre-page', $page);
        
        switch($page) {
            case 'login':
                $data['navigation'] = false;
                $data['title'] = 'Login';
                
                // Show the logout message if the user just logged out.
                if($this->session->flashdata('logout')) {
                    $this->Logger->show_message('You succesfully logged out.', 'info');
                }
                
                $data['log'] = $this->Logger->get_html_log();
                
                $this->load->view('templates/header', $data);
                $this->load->view('templates/login');
                $this->load->view('templates/footer', $data);
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