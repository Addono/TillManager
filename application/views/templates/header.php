<?php
$navigation_pages = [
    [
        "location" => "home",
        "title" => "Home",
        "admin" => false
    ],
    [
        "location" => "account",
        "title" => "Account details",
        "admin" => false
    ],
    [
        "location" => "manage_users",
        "title" => "Manage users",
        "admin" => true
    ]
];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>
            <?php echo $name . str_replace("_", " ", ucfirst($title)); ?>
        </title>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1">
        <?php
        // Add jQuery, jQuery UI, jQuery Mobile js and css.
        $this->Util->resources->add(['jquery', 'jquery-ui' ,'jquery-mobile', 'css']);

        // Check if the page should redirect.
        if ($redirect != null) {
            echo "<script type='text/javascript'> window.location.href = '" . base_url() . "index.php/$redirect/'</script>";
        }
        ?>
    </head>
    <body>
        <?php
        // Build the navigation list.
        if($navigation) { ?>
        <div data-role="page" class="ui-responsive-panel" data-position-fixed="true">
            <div data-role="panel" data-position="left" data-display="overlay" data-theme="a" id="nav-panel">
                <ul data-role="listview">
                    <li data-icon="delete"><a href="#" data-rel="close">Close this panel</a></li>
                    <?php
                        foreach($navigation_pages as $page) {
                            if($page['admin'] && !$user_data['admin']) {
                                continue;
                            }

                            if(!isset($page['location'])) {
                                echo "<li>" . $page['title'] . "</li>";
                            } else {
                                if($page['location'] == $title) {
                                    echo "<li data-theme='b'>" . $page['title'] . "</li>\n";
                                } else {
                                    echo "<li><a href='" . base_url() . "index.php/" . $page['location'] . "' data-url='" . base_url() . "index.php/" . $page['location'] . "'>" . $page['title'] . "</a></li>\n";
                                }
                            }
                        }
                    ?>
                </ul>
            </div><!-- nav-panel -->
        <?php } ?>

            <div data-role="header" class="ui-header" id="header">
                <?php if($navigation) {?><a href="#nav-panel" data-role="button" role="button" class="jqm-navmenu-link ui-nodisc-icon ui-alt-icon ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext" id="nav-button">Panel</a> <?php } ?>
                <h1><?php echo $name .  str_replace("_", " ", ucfirst($title)); ?></h1>
                <?php
                // Show the logout button if the user is logged in.
                if($logged_in) {
                    echo "<a data-ajax='false' data-method='delete' rel='nofollow' href='" . base_url() . "index.php/logout' data-role='button' class='ui-btn ui-icon-power ui-btn-icon-right'>Logout</a>";
                } ?>
            </div><!-- header -->

            <div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
