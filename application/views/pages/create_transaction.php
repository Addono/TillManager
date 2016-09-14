<?php
    $price = .70;
?>

<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    <div class="ui-body ui-body-a ui-corner-all">
        <form>
            <table data-role="table" data-mode="reflow" class="ui-responsive">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Amount</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $users = $this->DBManager->get_all_user_data();

                // Display all users.
                foreach($users as $user) {
                    // Hide the admin and local user.
                    if($user['username'] != 'admin' || $user['username'] != 'local') {
                        
                    }
                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
</div>