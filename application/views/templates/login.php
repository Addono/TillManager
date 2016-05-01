    <?php
    echo validation_errors();
    
    echo form_open('login');
    ?>
        <div class="ui-field-contain">
            <label for="username" >Username</label>
            <input type="text" name="username" placeholder="Username"/>
        </div>

        <div class="ui-field-contain">
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password"/>
        </div>

        <button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all" name="submit" value="submit">Login</button>
    </form>