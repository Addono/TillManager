<div class="ui-body ui-body-a ui-corner-all">
    <h3><? echo _("Get out, admin only here!"); ?></h3>
    <p><? echo _("You need to be logged  in as an administrator to be allowed to access this page. Click the 'Home' button to go back to safety."); ?><br></p>
    <p><? echo _("If you think that you should be able to access this page, then please contact your system administrator for help."); ?></p>
    <a href="<? echo $this->Util->get_url("home"); ?>" class="ui-btn ui-btn-inline"><? echo _("Home"); ?></a>
    <sub><? echo _("Don't worry, you are not in danger <small> - at least not that I'm currently aware of</small>. But really, you should go now, this page is not that interesting and you just have to click that button."); ?></sub>
</div>