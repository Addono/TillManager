<?php
require_once('DBManager.php'); // Import the database manage object.
require_once('Util.php');
require_once('Logger.php');

class Pages extends CI_Controller {

    const name = "Stamkas - ";
    
    public function view($page = 'home') {
        $this->Logger = new Logger($this);
        $this->DBManager = new DBManager($this);

        $this->load->library('session');

        var_dump($this->session);

        // Check if a user is logged in.
        if(!isset($this->session->userID) || $this->session->userID == NULL) {
            $this->load->view('templates/login');
            $this->Logger->show_html_log();
        } else {
            if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php')) {
                // Whoops, we don't have a page for that!
                show_404();
            }

            // Set multiple variables used during page rendering.
            $data['title'] = $page; // Capitalize the first letter
            $data['name'] = self::name;
            $data['resources'] = base_url() . "application/resources";
            $data['log'] = $this->variable;

            // Show the page. 
            $this->load->view('templates/header', $data);
            $this->load->view('pages/'.$page, $data);
            $this->load->view('templates/footer', $data);
        }
    }
}

