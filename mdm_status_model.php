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

        foreach (array('mdm_enrolled_via_dep', 'mdm_enrolled', 'mdm_server_url', 'last_mdm_kickstart', 'last_software_update_kickstart') as $item) {
            if (isset($plist[$item])) {
                $this->$item = $plist[$item];
            } else if ($item == 'last_mdm_kickstart' || $item == 'last_software_update_kickstart'){
                // Null the last_mdm_kickstart and last_software_update_kickstart if blank
                $this->$item = null;
            } else {
                $this->$item = '';
            }
        }
        $this->save();
    }
}
