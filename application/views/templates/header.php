<?php
$navigation_pages = [
    "Home" => "home",
    "About" => "about",
];
?>
<html>
    <head>
        <title><?php echo $name . $title; ?></title>
        <link href="<?php echo $resources;?>/jquery-ui/jquery-ui.min.css" rel="stylesheet">
        <link href="<?php echo $resources;?>/jquery-mobile/jquery.mobile-1.4.5.css" rel="stylesheet">
    </head>
    <body>
        <div data-role="page" class="ui-responsive-panel">
            <div data-role="panel" data-position="left" data-display="overlay" data-theme="a" id="nav-panel">
                <ul data-role="listview">
                    <li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
                    <?php
                        foreach($navigation_pages as $page_name => $url) {
                            if($url == $title) {
                                echo "<li data-theme='b'>$page_name</li>\n";
                            } else {
                                echo "<li><a href='" . base_url() . "index.php/$url'>$page_name</a></li>\n";
                            }
                        }
                    ?>
                </ul>
            </div>
            
            <div data-role="header" class="ui-header" id="header">
                <a href="#nav-panel" data-role="button" role="button" class="jqm-navmenu-link ui-nodisc-icon ui-alt-icon ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext" id="nav-button">Panel</a>
                <h1><?php echo $name . ucfirst($title);?></h1>
            </div>
            <div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
            