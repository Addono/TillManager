<?php
    $this->page_Logger = new Logger;
    
    // Check which form was submitted.
    switch($this->input->post('type')) {
        case 'change-password':
        $username = $user_data['username'];
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $new_password_conf = $this->input->post('new_password_confirm');

        if($current_password != null) {
            switch($this->DBManager->update_password($username, $current_password, $new_password)) {
                    case 'succes':
                        $this->page_Logger->add_message("Password succesfully updated.", "check");
                        break;
                    case 'username':
                        $this->page_Logger->add_error("Username not found, logout and try again. If the problem persists contact the system admin.");
                        break;
                    case 'password':
                        $this->page_Logger->add_message("The current password you entered is incorrect.", "alert");
                        break;
                    case 'passwords-not-equal':
                        $this->page_Logger->add_message("New passwords do not match.", "alert");
                    default:
                        $this->page_Logger->add_error("Something went wrong");
                        break;
            }
        }
        break;
        case 'reset-pin':
            // Try to reset the pin, and show a message according to the result.
            switch($this->DBManager->reset_pin($user_data['username'])) {
                case 'succes':
                    $this->page_Logger->add_message("Your pin was succesfully updated, to see your new pin click on the 'Show' button.", "check");
                    break;
                default:
                    $this->page_Logger->add_error("Something went wrong, please contact your system administrator.");
                    break;
            }
            
            $user_data = $this->DBManager->get_user_data($user_data['username']);
        break;
    }
?>

<div class="ui-body ui-body-a ui-corner-all">
    <style>
        .first-column-bold tr > td:first-child {
            font-weight:bold;
            padding-right: 2em;
        }
    </style>
    
    <h3>Account details</h3>
    <table class="first-column-bold">
        <tr><!-- Username -->
            <td>Username</td>
            <td><?php echo $user_data['username']; ?></td>
        </tr>
        
        <tr><!-- First name -->
            <td>First name</td>
            <td><?php echo $user_data['first_name'];?></td>
        </tr>
        
        <tr><!-- Last name -->
            <td>Last name</td>
            <td><?php echo $user_data['last_name'];?></td>
        </tr>
        
        <tr><!-- Role(s) -->
            <td><?php echo ($user_data['till_manager'] || $user_data['admin']) ? "Roles" : "Role";?></td>
            <td>User<?php echo $user_data['till_manager'] ? ", Till manager" : ""; echo $user_data['admin'] ? ", Admin" : ""; ?></td>
        </tr>
        
        <tr><!-- Pin -->
            <td>Pin</td>
            <td>
                <?php 
                echo $this->Util->get_html_popup_button('Show', $user_data['pin']);
                echo $this->Util->get_html_popup_button('Reset pin',
                        "<form method='post' data-ajax='false'>
                        <input type='hidden' name='type' value='reset-pin'>
                        <p>Are you sure you want to reset your pin code?</p>" .
                        $this->Util->form->get_submit('Reset pin') . 
                        "</form>",
                        'alert'
                        );
                ?>
            </td>
        </tr>
    </table>
</div>
<br>

<div class="ui-body ui-body-a ui-corner-all">
    <h3>Change password</h3>
    
    <form method="post">
        <input type='hidden' name='type' value='change-password'>
        <div class="ui-field-contain">
            <label for="current_password">Current password</label>
            <input type="password" name="current_password" required />
        </div>

        <div class="ui-field-contain">
            <label for="new_password">New password</label>
            <input type="password" name="new_password" required />
        </div>

        <div class="ui-field-contain">
            <label for="new_password_confirm">Confirm new password</label>
            <input type="password" name="new_password_confirm" required />
        </div>

        <button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all" name="submit" value="submit">Login</button>
    </form>
</div>

<?php
$this->page_Logger->show_html();