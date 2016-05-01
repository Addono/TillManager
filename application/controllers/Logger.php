<?php
class Logger {
    private $ci;
    private $html_log;
    
    function __constructor($ci) {
        $this->ci = $ci;
    }
    
    public function get_html_log() {
        return $this->html_log;
    }
    
    public function show_html_log() {
        echo $this->html_log;
    }
    
    public function add_comment($message) {
        $this->html_log .= "<!-- $message -->\n";
    }
    
    public function show_error($message) {
        $this->html_log .= '
            <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                <strong>Error: </strong>' . $message . '</p>
            </div>';
    }

    public function show_warning($message) {
        $this->html_log .= '
                <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
                        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                        <strong>Warning: </strong>' . $message . '</p>
                </div>';
    }
}