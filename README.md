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
