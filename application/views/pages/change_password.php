<?php
    $this->page_Logger = new Logger;
    
    $username = $user_data['username'];
    $current_password = $this->input->post('current_password');
    $new_password = $this->input->post('new_password');
    $new_password_conf = $this->input->post('new_password_confirm');
    
    if($current_password != null) {
        if($new_password !== $new_password_conf) {
            $this->page_Logger->add_message("New passwords do not match.", "alert");
        } else {
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
                default:
                    $this->page_Logger->add_error("Something went wrong");
                    break;
            }
        }
    }
?>

<div class="ui-body ui-body-a ui-corner-all">
    <h3>Change password</h3>
    
    <form method="post">
        <div class="ui-field-contain">
            <label for="current_password">Current password</label>
            <input type="password" name="current_password" required />
        </div>
        <br>

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