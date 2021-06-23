<?php
class providers_Provider
{
    /**
     * Connection object
     * @access protected
     * @var object
     *
     */
    protected $conn;

    /**
     * Providers objects
     * @access protected
     * @var object
     *
     */
    protected $sub_providers;

    /**
     * common url to do the request
     * @access private
     * @var string
     *
     */
    protected $common_url;

    /**
     * The class constructor
     *
     * This sets up the providers classes
     *
     * @param object  $conn        Connection object
     * @param string  $common_url  Common url to do the request
     *
     */
    public function __construct($conn, $common_url)
    {
        $this->conn          = $conn;

        $this->sub_providers = new Provider_registry();

        $this->common_url    = $common_url;
    }


    /**
     * This function returns the URL related to the provider
     *
     * @return string
     */
    public function get_common_url()
    {
        return $this->common_url;
    }


    /**
     * This function sets a new URL for the provider
     *
     * @param string  $common_url  Provider URL
     *
     * @return void
     */
    public function set_common_url($common_url)
    {echo 111;die;
        $this->common_url = $common_url;
    }
}
?>