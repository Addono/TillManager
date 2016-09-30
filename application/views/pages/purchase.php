<?php
// Get the price of the product.
$price = $this->DBManager->get_price(1);
?>

<div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
    Price per consumption: <?php echo $this->Util->price_to_string($price); ?>
    <div class="ui-body ui-body-a ui-corner-all">
        <?php echo form_open('process_purchase', 'data-ajax="false"'); ?>
            <button type="submit">Purchase</button>
            <table data-role="table" data-filter="true" data-mode="reflow" class="ui-responsive striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount of consumptions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $users = $this->DBManager->get_all_user_data();

                // Display all users.
                foreach($users as $user) {
                    // Hide the admin and local user.
                    if($user['username'] != 'admin' && $user['username'] != 'local') { ?>
                        <tr>
                            <td>
                                <?php echo $this->Util->combine_name($user); ?>
                            </td>
                            <td>
                                <?php echo $this->Util->form->get_horizontal_spinbox($user['id'], 0); ?>
                            </td>
                            </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
</div>