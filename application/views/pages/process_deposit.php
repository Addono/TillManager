<?php
$result;
// @TODO: Add server sided form validation.

$from = $this->input->post('from');
$to = $this->input->post('to');

// Convert the amount to a float with two decimals.
$amount_str = number_format($this->input->post('amount'), 2);
$amount = floatval($amount_str);

if($from === null) {
    $result = "Go to the deposit page to create a new deposit.";
} elseif(!$this->DBManager->check_user_id_exists($from)) {
    $result = "Deposit failed: Invalid user selected";
} elseif(!$this->DBManager->check_user_rights($to)) {
    $result = "Deposit failed: Receiving user should be a till manager.";
} elseif($amount <= 0) {
    $result = "Deposit failed: Amount should be positive.";
} else {
    // Get the author (current user).
    $author = $this->DBManager->get_current_user();

    // Create the deposit in the database.
    $transaction = $this->DBManager->create_deposit($from, $to, $amount, $author);
    
    if(is_numeric($transaction)) {
        $from_user = $this->DBManager->get_user_data($from);
        $to_user = $this->DBManager->get_user_data($to);
        
        $from_full_name = $this->Util->combine_name($from_user);
        $to_full_name = $this->Util->combine_name($to_user);
        
        $result = "Deposit of &euro;$amount_str from '$from_full_name' to '$to_full_name' succesfull (transaction #$transaction).";
    } else {
        $result = "Deposit failed: reason '$transaction'";
    }
}
?>

<script type="text/JavaScript">
    var url = "<?php echo  $this->Util->get_url("deposit"); ?>";
    var timeout = setTimeout("location.href = '" + url + "';", 10000);
     
    $(document).ready(function(){
        $('#cancel').click(function() {
            clearTimeout(timeout);
            $('#returnMessage').fadeOut();
            $(this).replaceWith("<a id='back' href='" + url + "'><button>New deposit</button</a>");
            $('#back').enhanceWithin();
        });
    });
 </script>

<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    <div class="ui-body ui-body-a ui-corner-all">
        <h3>Process deposit</h3>
        <p><?php echo $result; ?></p>
        
        <p>
            <i id="returnMessage">You will be automatically returned after 10 seconds. Click "Cancel" to prevent this from happening.</i>
            <button id="cancel">Cancel</button>
        </p>
    </div>
</div>
    