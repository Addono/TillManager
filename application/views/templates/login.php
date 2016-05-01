<html>
    <?php
    $this->load->helper('form');
    echo form_open('login');
    ?>

        <label for="username">Username</label>
        <input type="input" name="username" /><br />

        <label for="password">Password</label>
        <input type="password" name="password" /><br />

        <input type="submit" name="submit" />
    </form>
</html>