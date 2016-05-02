<?php
class DBManager {
    private $ci;
    const user_table_name = "user_table";
    const trans_table_name = "trans_table";
    const journ_table_name = "journ_table";
    
    function __construct($ci) {
        $this->ci = $ci; // Parse controller object.
        $this->ci->load->database();
        
        $this->create_missing_tables();
    }
    
    /**
     * Adds a new user to the user table.
     */
    public function add_user($nickname, $first_name, $last_name, $password) {
        $data = [
          'nickname' => $nickname,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => $this->hash_password($password),
            'pin' => $this->generate_pin($this->ci->config->item('pin_length'))
        ];
        
        $this->ci->db->insert(self::user_table_name, $data);
    }
    
    private function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Generates a pin, as a string, of the specified length.
     * @param int The required length of the pin. 
     * @return string The pin as a string.
     */
    private function generate_pin($length) {
        $pin = "";
        
        for($i = 0; $i < $length; $i++) {
            $pin .= rand(0, 9);
        }
        
        return $pin;
    }
    
    public function check_user_credentials($username, $password) {
        $this->ci->db->where(['username' => $username, 'pass' => $password]);
        
        $q = $this->ci->db->get(self::user_table_name);
        
        return $q->num_rows() > 0;
    }
    
    /**
    * Creates the required tables missing from the database.
    */
   private function create_missing_tables() {
       $table_sql = [
           self::user_table_name => "CREATE TABLE `" . self::user_table_name . "` (
           id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
           nickname tinytext NOT NULL,
           first_name tinytext,
           last_name tinytext,
           pin tinytext NOT NULL,
           password tinytext NOT NULL,
           debit float(10,2) DEFAULT '0' NOT NULL,
           credit float(10,2) DEFAULT '0' NOT NULL,
           role ENUM('till manager', 'user', 'till') DEFAULT 'user' NOT NULL,
           cdate datetime DEFAULT NOW() NOT NULL,
           edate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           UNIQUE KEY id (id)
      )
       ENGINE=InnoDB
       AUTO_INCREMENT=0;",

       self::trans_table_name => "CREATE TABLE " . self::trans_table_name . " (
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

       self::journ_table_name => "CREATE TABLE " . self::journ_table_name . " (
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

       foreach($table_sql as $name => $sql) {
           if($this->ci->db->table_exists($name)) {
               $this->ci->Logger->add_comment("Table $name already exists");
           } elseif($this->ci->db->query($sql)) {
               $this->ci->Logger->show_warning("Table '$name' created");
               
               if($name == self::user_table_name) {
                   $this->add_user('admin', 'Site', 'Admin', 'Banana', 0);
               }
           } else {
               $this->ci->Logger->show_error("Failed to create '$name'.");
           }
       }
   }
}