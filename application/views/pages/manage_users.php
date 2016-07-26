<?php
$this->page_Logger = new Logger;

// Get all post variables into one array.
$form['username'] = $this->input->post('username');
$form['first_name'] = $this->input->post('first_name');
$form['last_name'] = $this->input->post('last_name');
$form['email']  = $this->input->post('email');
$form['password'] = $this->input->post('password');
$form['password_conf'] = $this->input->post('password_confirm');
$form['admin'] = $this->input->post('admin');
$form['till_manager'] = $this->input->post('till_manager');
$form['username-change-password'] = $this->input->post('username-change-password');
$form['change-password'] = $this->input->post('change-password');
$form['conf-change-password'] = $this->input->post('conf-change-password');

// The default values of all form fields.
$form_default = [
            'username' => '',
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_conf' => '',
            'admin' => null,
            'till_manager' => null,
            'username-change-password' => '',
            'change-password' => '',
            'conf-change-password' => ''
        ];

$user_added = false;
$form_filled = false;

switch($this->input->post('type')) {
    case('add-user') :
        if($form['username'] === null) {
            $form = $form_default;
        } else {
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
                    $form = $form_default;
                    break;
            }
        }
        break;
    case('change-password'):
        $error = "Failed to update the password of '" . $form['username-change-password'] . "', ";

        switch($this->DBManager->update_password($form['username-change-password'], null, $form["change-password"], $form["conf-change-password"])) {
            case 'passwords-not-equal':
                $this->page_Logger->add_warning($error . "the passwords did not match.");
            break;
            case 'succes':
                $this->page_Logger->add_message("Password for '" . $form['username-change-password'] . "' succesfully updated, this user can now login with this new password.", "check");
                break;
        }
        break;
}
    ?>
    <div class="ui-body ui-body-a ui-corner-all">
    <h3>Add user</h3>
    <p><i>All fields are required</i></p>

    <form method="post">
        <input type="hidden" name="type" value="add-user">
        <div class="ui-field-contain">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php echo $form['username'];?>" required />
        </div>

        <div class="ui-field-contain">
            <label for="first_name">First name</label>
            <input type="text" name="first_name" value="<?php echo $form['first_name'];?>" required />
        </div>

        <div class="ui-field-contain">
            <label for="last_name">Last name</label>
            <input type="text" name="last_name" value="<?php echo $form['last_name'];?>" required />
        </div>

        <div class="ui-field-contain">
            <label for="username">Email</label>
            <input type="text" name="email" value="<?php echo $form['email'];?>" required />
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
            echo $this->Util->form->get_switch('admin', 'Admin', 'No', 'Yes', $form['admin']);
            echo $this->Util->form->get_switch('till_manager', 'Till Manager', 'No', 'Yes', $form['till_manager']);

            // Add the submit button.
            echo $this->Util->form->get_submit('Add user', false);
        ?>
    </form>
</div> <br>

<?php
    $this->page_Logger->show_html();
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
                    'priority' => '4',
                    'editable' => true
                ],
                [
                    'name' => 'till_manager',
                    'friendly-name' => 'Till manager',
                    'type' => 'boolean',
                    'true' => 'Yes',
                    'false' => 'No',
                    'priority' => '5',
                    'editable' => true
                ],
                [
                    'friendly-name' => 'Change password',
                    'username' => 'username', // The username field.
                    'type' => 'change-password',
                    'priority' => '7'
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
                            $strings = [];

                            // Check if a different string for true is set, else use default value.
                            if(isset($column['true'])) {
                                $strings['true'] = $column['true'];
                            } else {
                                $strings['true'] = "True";
                            }

                            // Check if a different string for false is set, else use default value.
                            if(isset($column['false'])) {
                                $strings['false'] = $column['false'];
                            } else {
                                $strings['false'] = "False";
                            }

                            $username = $user['username'];

                            // Check if it is editable and prevent altering admin and the users own information.
                            if(!$column['editable'] || $username === "admin" || $username === $user_data['username']) {
                                if($user[$column['name']]) {
                                    echo $strings['true'];
                                } else {
                                    echo $strings['false'];
                                }
                            } else {
                                $value  = $user[$column['name']] != 0 ? true : false;
                                echo $this->Util->form->get_switch($username, null, $strings['false'], $strings['true'], $value, $column['name'], true);
                            }
                            break;
                        case 'change-password':
                            $username = $user[$column['username']];

                            if($username == "admin" || $username == $user_data['username']) {
                                echo "<a href='" . $this->Util->get_url('account') . "' class='ui-btn ui-corner-all ui-shadow ui-mini ui-icon-alert ui-btn-icon-left' style='margin: 0'>Change your own password</a>";
                                break;
                            }

                            $id = "PR" . $username;

                            // Generate the popup of the password reset.
                            echo $this->Util->get_html_popup_button(
                                    "Change password", // Set the title of the popup.
                                    "<h3>Change password for '$username'</h3>\n" .
                                    "<form method='post' data-ajax='false'>\n" .
                                    "<input type='hidden' name='type' value='change-password'>\n" .
                                    "<input type='hidden' name='username-change-password' value='" . $username . "'>\n" .
                                    "<label for='password'>Password</label><input type='password' name='change-password' />\n" .
                                    "<label for='conf-password'>Confirm password</label><input type='password' name='conf-change-password' />\n" .
                                    $this->Util->form->get_submit('Change password', false) .
                                    "</form>\n",
                                    null, // Don't use an icon.
                                    $id, // Set the id of this popup.
                                    true // Use the mini version of the button.
                                    );
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

<?php
  echo $this->Util->Ajax->switch_js("admin");
  echo $this->Util->Ajax->switch_js("till_manager");
?>
