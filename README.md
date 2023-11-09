MDM Status module
================
The "MDM Enrolled" widget values are as follows:
  No = Not enrolled in MDM
  DEP = DEP enrolled MDM
  User Approved = User-Approved MDM

The following information is stored in the mdm_status table:

* Enrolled via DEP 10.13.4+
	- "Yes" or "No"
* MDM Enrollment Status 10.13.4+
	- "No", "Yes" installed but not approved, "Yes (User Approved)"
* Server URL
    - Captures the base URL of the MDM enrollment

mdm-watchdog
---
This module reports on when [mdm-watchdog](https://addigy.com/mdm-watchdog/) kickstarts the `mdmclient` or `softwareupdated` on the Mac.

Table Schema
---

The table contains the following information:

* id - int - Unique id
* mdm_enrolled - string - If Mac is enrolled in MDM
* mdm_enrolled_via_dep - string - If enrolled via DEP/ABM/ASM
* mdm_server_url - string - URL of enrolled MDM server
* last_mdm_kickstart - big int - Timestamp of when mdm-watchdog last kickstarted the `mdmclient`
* last_software_update_kickstart - big int - Timestamp of when mdm-watchdog last kickstarted `softwareupdated`
* is_supervised - int - Is the Mac supervised
* enrolled_in_dep - int - Is the Mac in ABM/ASM
* denies_activation_lock - int - Does the MDM disallow activation lock
* activation_lock_manageable - int - Can the MDM manage activation lock
* is_user_approved - int - Is the MDM user approved
* is_user_enrollment - int - Was the Mac enrolled in the MDM via user enrollment
* managed_via_mdm - int - Is the Mac managed by MDM
* org_address_full - text - One line address of organization
* org_address - string - Organization's address
* org_city - string - Organization's city
* org_country - string - Organization's country
* org_email - string - Organization's email address
* org_magic - string - Organization's magic (short name)
* org_name - string - Organization's name
* org_phone - string - Organization's phone number
* org_support_email - string - Organization's support email address
* org_zip_code - string - Organization's ZIP code
* original_os_version - string - Version of macOS the Mac originally enrolled with
* mdm_server_url_full - string - Full URL of MDM server