<?php

if( !function_exists('echoDbg') ) {
    /**
     * echo debug data
     *
     * @param $value
     * @param $key
     * @param bool $exit
     * @param bool $backtrack
     */
    function echoDbg($value, $key = '', $exit = false, $backtrack = false)
    {
        echo '<br/>';
        if($key){
            echo '<strong>'.$key.'</strong><br/>';
        }
        echo '<pre>'.print_r($value, true).'</pre><br/>';

        if($backtrack) {
            echo '<pre>';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 100);
            echo '</pre>';
        }
        if($exit) exit('end debug');
    }
}