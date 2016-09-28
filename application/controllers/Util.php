<?php
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
        $this->form = new Form($ci);

        $this->Ajax = new Ajax;
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
    
    /**
     * Concaterantes the real name of a user.
     * @param array The data of the user as an array.
     * @return string The real name of the parsed user.
     */
    function combine_name($user) {
        if($user['prefix_name'] != "" && $user['prefix_name'] != null) {
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
                echo "<link href='$this->location/style.css' rel='stylesheet'>";
                break;
            case 'jquery':
                echo "<script src='$this->location/jquery-ui/external/jquery/jquery.js' type='text/javascript'></script>";
                break;
            case 'jquery-ui':
                echo "<link href='$this->location/jquery-ui/jquery-ui.min.css' rel='stylesheet'>\n" .
                    "<script src='$this->location/jquery-ui/jquery-ui.min.js' type='text/javascript'></script>";
                break;
            case 'jquery-mobile':
                echo "<link href='$this->location/jquery-mobile/jquery.mobile-1.4.5.css' rel='stylesheet'>\n" .
                    "<script src='$this->location/jquery-mobile/jquery.mobile-1.4.5.min.js' type='text/javascript'></script>";
                break;
            default: // Log an error if the package does not exist.
                error_log("Package  '$package' was not found, and could therefore not be added by the Resources class.");
                break;
        }
    }
}

class Ajax {
  public function switch_js($class) {
    echo
    "<script>
    $( document ).ready(function() {
      $('.ajax-$class').click(function() {
        var select = $( this ).find('select');
        var name = select.attr('name');
        var value = select.val();

        $.post('". base_url() . "index.php/ajax/$class-switch', {name: name, value: value}, function(result) {
          console.log('I tried to send ' + name + ':' + value);
          console.log('The server returned \"' + result + '\"');

          switch(result) {
            case 'failed: user not logged in':
              alert('Action failed because you are not logged in anymore, reload the page and login before proceding.');
              break;
            case 'failed: access denied':
              alert('Action failed, you do not have enough rights to do this.');
              break;
          }
        });
      });
    });
</script>\n";
  }
}

class Form {
    private $ci;

    function __construct($ci) {
        $this->ci = $ci;
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
        if($label != null && $label != "") {
            $html .= '<label for="flip-select-second">' . $label . '</label>';
        }


        $html .= '<select id="flip-select-second" name="' . $name . '" data-role="flipswitch">'
                . '<option>' . $option_left . '</option>';

        // Set the default position to right if this is specified.
        if($default_pos === $option_right && is_string($default_pos) || // Check if the default position is equal to the right one, if the default position is parsed as a string.
                ($default_pos && is_bool($default_pos))) { // Check if the default position is right (true) if the default position is set as an boolean.
            $html .= '<option selected="">' . $option_right . '</option>';
        } else {
            $html .= '<option>' . $option_right . '</option>';
        }

        $html .= "</select>\n";

        return $html . "</div>\n"; // Return the generated HTML of the switch.
    }

    public function get_submit($title = "Submit", $inline = false) {
        return '<button type="submit" data-role="button" class="ui-btn ui-shadow ui-corner-all' . ($inline ? ' ui-btn-inline' : '') . '" name="submit" value="submit">' . $title . '</button>';
    }
}
