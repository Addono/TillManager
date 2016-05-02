<?php
$navigation_pages = [
    "Home" => "home",
    "About" => "about",
];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $name . $title; ?></title>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1">
        <?php $this->Util->resources->add(['jquery', 'jquery-ui' ,'jquery-mobile']); ?>
        <?php if($redirect != null) echo "<script type='text/javascript'> window.location.href = '" . base_url() . "index.php/$redirect/'</script>"; ?>
    </head>
    <body>
        <?php if($navigation) { ?>
        <div data-role="page" class="ui-responsive-panel">
            <div data-role="panel" data-position="left" data-display="overlay" data-theme="a" id="nav-panel">
                <ul data-role="listview">
                    <li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
                    <?php
                        foreach($navigation_pages as $page_name => $url) {
                            if($url == $title) {
                                echo "<li data-theme='b'>$page_name</li>\n";
                            } else {
                                echo "<li><a href='" . base_url() . "index.php/$url' data-url='" . base_url() . "index.php/$url'>$page_name</a></li>\n";
                            }
                        }
                    ?>
                </ul>
            </div><!-- nav-panel -->
        <?php } ?>
            
            <div data-role="header" class="ui-header" id="header">
                <?php if($navigation) {?><a href="#nav-panel" data-role="button" role="button" class="jqm-navmenu-link ui-nodisc-icon ui-alt-icon ui-btn-left ui-btn ui-icon-bars ui-btn-icon-notext" id="nav-button">Panel</a> <?php } ?>
                <h1><?php echo $name . ucfirst($title);?></h1>
                <?php if($logged_in) {echo "<a data-ajax='false' data-method='delete' rel='nofollow' href='" . base_url() . "index.php/logout' data-role='button' class='ui-btn ui-icon-power ui-btn-icon-right'>Logout</a>";} ?> 
            </div><!-- header -->
            
            <div data-role="main" class="ui-content jqm-content jqm-fullwidth" id="main">
            