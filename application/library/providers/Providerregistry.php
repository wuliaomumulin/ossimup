<?php
class providers_Providerregistry
{
    private $registry;

    public function __construct()
    {
        $this->registry = array();
    }


    /**
     * Sets the current provider
     * If the provider is not registered, we register it
     *
     * @access public
     * @param string item's unique name
     * @param mixed item
     *
     * @return boolean
     */
    public function set($conn, $name, $section)
    {
        $result = FALSE;

        if (!$this->exists($name))
        {
            $object = self::Create($conn, $name, $section);

            if($result!==NULL)
            {
                $this->registry[$name] = $object;

                $result = TRUE;
            }
        }
        else
        {
            //URL could have changed, we set up it again
            $this->registry[$name]->set_common_url($section);
        }

        return $result;
    }


    /**
     * Returns registered item
     *
     * @access public
     * @param string item's name
     *
     * @return mixed (null if name is not in registry)
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->registry))
        {
            $return = $this->registry[$name];
        }
        else
        {
            $return = NULL;
        }

        return $return;
    }

    /**
     * Returns true if item is registered
     *
     * @access public
     * @param string unique item's name
     *
     * @return boolean
     */
    public function exists($name)
    {
        if (is_string($name))
        {
            return array_key_exists($name, $this->registry);
        }
        else
        {
            throw new Exception('Registry item\'s name must be a string');
        }
    }



    public static function Create($conn, $name, $common_url)
    {
        switch ($name)
        {
            // Auth
            case 'auth':
                require_once 'auth.inc';

                $result = new Auth_provider($conn, $common_url);

                break;

            // Sensor
            case 'sensor':
                require_once 'sensor.inc';

                $result = new Sensor_provider($conn, $common_url);

                break;


            // NMAP
            case 'nmap':
                require_once 'nmap.inc';

                $result = new Nmap_provider($conn, $common_url);

                break;

            // System
            case 'system':
             //   require_once 'system.inc';

                $result = new \providers\providers_Syetem();
                $result->a();
                break;

            // Server
            case 'server':
                require_once 'server.inc';

                $result = new Server_provider($conn, $common_url);

                break;

            // Data
            case 'data':
                require_once 'data.inc';

                $result = new Data_provider($conn, $common_url);

                break;

            // Jobs
            case 'jobs':
                require_once 'jobs.inc';

                $result = new Jobs_provider($conn, $common_url);

                break;


            // ASEC
            case 'asec':
                require_once 'asec.inc';

                $result = new Asec_provider($conn, $common_url);

                break;


            // Plugin
            case 'plugin':
                require_once 'plugin.inc';

                $result = new Plugin_provider($conn, $common_url);

                break;

            case 'central_console':
                require_once 'central_console.inc';
                $result = new CentralConsoleProvider($conn, $common_url);
                break;



            default:

                $result = NULL;
        }

        return $result;
    }
}
?>