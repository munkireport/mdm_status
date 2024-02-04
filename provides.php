<?php

return array(
    'detail_widgets' => [
        'mdm_status_detail' => ['view' => 'mdm_status_detail_widget'],
    ],
    'listings' => array(
        'mdm_status' => array('view' => 'mdm_status_listing', 'i18n' => 'mdm_status.title'),
    ),
    'widgets' => array(
        'mdm_status' => array('view' => 'mdm_status_widget'),
        'mdm_enrolled_via_dep' => array('view' => 'mdm_enrolled_via_dep_widget'),
		'user_approved_status' => array('view' => 'user_approved_status_widget'),
	),
    'reports' => array(
        'mdm_status' => array('view' => 'mdm_status_report', 'i18n' => 'mdm_status.mdm_report'),
    ),
);
