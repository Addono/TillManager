<?php
class Util {
    public $resources;
    
    private $ci;
    
    function __construct($ci) {
        $this->ci = $ci;
        $this->ci->load->helper('url'); // Load required packages.
        
        $this->resources = new resources(base_url() . "application/resources");
    }
    
}

/*
 * 
        <script src="<?php echo $resources;?>/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="<?php echo $resources;?>/jquery-mobile/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
 */

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