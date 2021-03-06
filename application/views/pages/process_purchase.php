<?
/**
 * @TODO Add rights management for purchases.
 * @TODO Add server sided form validation.
 */
$purchases = [];

$users = $this->DBManager->get_all_user_data();

foreach($users as $user) {
    $amount = $this->input->post($user["id"]);
    
    if($amount > 0) {
        $purchases[$user["last_name"]] = [
            "amount" => $amount,
            "user" => $user
        ];
    }
}

?>

<script type="text/JavaScript">
    var url = "<? echo  $this->Util->get_url("purchase"); ?>";
    var timeout = setTimeout("location.href = '" + url + "';", 10000);
     
    $(document).ready(function(){
        $('#cancel').click(function() {
            clearTimeout(timeout);
            $('#returnMessage').fadeOut();
            $(this).replaceWith("<a id='back' href='" + url + "'><button>New purchase</button</a>");
            $('#back').enhanceWithin();
        });
    });
 </script>

<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    <div class="ui-body ui-body-a ui-corner-all">
        <h3>Process purchase</h3>
            <?
            if(count($purchases) == 0) {
                echo "<p>No purchases selected</p>";
            } else {
                $curr_user_id   = $this->DBManager->get_current_user();
                $curr_user      = $this->DBManager->get_user_data($curr_user_id);
                
                foreach($purchases as $purchase) {
                    $author = $this->DBManager->get_user_data($purchase["user"]["id"]);
                    
                    echo $this->Util->combine_name($purchase["user"]) . ": <b>" . $purchase["amount"] . "</b> consumption(s) - ";
                    
                    $result = $this->DBManager->create_purchase($author["id"], $purchase["amount"], $curr_user_id);
                    
                    if(is_int($result)) {
                        echo "Success (Transaction id #$result)";
                    } else {
                        echo "Failed because '$result'";
                    }
                    
                    echo "<p>";
                }
                
                echo "<p>Signed by " . $this->Util->combine_name($curr_user) . "</p>";
            }
            ?>
        
        <p>
            <i id="returnMessage">You will be automatically returned after 10 seconds. Click "Cancel" to prevent this from happening.</i>
            <button id="cancel">Cancel</button>
        </p>
    </div>
</div><!-- main -->
