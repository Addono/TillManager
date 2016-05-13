<?php
if($user_data['admin'] != 1) {
    echo $this->Util->get_html_not_admin();
} else {
    echo "<p>There is still nothing here.</p>";
}