<?php
abstract class tables {
  const users = "user_table";
  const transactions = "trans_table";
  const journal = "journ_table";
  const posts = "posts_table";
}

class DBManager {
    private $ci;
    public $curUser;

    function __construct($ci) {
        $this->ci = $ci; // Parse controller object.
        $this->ci->load->database();

        $this->create_missing_tables();
    }

    /**
     * Adds a new user to the user table.
     */
    public function add_user($username, $first_name, $last_name, $password, $admin, $till_manager, $conf_password = null, $email = null) {
        if($username === null || $username === "") {
            return 'username';
        }

        if($password == null || $password == "") {
            return 'password';
        }

        // Check if a valid email has been parsed.
        if($email != null) {
            // Check if the email is empty.
            if($email == "") {
                return 'email-empty';
            }

            // Check if the email has a valid syntax.
            if(!$this->check_email($email)) {
                return 'email-invalid';
            }
        }

        // If the confirmation password is passed, check if it matches.
        if($conf_password != null && $password != $conf_password) {
            return 'password-conf';
        }

        // Check if the username is already in use.
        if($this->user_exists($username)) {
            return 'username-exists';
        }

        $data = [
          'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'post_id' => $username = "admin" ? null : $this->add_post($username),
            'password' => $this->hash_password($password),
            'pin' => $this->generate_pin($this->ci->config->item('pin_length')),
            'admin' => $admin,
            'till_manager' => $till_manager,
            'email' => $email
        ];

        $this->ci->db->insert(tables::users, $data);
    }

    private function user_exists($username) {
        $this->ci->db->select(['username']);
        $this->ci->db->where(['username' => $username]);
        $q = $this->ci->db->get(tables::users);

        if($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_post($name) {
        if(is_array($name)) {
            foreach($name as $one_name) {
                $this->add_post($one_name);
            }
        } else {
            $data = [
                "name" => $name
            ];

            $this->ci->db->insert(tables::posts, $data);

            echo $this->ci->db->insert_id();
        }
    }

    /**
     * Get's all data in the user table for one user.
     * @param string The username whom's data should be returned
     * @return array All user data as an array.
     */
    public function get_user_data($username) {
        $this->ci->db->where(['username' => $username]);
        $this->ci->db->select(['*']);

        $data = $this->ci->db->get(tables::users)->row_array();

        // Add zeros to the start of the pin to make them equal in length.
        while(strlen("0" . $data['pin']) <= $this->ci->config->item('pin_length')) {
            $data['pin'] = "0" . $data['pin'];
        }

        return $data;
    }

    /**
     * Returns all user data as an two dimensional array.
     * @param boolean/null A boolean value if only, or no, admins should be selected.
     * @param boolean/null A boolean value if only, or no, till managers should be selected.
     * @return array A two dimensional array with the information of all users.
     */
    public function get_all_user_data($admin = null, $till_manager = null) {
        if($admin != null) {
            $this->ci->db->where(['admin' => true]);
        }

        if($till_manager != null) {
            $this->ci->db->where(['till_manager' => true]);
        }

        $this->ci->db->select("*");
        $q = $this->ci->db->get(tables::users);

        return $q->result_array();
    }

    /**
     * Hashes a password.
     * @param string The password to be hashed.
     * @return string The hash of the password.
     */
    private function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Generates a pin of the specified length.
     * @param int The required length of the pin.
     * @return int The pin as integer.
     */
    private function generate_pin($length) {
        return rand(0, (pow(10, $length)) - 1);
    }

    /**
     * Checks if an email address has a valid syntax.
     * @param string The email address to be checked.
     * @return boolean If the email's syntax is valid.
     */
    private function check_email($email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the user credentials are present in the database.
     * @param string The username.
     * @param string The password.
     * @return string The result of the check, 'valid' if it was correct,
     *      'username' if the username wasn't found, and 'password' if the
     *      password did not match the hashed password in the database.
     */
    public function check_user_credentials($username, $password) {
        // Query for the password.
        $this->ci->db->where(['username' => $username]);
        $this->ci->db->select(['password']);
        $q = $this->ci->db->get(tables::users);

        // Check if the username was found.
        if($q->num_rows() > 0) {
            // Get the hashed password.
            $hash = $q->row()->password;

            // Check if the hashed password corresponds to entered password.
            if(password_verify($password, $hash)) {
                return 'valid'; // The entered password matches the hashed version in the database.
            } else {
                return 'password'; // The entered password did not match the hashed version in the database.
            }
        } else {
            return 'username'; // Username not found
        }
    }

    /**
    * Updates a field of the username table.
    */
    private function update_username_field($username, $field, $value) {
      $this->ci->db->where('username', $username);

      if($this->ci->db->update(tables::users, [$field => $value])) {
          return 'succes';
      } else {
          return 'db-update-failure';
      }
    }

    /**
     * Updates the password of a user.
     * @param string The username of the account which password should be updated.
     * @param string The old password of the user, if left null then it won't be checked (not recommended).
     * @param string The new password of the user.
     */
    public function update_password($username, $old_password, $new_password, $new_password_conf = null) {
        // Check if the confirmation password is correct.
        if($new_password_conf !== null) {
            if($new_password_conf !== $new_password) {
                return 'passwords-not-equal';
            }
        }

        // Check if the old password is correct, if it was parsed to this function.
        if($old_password != null) {
            $password_check = $this->check_user_credentials($username, $old_password);

            if($password_check != 'valid') {
                return $password_check;
            }
        }

        // Generate a hashed version of the new password.
        $hash = $this->hash_password($new_password);

        return $this->update_username_field($username, 'password', $hash);
    }

    public function update_admin($username, $value) {
      return $this->update_username_field($username, 'admin', $value);
    }

    public function update_till_manager($username, $value) {
      return $this->update_username_field($username, 'till_manager', $value);
    }

    public function reset_pin($username) {
        $this->ci->db->where(['username' => $username]);
        $this->ci->db->select(['username']);
        $q = $this->ci->db->get(tables::users);

        if($q->num_rows() == 0) {
            return 'username';
        } else {
            $this->ci->db->where(['username' => $username]);
            $this->ci->db->update(tables::users, ['pin' => $this->generate_pin($this->ci->config->item('pin_length'))]);

            return 'succes';
        }
    }

    /**
    * Creates the required tables, if they are missing from the database.
    */
   private function create_missing_tables() {
       // Define the SQL creation code for each table we need.
       $table_sql = [
            tables::posts => "CREATE TABLE " . tables::posts . " (
            `post_id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` tinytext,
            `debit` FLOAT(10,2) DEFAULT 0.00,
            `credit` FLOAT(10,2) DEFAULT 0.00,
            `cdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `edate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`post_id`)
            )
            ENGINE=InnoDB
            AUTO_INCREMENT=7000000
            ;",

           tables::users => "CREATE TABLE `" . tables::users . "` (
           id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
           username tinytext NOT NULL,
           first_name tinytext,
           last_name tinytext,
           post_id mediumint,
           email tinytext,
           pin int(12) NOT NULL UNIQUE,
           password tinytext NOT NULL,
           admin BOOLEAN NOT NULL DEFAULT FALSE,
           till_manager BOOLEAN NOT NULL DEFAULT FALSE,
           cdate datetime DEFAULT NOW() NOT NULL,
           edate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           UNIQUE KEY id (id)
      )
       ENGINE=InnoDB
       AUTO_INCREMENT=0;",

       tables::transactions => "CREATE TABLE " . tables::transactions . " (
           trans_id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
           author_id mediumint UNSIGNED NOT NULL,
           description tinytext,
           type ENUM('inventory purchase', 'purchase', 'decleration', 'payout', 'refund', 'upgrade', 'unknown', 'error') DEFAULT 'error' NOT NULL,
           state ENUM('new', 'unapproved', 'confirmed', 'finished', 'canceled', 'error', 'not payed') DEFAULT 'error' NOT NULL,
           cdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
           edate datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL ,
           UNIQUE KEY id (trans_id)
       )
       ENGINE=InnoDB
       AUTO_INCREMENT=1000000
       ;",

       tables::journal => "CREATE TABLE " . tables::journal . " (
           `journ_id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
           `trans_id` mediumint UNSIGNED NOT NULL,
           `accountid` mediumint UNSIGNED NOT NULL,
           `cd` ENUM('credit','debit','error') NOT NULL DEFAULT 'error',
           `amount` FLOAT(6,2) NOT NULL,
           `new_balance` FLOAT(10,2),
           `payed` DATETIME DEFAULT NULL,
           `cdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
           `edate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           PRIMARY KEY (`journ_id`)
       )
       ENGINE=InnoDB
       AUTO_INCREMENT=5000000
       ;"
       ];

       // Check for each table if it exists, if not create it.
       foreach($table_sql as $name => $sql) {
           if($this->ci->db->table_exists($name)) {
               // This get's executed if the table already exists.
           } elseif($this->ci->db->query($sql)) { // Create the table if it doesn't exist.
               // Give the user feedback that a table was created.
               $this->ci->Logger->add_warning("Table '$name' created");

               // Add the default admin user to the user table.
               switch($name) {
                    case tables::users:
                        $this->add_user('admin', 'Site', 'Admin', 'Banana', true, false);
                        break;
                    case tables::posts:
                        $this->add_post(['Inventory', 'Deposit', 'Profit', 'COGS', 'Sales revenue']);
                        break;
               }
           } else {
               $this->ci->Logger->add_error("Failed to create '$name'.");
           }
       }
   }
}
