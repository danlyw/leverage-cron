<?php
return array(

    /*
	|--------------------------------------------------------------------------
	| Default FTP Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the FTP connections below you wish
	| to use as your default connection for all ftp work.
	|
	*/

    'default' => 'usfoods',

    /*
    |--------------------------------------------------------------------------
    | FTP Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the FTP connections setup for your application.
    |
    */

    'connections' => array(

        'usfoods' => array(
            'host'   	=> 'ediprod.usfoods.com',
            'port'  	=> 21,
            'username' 	=> 'ECLEVBUY',
            'password'	=> 'ka0a1raP',
            'passive'   => true,
            'secure' 	=> false
        ),
    ),
);