<?php

class auditbackup_Alienvaultconn
{
    private $username;
    private $host;
    private $port;
    private $cookie_file;
    private $debug_file;
    private $error;
    private $prefix_url;
    private $log;
    private $timeout;

    public function __construct($username = NULL, $host = NULL, $port = NULL, $log = FALSE)
    {
        $this->username    = ($username != '')                 ? $username : 'admin';//$_SESSION['username'];
        $this->username    = strtolower($this->username);
        $this->host        = (self::valid_ip($host)) ? $host     : '127.0.0.1';
        $this->port        = ($port>0 && $port<65535)          ? $port     : 40011;
        $this->timeout     = 120;

        $this->prefix_url  = "/av/api/1.0";

        $this->error       = '';
        $this->log         = file_exists('/tmp/debug_api') ? TRUE : $log;
        $this->cookie_file = '/var/tmp/api_cookie_jar_'.$this->username.'.txt';
        $this->debug_file  = '/tmp/api';
    }

    public function valid_ip($ip)
    {
        $cnd_1 = ($ip != '0.0.0.0');
        $cnd_2 = ($ip != '255.255.255.255');
        $cnd_3 = (preg_match('/^(([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$/', $ip) == TRUE);

        if ($cnd_1 && $cnd_2 && $cnd_3)
        {
            return TRUE;
        }

        return FALSE;
    }

}
?>