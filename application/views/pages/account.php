<div class="ui-body ui-body-a ui-corner-all">
    <style>
        .first-column-bold tr > td:first-child {
            font-weight:bold;
            padding-right: 2em;
        }
    </style>
    
    <h3>Account details</h3>
    <table class="first-column-bold">
        <tr><!-- Username -->
            <td>Username</td>
            <td><?php echo $user_data['username']; ?></td>
        </tr>
        
        <tr><!-- First name -->
            <td>First name</td>
            <td><?php echo $user_data['first_name'];?></td>
        </tr>
        
        <tr><!-- Last name -->
            <td>Last name</td>
            <td><?php echo $user_data['last_name'];?></td>
        </tr>
        
        <tr><!-- Pin -->
            <td>Pin</td>
            <td><?php echo $user_data['pin'];?></td>
        </tr>
        
        <tr><!-- Password -->
            <td>Password</td>
            <td><a href="<?php echo base_url(); ?>index.php/change_password">Change password</a></td>
        </tr>
        
        <tr><!-- Role(s) -->
            <td><?php echo ($user_data['till_manager'] || $user_data['admin']) ? "Roles" : "Role";?></td>
            <td>User<?php echo $user_data['till_manager'] ? ", Till manager" : ""; echo $user_data['admin'] ? ", Admin" : ""; ?></td>
        </tr>
    </table>
</div>