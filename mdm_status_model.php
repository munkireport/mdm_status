<?php

use CFPropertyList\CFPropertyList;

class Mdm_status_model extends \Model
{
    public function __construct($serial = '')
    {
        parent::__construct('id', 'mdm_status'); // Primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['mdm_enrolled_via_dep'] = '';
        $this->rs['mdm_enrolled'] = '';
        $this->rs['mdm_server_url'] = '';
        $this->rs['last_mdm_kickstart'] = null;
        $this->rs['last_software_update_kickstart'] = null;
        $this->rs['is_supervised'] = null;
        $this->rs['enrolled_in_dep'] = null;
        $this->rs['denies_activation_lock'] = null;
        $this->rs['activation_lock_manageable'] = null;
        $this->rs['is_user_approved'] = null;
        $this->rs['is_user_enrollment'] = null;
        $this->rs['managed_via_mdm'] = null;
        $this->rs['org_address_full'] = null;
        $this->rs['org_address'] = null;
        $this->rs['org_city'] = null;
        $this->rs['org_country'] = null;
        $this->rs['org_email'] = null;
        $this->rs['org_magic'] = null;
        $this->rs['org_name'] = null;
        $this->rs['org_phone'] = null;
        $this->rs['org_support_email'] = null;
        $this->rs['org_zip_code'] = null;
        $this->rs['original_os_version'] = null;
        $this->rs['mdm_server_url_full'] = null;

        if ($serial) {
            $this->retrieve_record($serial);
        }

        $this->serial = $serial;
    }


    // ------------------------------------------------------------------------

    /**
     * Process data sent by postflight
     *
     * @param string data
     *
     **/
    public function process($data)
    {
        $parser = new CFPropertyList();
        $parser->parse($data);

        $plist = $parser->toArray();

        foreach (array('mdm_enrolled_via_dep', 'mdm_enrolled', 'mdm_server_url', 'last_mdm_kickstart', 'last_software_update_kickstart', 'is_supervised', 'enrolled_in_dep', 'denies_activation_lock', 'activation_lock_manageable', 'is_user_approved', 'is_user_enrollment', 'managed_via_mdm', 'org_address_full', 'org_address', 'org_city', 'org_country', 'org_email', 'org_magic', 'org_name', 'org_phone', 'org_support_email', 'org_zip_code', 'original_os_version', 'mdm_server_url_full') as $item) {
            if (isset($plist[$item])) {
                $this->$item = $plist[$item];
            } else if ($item == 'mdm_enrolled_via_dep' || $item == 'mdm_enrolled' || $item == 'mdm_server_url'){
                // Blank if empty existing columns
                $this->$item = '';
            } else {
                // Null if empty everything else
                $this->$item = null;
            }
        }
        $this->save();
    }
}
