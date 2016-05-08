<?php
if($user_data['admin'] != 1) {
    echo $this->Util->get_html_not_admin();
} else {
    $this->page_Logger = new Logger;
    
    $user_added = false;
    
    $form['username'] = $this->input->post('username');
    $form['first_name'] = $this->input->post('first_name');
    $form['last_name'] = $this->input->post('last_name');
    $form['password'] = $this->input->post('password');
    $form['password_conf'] = $this->input->post('password_confirm');
    $form['admin'] = $this->input->post('admin');
    $form['till_manager'] = $this->input->post('till_manager');
    
    ?>
    <div class="ui-body ui-body-a ui-corner-all">
    <h3>Add user</h3>
    <p><i>All fields are required</i></p>
    
    <form method="post">
        <div class="ui-field-contain">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php if(!$user_added) echo $form['username'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="first_name">First name</label>
            <input type="text" name="first_name" value="<?php if(!$user_added) echo $form['first_name'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="last_name">Last name</label>
            <input type="text" name="last_name" value="<?php if(!$user_added) echo $form['last_name'];?>" required />
        </div>
        
        <div class="ui-field-contain">
            <label for="new_password">Password</label>
            <input type="password" name="password" required />
        </div>

        <div class="ui-field-contain">
            <label for="password_confirm">Confirm password</label>
            <input type="password" name="password_confirm" required />
        </div>

        <?php
        
            // Add the two switches for the user rights.
            echo $this->Util->form->get_switch('admin', 'Admin', 'No', 'Yes', !$user_added ? !$form['admin'] : true);  
            echo $this->Util->form->get_switch('till_manager', 'Till Manager', 'No', 'Yes', !$user_added ? !$form['admin'] : true);
            
            // Add the submit button.
            echo $this->Util->form->get_submit('Add user', false);
        ?>
    </form>
</div>

<?php
    $this->page_Logger->show_html();
}