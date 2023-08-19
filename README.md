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