<?php
    use lithium\analysis\Logger; 

    Logger::config(array(
        'default' => array('adapter' => 'Syslog'),
        'badnews' => array(
            'adapter' => 'File',
            'priority' => array('emergency', 'alert', 'critical', 'error')
        ),
    ));