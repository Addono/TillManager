<?php
/**
 * @TODO: Add multilanguage support with gettext
 * 
 * @author      Adriaan Knapen <a.d.knapen@student.tue.nl> <adriaan.knapen@gmail.com>
 * @copyright   Copyright (c) 2016, Adriaan Knapen
 * @licence     http://opensource.org/licenses/MIT	MIT License
 */

require_once('DBManager.php'); // Import the database manage object.
require_once('Util.php');
require_once('Logger.php');

class Pages extends CI_Controller {

    const name = "Stamkas - ";
    private $logged_in;
    private $user_data;

    // Stores information about names, locations, and access rights.
    public $pages = [
        "home" => [
            "location" => "home",
            "title" => "Home",
            "admin" => false,
            "tillmanager" => false
        ],
        "purchase" => [
            "location" => "purchase",
            "title" => "Purchase",
            "admin" => false,
            "tillmanager" => false
        ],
        "deposit" => [
            "location" => "deposit",
            "title" => "Deposit",
            "admin" => false,
            "tillmanager" => true
        ],
        "balance" => [
            "location" => "balance",
            "title" => "Balance",
            "admin" => false,
            "tillmanager" => false
        ],
        "manage_users" => [
            "location" => "manage_users",
            "title" => "Manage users",
            "admin" => true,
            "tillmanager" => false
        ],
        "account" => [
            "location" => "account",
            "title" => "Account details",
            "admin" => false,
            "tillmanager" => false
        ],
        // Hidden pages
        "process_purchase" => [
            "location" => "process_purchase",
            "title" => "Process purchase",
            "admin" => false,
            "tillmanager" => false,
            "hidden" => true
        ],
        "process_deposit" => [
            "location" => "process_deposit",
            "title" => "Process deposit",
            "admin" => false,
            "tillmanager" => true,
            "hidden" => true
        ]
    ];

    public function index($page = 'home') {
        // Load our own settings.
        $this->config->load('TillManager');

        // Create all used instances
        $this->Logger = new Logger;
        $this->DBManager = new DBManager($this);
        $this->Util = new Util($this);

        // Load all libraries and helpers
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');

        // Check if the user is logged in.
        $this->current_user_logged_in();

        $user = $this->get_data_current_user();
        
        // If there is a user logged in set the current user of DBManager.
        if($user !== null) {
            $this->DBManager->set_current_user($user["id"]);
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

                // Check if the credentials entered by the user are correct, 
                // if they are not then show an error message.
                switch($this->DBManager->check_user_credentials($username, $password, 'login')) {
                    case 'valid':
                        $this->session->set_userdata('username', $username);
                        $this->logged_in = true;
                        break;
                    case 'password':
                        $this->Logger->add_warning('Invalid password.');
                        break;
                    case 'username':
                        $this->Logger->add_warning('Username not found, note that the username is case sensitive.');
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
    }

    private function current_user_logged_in() {
      return $this->logged_in = isset($this->session->username) && $this->session->username !== null;
    }

    private function get_data_current_user() {
      if($this->logged_in) {
          $username = $this->session->username;
          $user_id = $this->DBManager->username_to_id($username);
          return $this->user_data = $this->DBManager->get_user_data($user_id);
      }
    }

    public function view($page) {
        // Set the default value of the data variable.
        $data['name'] = self::name;
        $data['logged_in'] = $this->logged_in;
        $data['redirect'] = null;
        $data['user_data'] = $this->user_data;
        $data['navigation_pages'] = $this->pages;

        // Show the page which should be loaded.
        switch($page) {
            case 'login':
                $data['navigation'] = false;
                $data['title'] = 'Login';

                // If the user is already logged in, show the home page instead.
                if($this->logged_in) {
                    $data['redirect'] = [
                        "target" => "home",
                        "time" => 0
                    ];

                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/footer', $data);
                } else {
                    // Show the logout message if the user just logged out.
                    if($this->session->flashdata('logout')) {
                        $this->Logger->add_message('You succesfully logged out.', 'info');
                    }

                    $data['log'] = $this->Logger->get_html();

                    $this->load->view('templates/header', $data);
                    $this->load->view('templates/login');
                    $this->load->view('templates/footer', $data);
                }
            break;
            case 'logout':
                $data['redirect'] = [
                    "target" => "login",
                    "time" => 0
                ];
                $data['title'] = 'Logout';
                $data['navigation'] = false;

                $data['log'] = $this->Logger->get_html();

                $this->load->view('templates/header', $data);
                $this->load->view('templates/footer', $data);
                break;
            default:
                // Set multiple variables used during page rendering.
                $data['title'] = $page;
                $data['navigation'] = true;
                $data['log'] = $this->Logger->get_html();

                // Show the page.
                $this->load->view('templates/header', $data);

                if(!isset($this->pages[$page])) {
                    show_404();
                } elseif(!file_exists(APPPATH.'views/pages/' . $page . '.php')) {
                    show_404();
                } elseif($this->pages[$page]['admin'] && !$this->user_data['admin']) {
                    $this->load->view('templates/admin_only', $data);
                } elseif($this->pages[$page]['tillmanager'] && !$this->user_data['till_manager']) {
                    $this->load->view('templates/tillmanager_only', $data);
                } else {
                    $this->load->view('pages/' . $page, $data);
                }
                $this->load->view('templates/footer', $data);
                break;
        }
    }

    public function ajax($target) {
      $this->load->library('session');
      $this->DBManager = new DBManager($this);

      $this->current_user_logged_in();
      $this->get_data_current_user();

      // If the user is not logged in, then show an error.
      if(!$this->logged_in) {
        echo "failed: user not logged in";
      } else {
        switch($target) {
          case 'admin-switch':
            // Check if the current user is an admin.
            if($this->user_data['admin']) {
              $username = $this->input->post('name');
              $value = $this->input->post('value');

              if('admin' == $username || 'local' == $username) {
                echo "failed: not allowed to change the admin status of the main admin or 'local' account.";
              } elseif($username == "" || $value == "") {
                echo "failed: invalid data parsed";
              } else {
                // Convert the switch value into a boolean value.
                $value = $value == "True" || $value == "Yes";

                echo $this->DBManager->update_admin($username, $value);
              }
            } else {
              echo "failed: access denied";
            }
            break;
          case 'till_manager-switch':
            case 'admin-switch':
              // Check if the current user is an admin.
              if($this->user_data['admin']) {
                $username = $this->input->post('name');
                $value = $this->input->post('value');

                // Convert the switch value into a boolean value.
                $value = $value == "True" || $value == "Yes";

                if('admin' == $username || 'local' == $username) {
                  echo "failed: not allowed to change the till manager status of the main admin or 'local' account.";
                } else {
                  echo $this->DBManager->update_till_manager($username, $value);
                }
              } else {
                echo "failed: access denied";
              }
            break;
          default:
            echo "No AJAX script found for '$target'";
        }
      }
    }
}
