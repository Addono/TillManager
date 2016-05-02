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
    
    /**
     * Adds a message to the log.
     * @param type The message it should display.
     * @param type The icon it should display.
     * 
     * Todo: Use something different than buttons to make them easier to distinguish.
     */
    public function show_message($message, $icon) {
        $this->html_log .= '<a disable class="ui-shadow-icon ui-btn-icon-left ui-btn ui-shadow ui-corner-all ui-icon-' . $icon . '">' . $message . '</a>';
    }
}