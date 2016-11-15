<?php
    $this->page_Logger = new Logger;

    // Check which form was submitted.
    switch($this->input->post('type')) {
        case 'change-password':
        $username = $user_data['username'];
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $new_password_conf = $this->input->post('new_password_confirm');

        if($current_password !== null) {
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

            // Renew the user data.
            $user_data = $this->DBManager->get_user_data($user_data['id']);
        break;
    }
?>
<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">    
    <div class="ui-body ui-body-a ui-corner-all">
        <style>
            .first-column-bold tr > td:first-child {
                font-weight:bold;
                padding-right: 2em;
            }
        </style>

        <h3><?php _("My account"); ?></h3>
        <table class="first-column-bold">
            <tr><!-- Username -->
                <td><?php _e("Username"); ?></td>
                <td><?php echo $user_data['username']; ?></td>
            </tr>

            <tr><!-- First name -->
                <td><?php _e("First name"); ?></td>
                <td><?php echo $user_data['first_name'];?></td>
            </tr>

            <tr><!-- Last name -->
                <td><?php _e("Last name"); ?></td>
                <td><?php echo $user_data['last_name'];?></td>
            </tr>

            <tr><!-- Role(s) -->
                <?php
                    $n = 1;
                    if($user_data['till_manager']) {
                        $n++;
                    }
                    
                    if($user_data['admin']) {
                        $n++;
                    }
                ?>
                <td><?php echo _n("Role", "Roles", $n); ?></td>
                <td><?php _e("User");
                          echo $user_data['till_manager'] ? ", " . _("Till manager") : "";
                          echo $user_data['admin'] ? ", " . _("Admin") : ""; ?>
                </td>
            </tr>

            <tr><!-- Pin -->
                <td><?php _e("Pin"); ?></td>
                <td>
                    <?php
                    echo $this->Util->get_html_popup_button(_("Show"), $user_data['pin']);
                    echo $this->Util->get_html_popup_button(_("Reset pin"),
                            "<form method='post' data-ajax='false'>
                            <input type='hidden' name='type' value='reset-pin'>
                            <p>" . _("Are you sure you want to reset your pin code?") . "</p>" .
                            $this->Util->form->get_submit(_("Reset pin")) .
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
        <h3><?php _e("Change password"); ?></h3>

        <form method="post">
            <input type='hidden' name='type' value='change-password'>
            <div class="ui-field-contain">
                <label for="current_password"><?php _e("Current password"); ?></label>
                <input type="password" name="current_password" required />
            </div>

            <div class="ui-field-contain">
                <label for="new_password"><?php _e("New password"); ?></label>
                <input type="password" name="new_password" required />
            </div>

            <div class="ui-field-contain">
                <label for="new_password_confirm"><?php _e("Confirm new password"); ?></label>
                <input type="password" name="new_password_confirm" required />
            </div>

            <?php echo $this->Util->form->get_submit(_("Change password")); ?>
        </form>
    </div>

<?php
$this->page_Logger->show_html();
?>
</div><!-- main -->
