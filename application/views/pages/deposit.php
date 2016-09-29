<?php
$all_users = $this->DBManager->get_all_user_data(null, null, false);

$till_managers = [];
$current_user_id = $this->DBManager->get_current_user();

foreach($all_users as $user) {
    if($user['till_manager']) {
        if($user['id'] == $current_user_id) {
            array_unshift($till_managers, $user);
        } else {
            $till_managers[] = $user;
        }
    }
}
?>

<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    <div class="ui-body ui-body-a ui-corner-all">
        <?php 
        echo form_open('process_deposit', 'data-ajax="false"');
        
        echo $this->Util->form->get_submit("Deposit");
        
        echo "<h3>Received from</h3>\n";
        echo $this->Util->form->get_radio_all_users($all_users, 'from', true, null, true);
        
        echo "<h3>Given to</h3>\n";
        echo $this->Util->form->get_radio_all_users($till_managers, 'to', true, $current_user_id, true);

        echo "<h3>Amount</h3>\n";
        echo "<input required placeholder='Amount in &euro;' type='number' min='0' step='0.01' name='amount' id='number-pattern'>\n";
        
        echo "</form>\n";
        ?>
    </div>
</div>