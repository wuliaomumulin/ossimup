<?php

class auditbackup_Alienvaultclient
{
    private $conn;
    private $providers;
    private $common_url;

    public function __construct($username = NULL, $host = NULL, $port = NULL, $log = FALSE)
    {
        $this->conn       = new auditbackup_Alienvaultconn($username, $host, $port, $log);

        $this->providers  = new providers_Providerregistry();
        //var_dump($this->providers);
        $this->common_url = '';
    }

    public function system($system_id = 'local')
    {
        $this->providers->set($this->conn, 'system', $this->common_url . "/system/$system_id");

        return $this->providers->get('system');
    }

}

?>