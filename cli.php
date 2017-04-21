<?php

/** 
 * Author: Dan Shumaker
 * Date: 5/23/2012
 * Updates: 1/4/2014  Making more extensible.
 *     Creating CommandLineSettings that extends this class
 *     Created help_docs
 *
 * CommandLine class
 * @package             Framework
 * http://php.net/manual/en/features.commandline.php
 * Modified by Dan Shumaker to handle default options
 */

/*
 * By default you get debug and help options
 *
 */
class CommandLine {

    public $args = array();
    public $script_name = '';
    public $help_docs = '';

    function __construct($help=NULL) { 
      $this->help_docs = $help;
      $this->args['debug'] = false;
      $this->args['help'] = false;
    }
    public function parseArgs($argv){
        $this->script_name = $argv[0];
        array_shift($argv);
        foreach ($argv as $arg){
            // --foo --bar=baz
            if (substr($arg,0,2) == '--'){
                $eqPos = strpos($arg,'=');
                // --foo
                if ($eqPos === false){
                    $key = substr($arg,2);
                    if ($key === 'help') { $this->print_help(); exit(); }
                    if (isset($this->args[$key])) {
                      // The existance of a given/supplied parameter by default
                      // means to turn it on . which is set it to true
                      $this->args[$key] = true;
                    } else {
                        // If the arg was not created with add_option then bail
                        $this->print_help($key);
                        exit();
                    }
                }
                // --bar=baz
                else {
                    $key                = substr($arg,2,$eqPos-2);
                    $value              = substr($arg,$eqPos+1);
                    $this->args[$key] = $value;
                }
            }
            // plain-arg
            else {
                $this->print_help($arg);
                exit();
            }
        }
    }
    public function print_help($bad='') {
      if ($bad !== '') {
        print "\nERROR:Invalid arguement " . $bad . "\n\n";
      } else { 
        print $this->help_docs;
      }

      print "\n Sample command line : \n\n" . basename($this->script_name) . ' ';
      foreach($this->args as $key => $value ) { 
        if ($value) {
          printf("--%s=%-s ", $key , $value? $value: 'off' );
        }
      }
      print "\n\n\nCommand line options are:\n";
      foreach($this->args as $key => $value ) printf("\t--%-15s ( %-s )\n", $key , $value? $value: 'off' );
    }
    public function add_option($option, $value=false) {
      $this->args[$option] = $value;
    }
    public function set_help($help) {
      $this->help_docs = $help;
    }
}

?>
