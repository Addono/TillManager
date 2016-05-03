    <?php
    echo validation_errors();
    
    echo form_open('login', 'data-ajax="false"');
    ?>
        <div class="ui-field-contain">
            <label for="username" >Username</label>
            <input required type="text" name="username" placeholder="Username" value="<?php echo $this->input->post('username'); ?>"/>
        </div>

        <div class="ui-field-contain">
            <label for="password">Password</label>
            <input required type="password" name="password" placeholder="Password"/>
        </div>

        <button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all" name="submit" value="submit">Login</button>
    </form>