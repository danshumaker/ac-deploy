ac-deploy
=========

deploy

/*
 * Author: Dan Shumaker
 * Date: 10/1/2012
 * script: deploy
 
Description: Move files, database, or code from one acquia server to the next and run drush commands.
    - Yes you can use the gui to do most of these things (not the drush, varnish, memcache stuff) but CLI's are better! :)
 
Features:  
     - Task Queue - It will wait until one acquia task process is done before starting another one.  This is 
     nice because their might be six or seven acquia tasks that need to be done for any one push and with
     this deploy tool you only have to initiate the sequence and then "walk away".  If any one of the tasks
     fail then the queue of tasks is aborted (not rolled back but just halted).
 
     - Command line Automation - While the gui is nice I wanted to be able to execute several tasks at once .  This deploy
     script automates that for you.
 
Dependencies:
     - This deploy script depends on valid drush aliases being installed on your acquia server.
        - You'll need acquia's aliases for your sites: https://docs.acquia.com/cloud/drush-aliases
        - You'll need acquia's cloud api for your site: https://insight.acquia.com/cloud/util/download/drush
     - Only tested with drush 5.
     - access to acquia network accounts.
     - ssh keys installed on acquia server and network accounts.
 
Setup:
    - Initally you need to setup a deploy process up by saving all the credentials in a configuration file.  Then after that you call deploy with
      the --load parameter and nothing else and it does the rest.
 
Typical usages after setup:
 
    deploy --load=stage_to_dev_db+files.dep
    deploy --load=dev_to_stage_code_only.dep
    deploy --load=dev_to_stage.dep
    deploy --load=stage_to_prod.dep
 
Typical setup usage:
 
     Setup for dev_to_stage_db_files.dep
        deploy --varnish=1 \
           --database=1 \
           --files=1 \
           --memcache=1 \
           --drush="vset drupal_stale_file_threshold 1 -y;vset shield_user XXXXXX -y; vset shield_pass XXXXXXX -y; cc all;vset drupal_stale_file_threshold 172800 -y" \
           --source=@drush_dev_server_alais \
          --dest=@drush_stage_server_alias \
          --dest_db=XXXX \
          --dest_env=dev  \
          --dest_host=staging-XXXX.prod.hosting.acquia.com \
          --dest_domain=XXXXXdev.prod.acquia-sites.com \
          --save=stage_to_dev_db_files.dep
 
Command Line Options:
 	--varnish         ( off )   -- (boolean) Purges the varnish cache for the site.
	--database        ( off )   -- Specifies the databasename to push & pull
	--code            ( off )   -- (boolean) Whether or not to push code -- off by default
	--files           ( off )   -- (boolean) whether or not to push the default/files directory
	--backup          ( off )   -- (boolean) backup the database before pushing and pulling it.
  --memcache        ( off )   -- (boolean) purge memcache on server
	--drush           (            (quote surrounded drush commands with the drush left out of the beginning of the command see examples)
	--source          ( @dev )  (drush alias of source site)
	--dest            ( @test )  ( drush alias of destination site)
	--dest_db         ( autocalculated from drush dest alais )    -- you can override the autocalculated values if you wish.
	--dest_env        ( autocalculated from drush dest alais )
	--dest_host       ( autocalculated from drush dest alais )
	--dest_domain     ( autocalculated from drush dest alais )
	--save            ( off )    (don't run any commands but save the settings to a file for later use -- RUN THIS FIRST)
	--load            ( off )    (specify a deploy settings / configuration file to load and execute) 
	--print           ( off )    (print help)

 */
