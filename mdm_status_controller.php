<?php
/**
 * Mdm_status module class
 *
 * @package munkireport
 * @author
 **/
class Mdm_status_controller extends Module_controller
{
    
    /*** Protect methods with auth! ****/
    public function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__);
    }
    /**
     * Default method
     *
     * @author eholtam
     **/
    public function index()
    {
        echo "You've loaded the mdm_status module!";
    }
    
    public function get_mdm_enrolled_via_dep_stats()
    {
        $sql = "SELECT COUNT(CASE WHEN mdm_enrolled_via_dep = 'Yes' THEN 1 END) AS dep_enrolled, 
            COUNT(CASE WHEN mdm_enrolled_via_dep = 'No' THEN 1 END) AS not_dep_enrolled
            FROM mdm_status
            LEFT JOIN reportdata USING (serial_number)
            ".get_machine_group_filter();

        $out = [];
        $queryobj = new Mdm_status_model();
        foreach($queryobj->query($sql)[0] as $label => $value){
                $out[] = ['label' => $label, 'count' => $value];
        }

        jsonView($out);
    }

    public function get_mdm_legacy_stats()
    {

        $sql = "SELECT COUNT(CASE WHEN mdm_enrolled = 'No' THEN 1 END) AS mdm_no,
            COUNT(CASE WHEN mdm_enrolled = 'Yes' THEN 1 END) AS non_uamdm, 
            COUNT(CASE WHEN mdm_enrolled = 'Yes (User Approved)' AND mdm_enrolled_via_dep <> 'Yes' THEN 1 END) AS uamdm,
            COUNT(CASE WHEN mdm_enrolled = 'Yes (User Approved)' AND mdm_enrolled_via_dep = 'Yes' THEN 1 END) AS dep_enrolled           
            FROM mdm_status
            LEFT JOIN reportdata USING (serial_number)
            ".get_machine_group_filter();

        $obj = new View();
        $mdm_status = new Mdm_status_model;
        jsonView($mdm_status->query($sql)[0]);
    }

    public function get_mdm_stats()
    {
         $sql = "SELECT COUNT(CASE WHEN mdm_enrolled = 'No' THEN 1 END) AS mdm_no,
            COUNT(CASE WHEN mdm_enrolled = 'Yes' THEN 1 END) AS non_uamdm, 
            COUNT(CASE WHEN mdm_enrolled = 'Yes (User Approved)' AND mdm_enrolled_via_dep <> 'Yes' THEN 1 END) AS uamdm,
            COUNT(CASE WHEN mdm_enrolled = 'Yes (User Approved)' AND mdm_enrolled_via_dep = 'Yes' THEN 1 END) AS dep_enrolled           
            FROM mdm_status
            LEFT JOIN reportdata USING (serial_number)
            ".get_machine_group_filter();

        $out = [];
        $queryobj = new Mdm_status_model();
        foreach($queryobj->query($sql)[0] as $label => $value){
                $out[] = ['label' => $label, 'count' => $value];
        }

        jsonView($out);
    }

    public function get_mdm_server_url()
    {
        $sql = "SELECT COUNT(CASE WHEN mdm_server_url <> '' AND mdm_server_url IS NOT NULL THEN 1 END) AS count, mdm_server_url
                FROM mdm_status
                LEFT JOIN reportdata USING (serial_number)
                ".get_machine_group_filter()."
                GROUP BY mdm_server_url
                ORDER BY count DESC";

        $out = [];
        $queryobj = new Mdm_status_model;
        foreach ($queryobj->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->key = str_replace(['https://','.com/mdm'], ['','.com'], $obj->mdm_server_url ? $obj->mdm_server_url : 'Unknown');
                $out[] = $obj;
            }
        }

        jsonView($out);
    }

    /**
     * Get tab data for serial_number
     *
     * @param string $serial serial number
     **/
    public function get_data($serial = '')
    {
        // Remove non-serial number characters
        $serial = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial);

        $out = array();

        $prm = new Mdm_status_model;
        foreach ($prm->retrieve_records($serial) as $mdm_status) {
            $out[] = $mdm_status->rs;
        }

        $obj = new View();
        $obj->view('json', array('msg' => $out));
    }

} // END class Mdm_status_controller
