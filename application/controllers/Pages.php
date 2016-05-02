<?php
require_once('DBManager.php'); // Import the database manage object.
require_once('Util.php');
require_once('Logger.php');

class Pages extends CI_Controller {

    const name = "Stamkas - ";
    private $logged_in;
    
    public function index($page = 'home') {
        echo $page;
        $this->Logger = new Logger($this);
        $this->DBManager = new DBManager($this);
        $this->Util = new Util($this);
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        
        $this->logged_in = isset($this->session->userID) && $this->session->userID != NULL;
        
        if($this->logged_in && $page == 'logout') {
            $this->logged_in = false;
            $this->session->sess_destroy();
        }
        
        if(!$this->logged_in) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            if($this->form_validation->run() == TRUE) {
                $this->session->set_userdata('userID', '123');
                $this->logged_in = true;
                echo "Test";
            } else {
                echo "Test 2";
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
        
        echo $page;
        
        switch($page) {
            case 'login':
                $data['navigation'] = false;
                $data['title'] = 'Login';

                $this->load->view('templates/header', $data);
                $this->load->view('templates/login');
                $this->Logger->show_html_log();
                $this->load->view('templates/footer', $data);
            break;
            case 'logout':
                $data['redirect'] = 'login';
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

                // Show the page. 
                $this->load->view('templates/header', $data);
                $this->load->view('pages/'.$page, $data);
                $this->Logger->show_html_log();
                $this->load->view('templates/footer', $data);
                break;
        }
    }
}