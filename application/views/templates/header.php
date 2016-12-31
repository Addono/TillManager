<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>
            <? echo $name . str_replace("_", " ", ucfirst($title)) . "\n"; ?>
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?
        // Add jQuery, jQuery UI, jQuery Mobile js and css.
        $this->Util->resources->add(['jquery', 'jquery-mobile', 'jqm-spinbox', 'css']);

        // Check if the page should redirect.
        if ($redirect !== null) {
            echo "<meta http-equiv='refresh' content='" . $redirect['time'] . "; url=" . $this->Util->get_url($redirect['target']) . "/' />\n";
        }
        ?>
        
    </head>
    <body>
        <?
        // Build the navigation list.
        if($navigation) { ?>
        <div data-role="page" class="ui-responsive-panel" data-position-fixed="true">
            <div data-role="panel" data-position="left" data-display="overlay" data-theme="a" id="nav-panel">
                <ul data-role="listview">
                    <!-- <li data-icon="delete"><a href="#" data-rel="close">Close this panel</a></li> -->
                    <?
                        $current_page;
                        
                        foreach($navigation_pages as $page) {
                            // Check if the currently evaluated page is the current page.
                            if(isset($page['location']) && $page['location'] == $title) {
                                $current_page = $page;
                            }
                            
                            // Check if this page should not be included in the navigation.
                            if(($page['admin'] && !$user_data['admin']) || 
                                    ($page['tillmanager'] && !$user_data['till_manager']) || 
                                    (isset($page['hidden']) && $page['hidden'])) {
                                continue;
                            }
                            
                            // Draw its entry in the navigation.
                            if(!isset($page['location'])) {
                                echo "<li>" . $page['title'] . "</li>";
                            } elseif($page['location'] == $title) {
                                echo "<li data-theme='b'>" . $page['title'] . "</li>\n";
                            } else {
                                echo "<li><a href='" . $this->Util->get_url($page['location']) . "' data-url='" . $this->Util->get_url($page['location']) . "'>" . $page['title'] . "</a></li>\n";
                            }
                        }
                    ?>
                </ul>
            </div><!-- nav-panel -->
        <?
        } else {
            $current_page['title'] = _("Login");
        }
        ?>

            <div data-role="header" class="ui-header" id="header">
                <? if($navigation) {?><a href="#nav-panel" data-role="button" role="button" class="jqm-navmenu-link ui-nodisc-icon ui-alt-icon ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext" id="nav-button">Panel</a> <? } ?>
                <h1><? echo $name .  $current_page['title']; ?></h1>
                <?
                // Show the logout button if the user is logged in.
                if($logged_in) {
                    echo "<a data-ajax='false' data-method='delete' rel='nofollow' href='" . $this->Util->get_url('logout') . "' data-role='button' class='ui-btn ui-icon-power ui-btn-icon-right'>" . _("Logout") . "</a>";
                } ?>
            </div><!-- header -->
