<div class="ui-body ui-body-a ui-corner-all">
    <h3><? echo _("Login"); ?></h3>
    <?
    echo validation_errors();
    
    echo form_open('login', 'data-ajax="false"');
    ?>
        <div class="ui-field-contain">
            <label for="username" ><? echo _("Username"); ?></label>
            <input required type="text" name="username" placeholder="Username" value="<? echo $this->input->post('username'); ?>"/>
        </div>

        <div class="ui-field-contain">
            <label for="password"><? echo _("Password"); ?></label>
            <input required type="password" name="password" placeholder="Password"/>
        </div>

        <button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all" name="submit" value="submit"><? echo _("Login"); ?></button>
    </form>
</div>