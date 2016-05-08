<?php
class DBManager {
    private $ci;
    public $curUser;
    
    const user_table = "user_table";
    const trans_table = "trans_table";
    const journ_table = "journ_table";
    
    function __construct($ci) {
        $this->ci = $ci; // Parse controller object.
        $this->ci->load->database();
        
        $this->create_missing_tables();
    }
    
    /**
     * Adds a new user to the user table.
     */
    public function add_user($username, $first_name, $last_name, $password, $admin, $till_manager, $conf_password = null) {
        if($username == null || $username == "") {
            return 'username';
        }
        
        if($password == null || $password == "") {
            return 'password';
        }

        // If the confirmation password is passed, check if it matches.
        if($conf_password != null && $password != $conf_password) {
            return 'password-conf';
        }

        // Check if the username is unique.
        $this->ci->db->where(['username' => $username]);
        $q = $this->ci->db->get(self::user_table);
        
        if($q->num_rows > 0) {
            return 'username-exists'; // Return if the username was not unique.
        }
        
        $data = [
          'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => $this->hash_password($password),
            'pin' => $this->generate_pin($this->ci->config->item('pin_length')),
            'admin' => $admin,
            'till_manager' => $till_manager
        ];
        
        $this->ci->db->insert(self::user_table, $data);
    }
    
    /**
     * Get's all data in the user table for one user.
     * @param string The username whom's data should be returned
     * @return array All user data as an array.
     */
    public function get_user_data($username) {
        $this->ci->db->where(['username' => $username]);
        $this->ci->db->select(['*']);
        
        $data = $this->ci->db->get(self::user_table)->row_array();
        
        // Add zeros to the start of the pin to make them equal in length.
        while(strlen("0" . $data['pin']) <= $this->ci->config->item('pin_length')) {
            $data['pin'] = "0" . $data['pin'];
        }
        
        return $data;
    }
    
    public function get_all_user_data($admin = null, $till_manager = null) {
        if($admin != null) {
            $this->ci->db->where(['admin' => true]);
        }
        
        if($till_manager != null) {
            $this->ci->db->where(['till_manager' => true]);
        }
        
        $this->ci->db->select("*");
        $q = $this->ci->db->get(self::user_table);
        
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
        $q = $this->ci->db->get(self::user_table);
        
        // Check if the username was found.
        if($q->num_rows() > 0) {
            // Get the hashed password.
            $hash = $q->row()->password;
            
            // Check if the hashed password corresponds to entered password.
            if(password_verify($password, $hash)) {
                return 'valid';
            } else {
                return 'password';
            }
        } else {
            return 'username'; // Username not found
        }
    }
    
    /**
     * 
     * @param string The username of the account which password should be updated.
     * @param string The old password of the user, if left null then it won't be checked (not recommended).
     * @param string The new password of the user.
     */
    public function update_password($username, $old_password, $new_password) {
        // Check if the old password is correct, if it was parsed.
        if($old_password != null) {
            $password_check = $this->check_user_credentials($username, $old_password);
            
            if($password_check != 'valid') {
                return $password_check;
            }
        }
        
        $this->ci->db->where('username', $username);
        $hash = $this->hash_password($new_password); // The hashed version of the new password.
        
        if($this->ci->db->update(self::user_table, ['password' => $hash])) {
            return 'succes';
        }
    }
    
    /**
    * Creates the required tables, if they are missing from the database.
    */
   private function create_missing_tables() {
       // Define the SQL creation code for each table we need.
       $table_sql = [
           self::user_table => "CREATE TABLE `" . self::user_table . "` (
           id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
           username tinytext NOT NULL,
           first_name tinytext,
           last_name tinytext,
           email tinytext,
           pin int(12) NOT NULL UNIQUE,
           password tinytext NOT NULL,
           debit float(10,2) DEFAULT '0' NOT NULL,
           credit float(10,2) DEFAULT '0' NOT NULL,
           admin BOOLEAN NOT NULL DEFAULT FALSE,
           till_manager BOOLEAN NOT NULL DEFAULT FALSE,
           cdate datetime DEFAULT NOW() NOT NULL,
           edate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           UNIQUE KEY id (id)
      )
       ENGINE=InnoDB
       AUTO_INCREMENT=0;",

       self::trans_table => "CREATE TABLE " . self::trans_table . " (
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

       self::journ_table => "CREATE TABLE " . self::journ_table . " (
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
               if($name == self::user_table) {
                   $this->add_user('admin', 'Site', 'Admin', 'Banana', true, false);
               }
           } else {
               $this->ci->Logger->add_error("Failed to create '$name'.");
           }
       }
   }
}