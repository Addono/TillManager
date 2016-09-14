<?php
abstract class tables {
  const users = "user_table";
  const transactions = "trans_table";
  const journal = "journ_table";
  const posts = "posts_table";
  const products = "products_table";
  const logins = "logins_table";
}

class DBManager {
    private $ci;

    function __construct($ci) {
        $this->ci = $ci; // Parse controller object.
        $this->ci->load->database();

        $this->create_missing_tables();

        $actions = [
          [
            "post_id" => 7000000,
            "amount" => 5.95
          ],
          [
            "post_id" => 7000008,
            "amount" => 5.95
          ],
        ];

        //$this->create_transaction(1, "unknown", "", $actions);
    }

    //////////\\\\\\\\\\
    //     Users      \\
    //////////\\\\\\\\\\

    /**
     * Adds a new user to the user table, also creates a new post for this user.
     */
    public function add_user($username, $first_name, $prefix_name, 
            $last_name, $password, $admin, $till_manager, 
            $conf_password = null, $email = null) {
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
            'prefix_name' => $prefix_name,
            'last_name' => $last_name,
            'debit_post_id' => $username == "admin" ? null : $this->add_post($username, 'debit', 1, $this->get_masterpost_id("Till debit")),
            'credit_post_id' => $username == "admin" ? null : $this->add_post($username, 'credit', 1, $this->get_masterpost_id("Till credit")),
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
    public function check_user_credentials($username, $password, $type, $author = null) {
        // Query for the password.
        $this->ci->db->where(['username' => $username]);
        $this->ci->db->select(['password']);
        $q = $this->ci->db->get(tables::users);
        
        $return;

        // Check if the username was found.
        if($q->num_rows() > 0) {
            // Get the hashed password from the database.
            $hash = $q->row()->password;

            // Check if the hashed password corresponds to entered password.
            if(password_verify($password, $hash)) {
                $return =  'valid'; // The entered password matches the hashed version in the database.
            } else {
                $return = 'password'; // The entered password did not match the hashed version in the database.
            }
        } else {
            $return = 'username'; // Username not found
        }
        
        $this->add_login($username, $type, $return, $author);
        
        return $return;
    }

    /**
    * Updates a field of the username table.
    * @param string The username from whom a field should be altered.
    * @param string The name of the field whom should be updated.
    * @param mixed  The new value of the field.
    * @return string Returns if the database update was succesfull.
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
            $password_check = $this->check_user_credentials($username, $old_password, 'password_change');

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
    
    //////////\\\\\\\\\\
    //     Logins     \\
    //////////\\\\\\\\\\
    
    private function add_login($username, $type, $success, $author = null) {
        $data = [
            "username" => $username,
            "type" => $type,
            "success" => $success,
            "author" => $author,
            "ip" => $_SERVER['REMOTE_ADDR']
        ];
        
        $this->ci->db->insert(tables::logins, $data);
    }

    //////////\\\\\\\\\\
    //  Transactions  \\
    //////////\\\\\\\\\\

    public function create_transaction($author_id, $type, $description, $actions) {
      $credit = 0;
      $debit = 0;

      foreach($actions as $action) {
        // Check if all required fields are set.
        if(!(isset($action['amount']) && isset($action['post_id']))) {
          return "error: invalid action";
        }

        $action['cd'] = $this->get_post_cd($action['post_id']);

        // Sum all credit and debit values.
        if($action['cd'] == 'credit') {
          $credit += $action['amount'];
        } elseif($action['cd'] == 'debit') {
          $debit += $action['amount'];
        } else {
          return "error: invalid cd parsed";
        }
      }

      // Check if the credit and debit sum are equal.
      if($credit !== $debit) {
        return "error: debit and credit sum not equal";
      }

      // Create a new transaction.
      switch($type) {
        case 'purchase':
        case 'sell':
        case 'decleration':
        case 'payout':
        case 'refund':
        case 'upgrade':
        case 'unknown':
          $data = [
            "author_id" => $author_id,
            "description" => $description,
            "type" => $type
          ];

          if(!$this->ci->db->insert(tables::transactions, $data)) {
            return "error: database insertion failed";
          }
          break;
        default:
          return "error: type not valid";
      }

      $trans_id = $this->ci->db->insert_id();

      foreach ($actions as $action) {
        $this->create_journal($author_id, $trans_id, $action['post_id'], $action['amount']);
      }
    }

    //////////\\\\\\\\\\
    //    Journals    \\
    //////////\\\\\\\\\\

    private function create_journal($author_id, $trans_id, $post_id, $amount) {
      echo $result = $this->update_post($post_id, $amount) . "\n";

      if(is_string($result) && $result != "success") {
        error_log("Journal creation failed with post updating, report'$result'");
        return "error: could not update post";
      }

      $data = [
        "trans_id" => $trans_id,
        "post_id" => $post_id,
        "amount" => $amount,
        "new_balance" => $result
      ];

      if($this->ci->db->insert(tables::journal, $data)) {
        return $this->ci->db->insert_id();
      } else {
        return "error: could not insert new journal into db";
      }
    }

    //////////\\\\\\\\\\
    //     Posts      \\
    //////////\\\\\\\\\\

    public function add_post($name, $cd, $priority = 0, $parent = null, $id = null) {
        if($cd != "credit" && $cd != "debit") {
          return "error: invalid cd parsed";
        }

        if(!($parent == null || $parent == -1) && !$this->post_exists($parent)) {
          return "error: parent not found";
        }

        $data = [
            "name" => $name,
            "cd" => $cd,
            "priority" => $priority,
            "parent" => $parent
        ];

        if($id != null) {
          $data["post_id"] = $id;
        }

        $this->ci->db->insert(tables::posts, $data);

        return $this->ci->db->insert_id();
    }

    /**
     * Adds multiple posts all parsed as an array.
     */
    public function add_posts($posts) {
      foreach($posts as $name => $post) {
        if(isset($post['priority'])) {
          switch($post['parent']) {
            case true:
              $post['parent'] = 0;
              break;
            case false:
            default:
              $post['parent'] = null;
              break;
          }

          if(isset($post['id'])) {
            $this->add_post($name, $post['cd'], $post['priority'], $post['parent'], $post['id']);
          } else {
            $this->add_post($name, $post['cd'], $post['priority'], $post['parent']);
          }
        } else {
          $this->add_post($post['name'], $post['cd']);
        }
      }
    }

    private function update_post($post_id, $amount) {
      // Check if the post exists.
      if(!$this->post_exists($post_id)) {
        return "error: post not found";
      }

      // Update the parent - if it has one.
      $this->ci->db->where('post_id', $post_id);
      $this->ci->db->select("parent");
      $result = $this->ci->db->get(tables::posts);
      $parent = $result->row()->parent;

      if($parent === 0) {
        // Prevent direct alteration of a parent post.
        return "error: can not only alter a parent post";
      } elseif($parent != null) {
        // Update the parent post.
        if($this->update_post_amount($parent, $amount) != "success") {
          return "error: could not update amount parent";
        }
      }

      // Update the target post.
      if($this->update_post_amount($post_id, $amount) != "success") {
        return "error: could not update amount post";
      } else {
        return "success";
      }
    }

    private function update_post_amount($post_id, $amount) {
      if(!$this->post_exists($post_id)) {
        return "error: post does not exists";
      }

      // Get the old amount from the database.
      $this->ci->db->where('post_id', $post_id);
      $this->ci->db->select(["amount"]);
      $result = $this->ci->db->get(tables::posts);
      $old_amount = $result->row()->amount;

      // Calculate the new amount.
      $data = [
        "amount" =>  $old_amount + $amount
      ];

      // Update the database with the new amount.
      $this->ci->db->where('post_id', $post_id);
      if($this->ci->db->update(tables::posts, $data)) {
        return "success";
      } else {
        return "error: could not update post";
      }
    }

    private function post_exists($post_id) {
      $this->ci->db->where("post_id", $post_id);
      $amount = $this->ci->db->get(tables::posts)->num_rows();

      return $amount >= 1;
    }

    private function get_post_cd($post_id) {
      $this->ci->db->where('post_id', $post_id);
      $this->ci->db->select("cd");

      return $this->ci->db->get(tables::posts)->row()->cd;
    }

    public function get_posts($parents_only) {
      $this->ci->db->select("*");
      $this->ci->db->where("parent", null);
      $this->ci->db->or_where("parent", 0);

      return $this->ci->db->get(tables::posts)->result_array();
    }

    private function get_masterpost_id($name) {
      return self::master_posts[$name]["id"];
    }

    const posts_id_start = 7000000;
    const master_posts = [
      "Possessions" => [
        "cd" => "debit",
        "parent" => false,
        "priority" => 3,
        "id" => self::posts_id_start
      ],
      "Inventory" => [
        "cd" => "debit",
        "parent" => true,
        "priority" => 2,
        "id" => self::posts_id_start + 1
      ],
      "Till debit" => [
        "cd" => "debit",
        "parent" => true,
        "priority" => 1,
        "id" => self::posts_id_start + 2
      ],
      "Equity" => [
        "cd" => "credit",
        "priority" => 3,
        "id" => self::posts_id_start + 3
      ],
      "Till credit" => [
        "cd" => "credit",
        "parent" => true,
        "priority" => 1,
        "id" => self::posts_id_start + 4
      ]
    ];

    /**
    * Creates the required tables, if they are missing from the database.
    */
   private function create_missing_tables() {
       // Define the SQL creation code for each table we need.
       $table_sql = [
            tables::posts => "CREATE TABLE " . tables::posts . " (
            `post_id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
            `parent` mediumint UNSIGNED DEFAULT null,
            `name` tinytext,
            `cd` ENUM('credit','debit') NOT NULL,
            `amount` FLOAT(10,2) DEFAULT 0.00,
            `priority` tinyint UNSIGNED NOT NULL DEFAULT 0,
            `cdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `edate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`post_id`)
            )
            ENGINE=InnoDB
            AUTO_INCREMENT=" . self::posts_id_start . "
            ;",

           tables::users => "CREATE TABLE `" . tables::users . "` (
           id mediumint UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
           username tinytext NOT NULL,
           first_name tinytext,
           prefix_name tinytext,
           last_name tinytext,
           debit_post_id mediumint,
           credit_post_id mediumint,
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
               trans_id mediumint UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
               author_id mediumint UNSIGNED NOT NULL,
               description tinytext,
               type ENUM('purchase', 'sell', 'decleration', 'payout', 'refund', 'upgrade', 'unknown', 'error') DEFAULT 'error' NOT NULL,
               approved BOOLEAN NOT NULL DEFAULT FALSE,
               cdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
               edate datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL ,
               UNIQUE KEY id (trans_id)
           )
           ENGINE=InnoDB
           AUTO_INCREMENT=1000000
           ;",

           tables::journal => "CREATE TABLE " . tables::journal . " (
               `journ_id` mediumint UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
               `trans_id` mediumint UNSIGNED NOT NULL,
               `post_id` mediumint UNSIGNED NOT NULL,
               `amount` FLOAT(6,2) NOT NULL,
               `new_balance` FLOAT(10,2),
               `cdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
               `edate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
               UNIQUE KEY id (`journ_id`)
           )
           ENGINE=InnoDB
           AUTO_INCREMENT=5000000
           ;",
           
           tables::products => "CREATE TABLE " . tables::products . " (
               `product_id` mediumint UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
               `post_id` mediumint UNSIGNED NOT NULL,
               `name` tinytext NOT NULL,
               `description` tinytext
            )
            ENGINE=InnoDB
            AUTO_INCREMENT=9000000",
           
           tables::logins => "CREATE TABLE " . tables::logins . " ( 
               `id` mediumint UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
               `username` tinytext NOT NULL,
               `author` TINYINT UNSIGNED,
               `type` ENUM('login', 'password_change') NOT NULL,
               `success` TINYTEXT NOT NULL,
               `ip` tinytext NOT NULL,
               `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
           )
           ENGINE=InnoDB
           AUTO_INCREMENT=2000000"
       ];

       // Check for each table if it exists, if not create it.
       foreach($table_sql as $name => $sql) {
           if($this->ci->db->table_exists($name)) {
               // This get's executed if the table already exists.
           } elseif($this->ci->db->query($sql)) { // Create the table if it doesn't exist.
               // Give the user feedback that a table was created.
               $this->ci->Logger->add_message("Table '$name' created", "plus");

               // Add the default admin user to the user table.
               switch($name) {
                    case tables::users:
                        $this->add_user('admin', 'Site', '', 'Admin', 'Banana', true, false);
                        $this->add_user('local', 'Local', '', 'Computer', 'Banana', false, false);
                        break;
                    case tables::posts:
                        $this->add_posts(self::master_posts);
                        break;
               }
           } else {
               $this->ci->Logger->add_error("Failed to create '$name'.");
           }
       }
   }
}
