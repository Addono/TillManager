<?php
function get_sum($posts) {
  $sum = 0;

  foreach($posts as $post) {
    $sum += $post['amount'];
  }

  if($sum % 1 == 0) {
    return $sum . ".00";
  } elseif($sum % .1 == 0) {
    return $sum . "0";
  } else {
    return $sum;
  }
}

$posts = $this->DBManager->get_posts();

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
<div class="ui-body ui-body-a ui-corner-all">
<h3>Balance</h3>
  <table class="balance-table">
    <thead>
      <tr>
        <th colspan="2">Debit</th>
        <th colspan="2">Credit</th>
      </tr>
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
</div>
