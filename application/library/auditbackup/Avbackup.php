<?php

class auditbackup_Avbackup
{
    private $system_id;
    private $backup_type;
    private $api_client;


    public function __construct($system_id, $backup_type)
    {
        $this->system_id = $system_id;
        $this->backup_type = $backup_type;
        $this->api_client  = new auditbackup_Alienvaultclient();
    }

//    public function get_audit_backup_list()
//    {
//        $response = $this->api_client->system($this->system_id)->get_audit_backup_list($this->system_id);
//        $response = @json_decode($response, TRUE);
//
//        if (!$response || $response['status'] != 'success') {
//            $exp_msg = $this->api_client->get_error_message($response);
//
//            Av_exception::throw_error(Av_exception::USER_ERROR, $exp_msg);
//        }
//
//        $data = (is_array($response['data']['file_list'])) ? $response['data']['file_list'] : array();
//
//        return $data;
//    }

    public function restore_audit_backup_list($file)
    {
        //var_dump($this->api_client->system($this->system_id));die;
        $response = $this->api_client->system($this->system_id)->restore_audit_backup_list($this->system_id,$file);
        $response = @json_decode($response, TRUE);
        //var_dump($response);die;
        if (!$response || $response['status'] != 'success')
        {
            $exp_msg = $this->api_client->get_error_message($response);

            Av_exception::throw_error(Av_exception::USER_ERROR, $exp_msg);
        }

        $data = $response['data'];

        return $data;
    }
}

?>