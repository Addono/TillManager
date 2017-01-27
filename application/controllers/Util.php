<?
/**
 * A class containing multiple tools to handle generic and regularly used HTML.
 */
class Util {
    public $resources;
    public $form;

    private $ci;

    function __construct($ci) {
        $this->ci = $ci;
        $this->ci->load->helper('url'); // Load required packages.

        $this->resources = new resources(base_url() . "application/resources");
        $this->form = new Form($ci, $this);

        $this->Ajax = new Ajax($this);
    }

    /**
     * Returns the full url of a page.
     * @param string The name of the page.
     * @return string The url of the page.
     */
    public function get_url($page) {
        return base_url() . "index.php/" . $page;
    }

    /**
     * Returns the HTML of an tooltip with an specified message.
     * @param string The message of the tooltip
     * @return string An tooltip as HTML.
     */
    public function get_html_tooltip($message) {
        $id = "tooltip-" . rand(0, 99999999999);

        return "<a href='#$id' data-rel='popup' data-transition='pop' style='border: 0; background: none;' class='ui-btn ui-alt-icon ui-btn-inline ui-nodisc-icon ui-icon-info ui-btn-icon-notext'>Tooltip</a>"
        . "\n<div style='max-width:30em' data-role='popup' id='$id' class='ui-content'>$message</div>";
    }

    /**
     * Creates a popup and button, clicking the button will open the popup.
     * @param string The text on the button.
     * @param string The HTML the popup should contain.
     * @param string The name of the jQM icon placed at the button, or null if non should be used.
     * @param string The ID of the button, null if it should be generated.
     * @param boolean If jQM 'mini'-style should be used.
     * @return string The HTML of the button and popup.
     */
    public function get_html_popup_button($button, $popup, $icon = null, $id = null, $mini = false) {
        $html = "";
        $class = "";
        $style = "";

        if($id === null) {
            $id = "popup" . rand(0, 999999999999);
        }

        if($mini) {
            $class .= " ui-mini";
            $style .= "margin: 0";
        }

        if($icon === null) {
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
    
    /**
     * Concaterantes the real name of a user.
     * @param array The data of the user as an array.
     * @return string The real name of the parsed user.
     */
    function combine_name($user) {
        if($user['prefix_name'] !== "" && $user['prefix_name'] !== null) {
            return $user['first_name'] . " " . $user['prefix_name'] . " " . $user['last_name'];
        } else {
            return $user['first_name'] . " " . $user['last_name'];
        }
    }
    
    public function price_to_string($price) {
        return "€" . number_format((float) $price, 2);
    }
}

/**
 * Class for adding (external) JS and CSS resources to the page.
 */
class resources {
    private $location;

    function __construct($location) {
        $this->location = $location;
    }

    /**
     * Adds the HTML for one or multiple packages to the page.
     * @param mixed Either an string of the name of one package, or multiple names stored in an array.
     */
    public function add($packages) {
        // If an array of packages is parsed, load them one by one.
        if(is_array($packages)) {
            foreach($packages as $package) {
                $this->add($package);
            }
        } else {
            echo "\n";
            $this->get_html($packages);
        }
    }

    // Gets the html needed to return each of the packages.
    private function get_html($package) {
        switch(strtolower($package)) {
            case 'css':
                echo $this->css_resource("style.css");
                break;
            case 'jquery':
                echo $this->js_resource("jquery-ui/external/jquery/jquery.js");
                break;
            case 'jquery-ui':
                echo $this->css_resource("jquery-ui/jquery-ui.min.css");
                echo $this->js_resource("jquery-ui/jquery-ui.min.js");
                break;
            case 'jquery-mobile':
                echo $this->css_resource("jquery-mobile/jquery.mobile-1.4.5.css");
                echo $this->js_resource("jquery-mobile/jquery.mobile-1.4.5.min.js");
                break;
            case 'jqm-spinbox':
                echo $this->js_resource("jqm-spinbox.min.js");
                break;
            default: // Log an error if the package does not exist.
                error_log("Package  '$package' was not found, and could therefore not be added by the Resources class.");
                break;
        }
    }
    
    // @TODO: Chekc if the resource exists.
    private function js_resource($location) {
        return "<script src='$this->location/$location' type='text/javascript'></script>\n";
    }
    
    private function css_resource($location) {
        return "<link href='$this->location/$location' rel='stylesheet'>\n";
    }
}

class Ajax {
    private $Util;

    function __construct($util) {
        $this->Util = $util;
    }    
    
    public function switch_js($class) {
        echo
            "<script>
            $( document ).ready(function() {
                $('.ajax-$class').click(function() {
                    var select = $( this ).find('select');
                    var name = select.attr('name');
                    var firstVal = select.find(':nth-child(1)').attr('value');
                    var value;
                    
                    // Get the position of the switch and get the corresponding value attribute.
                    if(select.val() == firstVal) {
                        value = firstVal;
                    } else {
                        value = select.find(':nth-child(2)').attr('value');
                    }

                    $.post('" . $this->Util->get_url("ajax/$class-switch") . "', {name: name, value: value}, function(result) {
                        if(result != 'succes') {
                            switch(result) {
                                case 'failed: user not logged in':
                                    alert('" . _("Action failed because you are not logged in anymore, reload the page and login before proceding.") . "');
                                    break;
                                case 'failed: access denied':
                                    alert('" . _("Action failed, you do not have enough rights to do this.") . "');
                                    break;
                                default:
                                    alert('" . _("Something unexpected happened, the error was: ") . "\'' + result + \"'\");
                                    break;
                            }
                        }
                    });
                });
            });
            </script>\n";
    }
}

class Form {
    private $ci;
    private $Util;

    function __construct($ci, $util) {
        $this->ci = $ci;
        $this->Util = $util;
    }

    /**
     * Generates the HTML a form switch
     * @param string The name tag of the switch.
     * @param string The label in front of the switch.
     * @param string The left - false - value and name of the switch.
     * @param string The right - true - value and name of the switch.
     * @param mixed  Can be either an boolean or a string deciding if the right or left value should be default. Parsing true (boolean) or the right option (string) will make the right position default.
     * @param mixed  Either false if it shouldn't support Ajax, or the Ajax class which the switch should have.
     * @param boolean If the switch should be in compact mode.
     * @return string The HTML for the switch form element.
     */
    public function get_switch($name, $label = null, $option_left = "Off", $option_right = "On", $default_pos = false, $ajax = false, $compact = false) {
        $html;

        $class = "";

        if($ajax !== false) {
          $class .= $ajax;
        }


        // Check if it should be a compact switch.
        if($compact) {
          $html = "<div class='ui-field-contain ajax-$class compact-switch'>\n";
        } else {
          $html = "<div class='ui-field-contain'>\n";
        }

        // Don't create a label if it will not contain text.
        if($label !== null && $label !== "") {
            $html .= '<label for="flip-select-second">' . $label . '</label>';
        }


        $html .= '<select id="flip-select-second" name="' . $name . '" data-role="flipswitch">'
                . '<option value="False">' . $option_left . '</option>';

        // Set the default position to right if this is specified.
        // Check if the default position is equal to the right one, if the default position is parsed as a string.
        if($default_pos === $option_right && is_string($default_pos) ||
                // Check if the default position is right (true) if the default position is set as an boolean.
                ($default_pos && is_bool($default_pos))) { 
            $html .= '<option selected="" value="True">' . $option_right . '</option>';
        } else {
            $html .= '<option value="True">' . $option_right . '</option>';
        }

        $html .= "</select>\n";

        return $html . "</div>\n"; // Return the generated HTML of the switch.
    }

    /**
     * Generates the HTML of a submit button.
     * @param string    The text of the submit button.
     * @param boolean   If the button should have the jQM inline style.
     * @return string   The HTML of the submit button.
     */
    public function get_submit($title = "Submit", $inline = false) {
        $classes = "ui-btn ui-shadow ui-corner-all";
        
        if($inline) {
            $classes .= " ui-btn-inline";
        }
        
        return "<button type='submit' data-role='button'"
                . " class='$classes' name='submit' value='submit'>$title</button>";
    }
    
    /**
     * Generates HTML for a radio button list of all supplied users.
     * @param array     The user data as multidimentional array.
     * @param string    The name of the radio buttons.
     * @param boolean   If it should be filterable or not.
     * @param string    The ID of the user whom should be selected.
     * @param boolean   If the radiobuttons are a required field of the form.
     * @return string   The generated HTML.
     */
    public function get_radio_all_users($users, $name, $filterable = false, 
            $select_id = null, $required = false) {
        if($filterable) {
            $filterable = "true";
        } else {
            $filterable = "false";
        }
        
        $html = "<fieldset data-role='controlgroup' data-filter='$filterable'"
                . " data-inset='$filterable'>\n";
        
        foreach($users as $user) {
            $user_id = $user['id'];
            $user_full_name = $this->Util->combine_name($user);
            
            $attr = "type='radio' name='$name' id='$name-$user_id' value='$user_id'";
            
            if($select_id !== null && $select_id === $user_id) {
                $attr .= " checked='checked'";
                $user_full_name = "Me: " . $user_full_name;
            }
            
            if($required) {
                $attr .= " required";
            }
            
            $html .= "<input $attr>\n"
                    . "<label for='$name-$user_id'>$user_full_name</label>\n";
        }
        
        $html .= "</fieldset>\n";
        
        return $html;
    }
    
    public function get_horizontal_spinbox($name, $value = 0, $min = null, $max = null) {
        $attr = "class='spinbox-btn'"
                . " type='text'"
                . " data-role='spinbox' "
                . " data-options='{'type':'horizontal'}'"
                . " value='$value'"
                . " name='$name'";
        
        if($min !== null) {
            $attr .= " min='$min'";
        }
        
        if($max !== null) {
            $attr .= " max='$max'";
        }
        
        echo "<input $attr/>\n";
    }
}
