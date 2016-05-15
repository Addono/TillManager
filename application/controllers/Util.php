<?php
class Util {
    public $resources;
    public $form;
    
    private $ci;
    
    function __construct($ci) {
        $this->ci = $ci;
        $this->ci->load->helper('url'); // Load required packages.
        
        $this->resources = new resources(base_url() . "application/resources");
        $this->form = new Form($ci);
    }
    
    public function get_url($page) {
        return base_url() . "index.php/" . $page;
    }
    
    public function get_html_tooltip($message) {
        $id = "tooltip-" . rand(0, 99999999999);
        
        return "<a href='#$id' data-rel='popup' data-transition='pop' style='border: 0; background: none;' class='ui-btn ui-alt-icon ui-btn-inline ui-nodisc-icon ui-icon-info ui-btn-icon-notext'>Tooltip</a>"
        . "\n<div style='max-width:30em' data-role='popup' id='$id' class='ui-content'><p>$message</p></div>";
    }
    
    public function get_html_popup_button($button, $popup, $icon = null, $id = null, $mini = false) {
        $html = "";
        $class = "";
        $style = "";
        
        if($id == null) {
            $id = "popup-" . rand(0, 99999999999);
        }
        
        if($mini) {
            $class .= " ui-mini";
            $style .= "margin: 0"; 
        }
        
        if($icon == null) {
            $html .= "<a href='#$id' data-rel='popup' data-transition='pop' class='ui-btn ui-corner-all $class' style='$style'>$button</a>\n";
        } else {
            $html .= "<a href='#$id' data-rel='popup' data-transition='pop' class='ui-btn ui-corner-all ui-icon-$icon ui-btn-icon-left $class' style='$style'>$button</a>\n";
        }
        
        $html .= "<div style='max-width:30em' data-role='popup' id='$id' class='ui-content ui-corner-all'>\n"
                . "<a href='#' data-rel='back' class='ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right'>Close</a>\n" // Add the close button
                . "$popup\n"
                . "</div>\n";
        
        return $html;
    }
    
    public function get_html_not_admin() {
        return '<div class="ui-body ui-body-a ui-corner-all">'
        . '<h3>Get out, admin only here!</h3>'
        . '<p>You need to be an admin to be allowed to acces this page. Click the button to go back to safety. <br><sub>Don\'t worry, you are not in danger - at least not that I\'m aware of - but really, you should go ... and be carefull out there!</sub></p>'
        . '<a href="' . base_url() . 'index.php/home" class="ui-btn ui-btn-inline">Home</a>'
        . '</div>';
    }
}

class resources {
    private $location;
    
    function __construct($location) {
        $this->location = $location;
    }
    
    public function add($packages) {
        // If an array of packages is parsed, load them one by one.
        if(is_array($packages)) {
            foreach($packages as $package) {
                $this->add($package);
            }
        } else {
            echo $this->get_html($packages) . "\n";
        }
    }
    
    private function get_html($package) {
        switch(strtolower($package)) {
            case 'jquery':
                return "<script src='$this->location/jquery-ui/external/jquery/jquery.js' type='text/javascript'></script>";
            case 'jquery-ui':
                return "<link href='$this->location/jquery-ui/jquery-ui.min.css' rel='stylesheet'>\n" . 
                    "<script src='$this->location/jquery-ui/jquery-ui.min.js' type='text/javascript'></script>";
            case 'jquery-mobile':
                return "<link href='$this->location/jquery-mobile/jquery.mobile-1.4.5.css' rel='stylesheet'>\n" . 
                    "<script src='$this->location/jquery-mobile/jquery.mobile-1.4.5.min.js' type='text/javascript'></script>";
        }
    }
}

class Form {
    private $ci;
    
    function __construct($ci) {
        $this->ci = $ci;
    }
    
    /**
     * Generates the HTML a form switch
     * @param type $name
     * @param type $label
     * @param type $option_left
     * @param type $option_right
     * @param type $default_pos
     * @return string The HTML for the switch form element.
     */
    public function get_switch($name, $label = null, $option_left = "Off", $option_right = "On", $default_pos = false) {
        $html = "<div class='ui-field-contain'>\n";
        
        if($label != null && $label != "") {
            $html .= '<label for="flip-select-second">' . $label . '</label>';
        }
        
        $html .= '<select id="flip-select-second" name="' . $name . '" data-role="flipswitch">'
                . '<option>' . $option_left . '</option>';
        
        // Set the default position.
        if($default_pos) {
            $html .= '<option selected="">' . $option_right . '</option>';
        } else {
            $html .= '<option>' . $option_right . '</option>';
        } 
            
        $html .= "</select>\n";
        
        return $html . "</div>\n";
    }
    
    public function get_submit($title = "Submit", $inline = false) {
        return '<button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all' . ($inline ? ' ui-btn-inline' : '') . '" name="submit" value="submit">' . $title . '</button>';
    }
}