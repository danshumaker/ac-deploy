<?php

/** 
 * Author: Dan Shumaker
 * Date: 5/23/2012
 * 
 * CommandLine class
 * @package             Framework
 * http://php.net/manual/en/features.commandline.php
 * Modified by Dan Shumaker to handle default options
 */


require_once dirname(__FILE__) . '/cli.php';
class CommandLineSettings extends CommandLine {

    public $settings_file = '';
    public $store_option = array(); // Stores whether or not to save
       // A command line option to the settings file or not.

    function __construct($help=NULL) {
      parent::__construct($help);

      // By default setup save, load, and print for settings
      $this->add_option('save', false, false);
      $this->add_option('load', false, false);
      $this->add_option('print', false, false);
      $this->add_option('help', false, false);
      $this->add_option('debug', false, false);
    }

    public function load_settings() {
      $vals = file_get_contents($this->args['load']);
      // Later over writes former
      //                          defaults<--stored values
      if ($vals) {
        $this->args = array_merge($this->args, unserialize($vals));
      } else { 
        print "ERROR: Bad loaded file\n\n";
        exit();
      }
    }
    function ismake($dir) {
      if (is_dir($dir)) {
        return;
      } else {
          if (mkdir($dir, 0777, true)) {
            return;
          } else {
            print "Couldn't create directory ". $dir;
            exit();
          }
      }
    }
    public function save_settings() {
      if (is_string($this->args['save'])) {
        $base = dirname($this->args['save']) ;
        $this->ismake($base);

        // remove unwanted values from being stored
        $file = $this->args['save'];
        foreach($this->store_option as $key => $value) {
          if (!$this->store_option[$key]) {
            unset($this->args[$key]);
          }
        }
        $args = serialize($this->args);
        file_put_contents($file, $args );
      } else {
        print 'Invalid save string filename\n';
        exit();
      }
    }
    public function add_option($option, $value, $type) {
      parent::add_option($option, $value);
      $this->store_option[$option] = $type;
    }
}

?>
