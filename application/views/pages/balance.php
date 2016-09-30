<?php
function get_sum($posts) {
  $sum = 0;

  foreach($posts as $post) {
    // Only sum the parent values.
    if($post["parent"] == null || $post["parent"] == 0) {
      $sum += $post['amount'];
    }
  }

  return number_format($sum, 2);
}

$posts = $this->DBManager->get_posts(true);

$debit = [];
$credit = [];

foreach($posts as $post) {
  if($post['cd'] == "debit") {
    $debit[] = $post;
  } else {
    $credit[] = $post;
  }
}
 ?>
<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">    
    <div class="ui-body ui-body-a ui-corner-all">
        <h3>Balance</h3>
          <table class="balance-table">
            <thead>
                <th colspan="2">Debit</th>
                <th colspan="2">Credit</th>
            </thead>
            <tbody>
        <?php for($i = 0, $max = max(count($debit), count($credit)); $i < $max; $i++) { ?>
              <tr>
                <td><?php echo isset($debit[$i]) ? $debit[$i]["name"] : "";?></td>
                <td><?php echo isset($debit[$i]) ? "&euro;" . $debit[$i]["amount"] : "";?></td>
                <td><?php echo isset($credit[$i]) ? $credit[$i]["name"] : "";?></td>
                <td><?php echo isset($credit[$i]) ? "&euro;" . $credit[$i]["amount"] : "";?></td>
              </tr>
        <?php } ?>
              <tr>
                <td>Total</td>
                <td>&euro;<?php echo get_sum($debit);?></td>
                <td>Total</td>
                <td>&euro;<?php echo get_sum($credit);?></td>
              </tr>
            </tbody>
          </table>
    </div> <br>
    
    <?php
    
    $all_users = $this->DBManager->get_all_user_data();
    
    ?>
    
    <div class="ui-body ui-body-a ui-corner-all">
        
        <table>
            <thead>
                <th>Name</th>
                <th>Till debit
                    <?php echo $this->Util->get_html_tooltip("The amount of till money a user has in his possession."); ?>
                </th>
                <th>Till credit
                    <?php echo $this->Util->get_html_tooltip("The amount each user can still can spend."); ?>
                </th>
            </thead>
            <tbody>
            <?php
                foreach($all_users as $user) {
                    // Skip the admin and local account.
                    if($user['username'] == "admin" || $user['username'] == "local") {
                        continue;
                    }
                    
                    // Get all the data for this user.
                    $name = $this->Util->combine_name($user);
                    $debit = $this->DBManager->get_post($user['debit_post_id']);
                    $credit = $this->DBManager->get_post($user['credit_post_id']);
                    ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td>&euro;<?php echo $debit->amount; ?></td>
                    <td>&euro;<?php echo $credit->amount; ?></td>
                </tr>
                <?php
                }
            ?>
            </tbody>
        </table>
    </div> <br>
</div><!-- main -->