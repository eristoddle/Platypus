<?php
/** 
 * This script is meant to be executed via crontab.
 * It should help you easily set li3 commands as cron jobs.
 *
 * You should put this in the root of your application.
 * However, you don't need to, but you would then need to
 * pass a --path= option with the path to your app.
 * This is because the li3 console command must be called
 * from a specific location.
 *
 * Usage Examples:
 *
 * This would specify the development environment for some command.
 * > php /path/to/cron.php --env=development mycommand method
 *
 * This would show the li3 help command output. 
 * Note the verbose flag can be useful for testing before setting cron.
 * > php /path/to/cron.php --verbose help
 *
*/

// While this script shouldn't be able to be accessed from a web browser
// because it should sit outside the webroot...This isn't a bad idea.
if(php_sapi_name() != 'cli') die('Access denied.');

// Who knows what the li3 console command was doing, this can help prevent
// issues with hitting a memory limit.
gc_collect_cycles();

// This will parse the args.
// --arg=val will be array('arg' => 'val')
// arg will be array(0 => 'arg')
// --arg will be array('arg' => true)
function parseArgs($argv){
    array_shift($argv);
    $out = array();
    foreach ($argv as $arg){
        if (substr($arg,0,2) == '--'){
            $eqPos = strpos($arg,'=');
            if ($eqPos === false){
                $key = substr($arg,2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            } else {
                $key = substr($arg,2,$eqPos-2);
                $out[$key] = substr($arg,$eqPos+1);
            }
        } else if (substr($arg,0,1) == '-'){
            if (substr($arg,2,1) == '='){
                $key = substr($arg,1,1);
                $out[$key] = substr($arg,3);
            } else {
                $chars = str_split(substr($arg,1));
                foreach ($chars as $char){
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
        } else {
            $out[] = $arg;
        }
    }
    return $out;
}

// Get the passed args.
$options = parseArgs($argv);

// Specify --env= in the cron, otherwise it will be set to production by default.
// Then unset the value so it doesn't get in the way of building the li3 command.
$env = (isset($options['env'])) ? $options['env']:'production';
if(isset($options['env'])) {
    unset($options['env']);
}

// This script is meant to be in the root of the application. 
// If it is not, then --path= must be passed with the full path to the app.
$li3_app_path = (isset($options['path'])) ? $options['path']:__DIR__;
if(isset($options['path'])) {
    unset($options['path']);
}

// This can be helpful if you want to test the call before putting it in crontab.
// Just call the same command you'd set in cron with --verbose passed and you'll see output.
$verbose = (isset($options['verbose'])) ? true:false;
if(isset($options['verbose'])) {
    unset($options['verbose']);
}

// The command is simply the remainder of the arguments passed. 
// Technically, the --env and/or --path can be at the beginning or end.
$li3_command = join(' ', $options);

// Call the command(s) with a subshell. We need ensure li3 is called from the proper directory.
$command = "(cd {$li3_app_path} && exec libraries/lithium/console/li3 --env={$env} $li3_command)";

// Do something with the output.
if($verbose) {
    echo `$command`;
    echo PHP_EOL;
} else {
    $nullResult = `$command > /dev/null &`;
}

exit();
