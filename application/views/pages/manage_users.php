<?php
if($user_data['admin'] != 1) {
    echo $this->Util->get_html_not_admin();
} else {
    $this->page_Logger = new Logger;
    
    $user_added = false;
    
    $form['username'] = $this->input->post('username');
    $form['first_name'] = $this->input->post('first_name');
    $form['last_name'] = $this->input->post('last_name');
    $form['email']  = $this->input->post('email');
    $form['password'] = $this->input->post('password');
    $form['password_conf'] = $this->input->post('password_confirm');
    $form['admin'] = $this->input->post('admin');
    $form['till_manager'] = $this->input->post('till_manager');
    
    $form_filled = false;
    
    foreach($form as $field) {
        if($field !== null) {
            $form_filled = true;
        }
    }
    
    if($form_filled) {
        switch($this->DBManager->add_user($form['username'], $form['first_name'], $form['last_name'], $form['password'], $form['admin'], $form['till_manager'], $form['password_conf'], $form['email'])) {
            case 'username':
                $this->page_Logger->add_warning("Failed to add user, username should not be empty. Please fill the username field and try again.");
                break;
            case 'username-exists':
                $this->page_Logger->add_warning("Failed to add user, username is already in use. Try again with a different username.");
                break;
            case 'email-empty':
                $this->page_Logger->add_warning("Failed to add user, email address should not be empty.");
                break;
            case 'email-invalid':
                $this->page_Logger->add_warning("Failed to add user, email address has an invalid syntax.");
                break;
            case 'password':
                $this->page_Logger->add_warning("Failed to add user, password should not be empty.");
                break;
            case 'password-conf':
                $this->page_Logger->add_warning("Failed to add user, passwords did not match. Make sure that they are equal.");
                break;
            default:
                $this->page_Logger->add_message("User '" . $form['username'] . "' added succesfully.", "check");
                $user_added = true;
                break;
        }
    }
    
    ?>
    <div class="ui-body ui-body-a ui-corner-all">
    <h3>Add user</h3>
    <p><i>All fields are required</i></p>
    
    <form method="post">
        <div class="ui-field-contain">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php if(!$user_added) echo $form['username'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="first_name">First name</label>
            <input type="text" name="first_name" value="<?php if(!$user_added) echo $form['first_name'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="last_name">Last name</label>
            <input type="text" name="last_name" value="<?php if(!$user_added) echo $form['last_name'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="username">Email</label>
            <input type="text" name="email" value="<?php if(!$user_added) echo $form['email'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="new_password">Password</label>
            <input type="password" name="password" required />
        </div>

        <div class="ui-field-contain">
            <label for="password_confirm">Confirm password</label>
            <input type="password" name="password_confirm" required />
        </div>

        <?php
        
            // Add the two switches for the user rights.
            echo $this->Util->form->get_switch('admin', 'Admin', 'No', 'Yes', !$user_added ? $form['admin'] : true);  
            echo $this->Util->form->get_switch('till_manager', 'Till Manager', 'No', 'Yes', !$user_added ? $form['admin'] : true);
            
            // Add the submit button.
            echo $this->Util->form->get_submit('Add user', false);
        ?>
    </form>
</div> <br>

<?php
    $this->page_Logger->show_html();
}
?>

<div class="ui-body ui-body-a ui-corner-all">
    <h3>User information</h3>
    <table data-role="table" data-mode="reflow" class="ui-responsive">
        <?php
        /**
         *  Stores which columns should be displayed, and how they should be displayed.
         *  'name' is the name of the SQL column.
         *  'friendly-name' is the title of the field.
         *  'type' is the type of data which it contains.
         *  'priority' is the priority of the column, only used when the tables data mode is set to 'columntoggle'.
         */
            $columns = [
                [
                    'name' => 'username',
                    'friendly-name' => 'Username',
                    'type' => 'string',
                    'priority' => '1'
                ],
                [
                    'name' => 'first_name',
                    'friendly-name' => 'First name',
                    'type' => 'string',
                    'priority' => '2'
                    
                ],
                [
                    'name' => 'last_name',
                    'friendly-name' => 'Last name',
                    'type' => 'string',
                    'priority' => '3'
                ],
                [
                    'name' => 'email',
                    'friendly-name' => 'Email',
                    'type' => 'string',
                    'priority' => '3'
                ],
                [
                    'name' => 'admin',
                    'friendly-name' => 'Admin',
                    'type' => 'boolean',
                    'true' => 'Yes',
                    'false' => 'No',
                    'priority' => '4'
                ],
                [
                    'name' => 'till_manager',
                    'friendly-name' => 'Till manager',
                    'type' => 'boolean',
                    'true' => 'Yes',
                    'false' => 'No',
                    'priority' => '5'
                ],
                [
                    'name' => 'debit',
                    'friendly-name' => 'Debit',
                    'type' => 'euros',
                    'priority' => '6'
                ],
                [
                    'name' => 'credit',
                    'friendly-name' => 'Credit',
                    'type' => 'euros',
                    'priority' => '6'
                ]
            ];
            
            $users = $this->DBManager->get_all_user_data();
            
            // Return the first row containing the table header.
            echo "<thead><tr>\n";
            
            foreach($columns as $column) {
                // Check if the priority field is set, if so add it to the head of the column.
                if(isset($column['priority'])) {
                    echo "\t\t<th data-priority='" . $column['priority'] . "'>";
                } else {
                    echo "\t\t<th>";
                }
                
                echo $column['friendly-name'];
                echo "</th>\n";
            }
            
            echo "\t</tr></thead>\n";
            
            // Return the table body.
            echo "\t<tbody>\n";
            foreach($users as $user) { // Return a row for every user.
                echo "\t\t<tr>\n";
                
                // Return every column for each user.
                foreach($columns as $key => $column) {
                    // Make the first cell in each row the header.
                    if($key == 0) {
                        echo "\t\t\t<th>";
                    } else {
                        echo "\t\t\t<td>";
                    }
                    
                    // Return every column, how is based on the column's type.
                    switch($column['type']) {
                        case 'string':
                        case 'float':
                            echo $user[$column['name']];
                            break;
                        case 'euros':
                            echo "&euro;" . $user[$column['name']];
                            break;
                        case 'boolean':
                            // Check if the field is true or false.
                            if($user[$column['name']]) {
                                // Check if a different string for true is set, else use default value.
                                if(isset($column['true'])) {
                                    echo $column['true'];
                                } else {
                                    echo "True";
                                }
                            } else {
                                // Check if a different string for false is set, else use default value.
                                if(isset($column['false'])) {
                                    echo $column['false'];
                                } else {
                                    echo "False";
                                }
                            }
                            break;
                    }
                    
                    // Make the first cell of each row the header.
                    if($key == 0) {
                        echo "</th>\n";
                    } else {
                        echo "</td>\n";
                    }
                }
                
                echo "\t\t</tr>\n";
            }
            
            echo "\t</tbody>\n";
        ?>
    </table>
</div>