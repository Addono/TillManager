<?php
class Util {
    private $ci;
    private $resources;
    
    function __construct($ci) {
        $this->ci = $ci;
        $this->ci->load->helper('url');
        
        $this->resources = $ci->base_url() . "application/resources";
    }
    
    
}