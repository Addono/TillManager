<?php
$this->page_Logger = new Logger;

// Get all post variables into one array.
$form['username'] = $this->input->post('username');
$form['first_name'] = $this->input->post('first_name');
$form['prefix_name'] = $this->input->post('prefix_name');
$form['last_name'] = $this->input->post('last_name');
$form['email']  = $this->input->post('email');
$form['password'] = $this->input->post('password');
$form['password_conf'] = $this->input->post('password_confirm');
$form['username-change-password'] = $this->input->post('username-change-password');
$form['change-password'] = $this->input->post('change-password');
$form['conf-change-password'] = $this->input->post('conf-change-password');

// The default values of all form fields.
$form_default = [
            'username' => '',
            'first_name' => '',
            'prefix_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_conf' => '',
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
            switch($this->DBManager->add_user($form['username'], $form['first_name'], $form['prefix_name'], $form['last_name'], $form['password'], false, false, $form['password_conf'], $form['email'])) {
                case 'username':
                    $this->page_Logger->add_warning(_("Failed to add user, username should not be empty. Please fill the username field and try again."));
                    break;
                case 'username-exists':
                    $this->page_Logger->add_warning(_("Failed to add user, username is already in use. Try again with a different username."));
                    break;
                case 'email-empty':
                    $this->page_Logger->add_warning(_("Failed to add user, email address should not be empty."));
                    break;
                case 'email-invalid':
                    $this->page_Logger->add_warning(_("Failed to add user, email address has an invalid syntax."));
                    break;
                case 'password':
                    $this->page_Logger->add_warning(_("Failed to add user, password should not be empty."));
                    break;
                case 'password-conf':
                    $this->page_Logger->add_warning(_("Failed to add user, passwords did not match. Make sure that they are equal."));
                    break;
                default:
                    $this->page_Logger->add_message(printf(_("User '%s' added succesfully."), $form['username']), "check");
                    $form = $form_default;
                    break;
            }
        }
        break;
    case('change-password'):
        $error = 
        
        $current_user = $this->DBManager->get_user_data($this->DBManager->get_current_user());

        // Prevent other users than the 'admin' user to change the password of 'admin'.
        if($form['username-change-password'] == "admin" && $current_user['username'] == "admin") {
            $this->page_Logger->add_warning(_("Only 'admin' is allowed to change its own password"));
        } else {
            switch($this->DBManager->update_password($form['username-change-password'], null, $form["change-password"], $form["conf-change-password"])) {
                case 'passwords-not-equal':
                    $error = printf(_("Failed to update the password of '%s', the passwords did not match."), $form['username-change-password']);
                    $this->page_Logger->add_warning($error);
                break;
                case 'succes':
                    $message = printf(_("Password for '%s' succesfully updated, this user can now login with this new password."), $form['username-change-password']);
                    $this->page_Logger->add_message($message, "check");
                    break;
            }
        }
        break;
}
?>
<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    <div class="ui-body ui-body-a ui-corner-all">
        <h3><?php _e("Add user"); ?></h3>
        <p><i><?php _e('All fields marked with an "*" are required.'); ?></i></p>

        <form method="post">
            <input type="hidden" name="type" value="add-user">
            <div class="ui-field-contain">
                <label for="username"><?php _e("Username"); ?>*</label>
                <input type="text" name="username" value="<?php echo $form['username'];?>" required />
            </div>

            <div class="ui-field-contain">
                <label for="first_name"><?php _e("First name"); ?>*</label>
                <input type="text" name="first_name" value="<?php echo $form['first_name'];?>" required />
            </div>
            
            <div class="ui-field-contain">
                <label for="last_name"><?php _e("Surname prefix"); ?></label>
                <input type="text" name="prefix_name" value="<?php echo $form['prefix_name'];?>"/>
            </div>

            <div class="ui-field-contain">
                <label for="last_name"><?php _e("Last name"); ?>*</label>
                <input type="text" name="last_name" value="<?php echo $form['last_name'];?>" required />
            </div>

            <div class="ui-field-contain">
                <label for="username"><?php _e("Email"); ?>*</label>
                <input type="text" name="email" value="<?php echo $form['email'];?>" required />
            </div>

            <div class="ui-field-contain">
                <label for="new_password"><?php _e("Password"); ?>*</label>
                <input type="password" name="password" required />
            </div>

            <div class="ui-field-contain">
                <label for="password_confirm"><?php _e("Confirm password"); ?>*</label>
                <input type="password" name="password_confirm" required />
            </div>

            <?php
                // Add the submit button.
                echo $this->Util->form->get_submit(_("Add user"), false);
            ?>
        </form>
    </div> <br>

    <?php
        $this->page_Logger->show_html();
    ?>

    <div class="ui-body ui-body-a ui-corner-all">
        <h3><?php _e("User information"); ?></h3>
        <table data-role="table" data-mode="reflow" class="ui-responsive striped">
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
                        'friendly-name' => _('Username'),
                        'type' => 'string',
                        'priority' => '1'
                    ],
                    [
                        'name' => 'first_name',
                        'friendly-name' => _('First name'),
                        'type' => 'string',
                        'priority' => '2'

                    ],
                                        [
                        'name' => 'prefix_name',
                        'friendly-name' => _('Prefix'),
                        'type' => 'string',
                        'priority' => '3'
                    ],
                    [
                        'name' => 'last_name',
                        'friendly-name' => _('Last name'),
                        'type' => 'string',
                        'priority' => '3'
                    ],
                    [
                        'name' => 'email',
                        'friendly-name' => _('Email'),
                        'type' => 'string',
                        'priority' => '3'
                    ],
                    [
                        'name' => 'admin',
                        'friendly-name' => _('Admin'),
                        'type' => 'boolean',
                        'true' => 'Yes',
                        'false' => 'No',
                        'priority' => '4',
                        'editable' => true
                    ],
                    [
                        'name' => 'till_manager',
                        'friendly-name' => _('Till manager'),
                        'type' => 'boolean',
                        'true' => 'Yes',
                        'false' => 'No',
                        'priority' => '5',
                        'editable' => true
                    ],
                    [
                        'friendly-name' => _('Change password'),
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
                                if(!$column['editable'] || $username === "admin" || $username === "local" || $username === $user_data['username']) {
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

                                if($username == $user_data['username']) {
                                    echo "<a href='" . $this->Util->get_url('account') . "' class='ui-btn ui-corner-all ui-shadow ui-mini ui-icon-alert ui-btn-icon-left' style='margin: 0'>"
                                            . _("Change your own password") 
                                            . "</a>";
                                    break;
                                }
                                
                                if($username == 'admin') {
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
                                        $this->Util->form->get_submit(_('Change password'), false) .
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
</div><!-- page -->