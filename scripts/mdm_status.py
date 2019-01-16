#!/usr/bin/python

"""
MDM status reporting tool. Requires macOS 10.13.4+ for command output compatibility.
"""

import subprocess
import os
import plistlib
import sys
import platform

def get_mdm_status_modern():
    '''Uses profiles command to get MDM status for this machine.
    Requires macOS 10.13.4 due to MDM status output changes.'''
    cmd = ['/usr/bin/profiles', 'status', '-type', 'enrollment']
    proc = subprocess.Popen(cmd, shell=False, bufsize=-1,
                            stdin=subprocess.PIPE,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    (output, unused_error) = proc.communicate()

    enrolled_via_dep = ''
    mdm_enrollment = ''
    values = output.split('\n')
    for value in values:
        if "Enrolled via DEP:" in value:
            enrolled_via_dep = value.split(':')[1].lstrip()
            if "Yes" in enrolled_via_dep:
                enrolled_via_dep = "Yes"
            else:
                enrolled_via_dep = "No"

        if "MDM enrollment:" in value:
            mdm_enrollment = value.split(':')[1].lstrip()

    result = {}
    result.update({'mdm_enrolled_via_dep': enrolled_via_dep})
    result.update({'mdm_enrolled': mdm_enrollment})
    return result

def get_mdm_status_legacy():
    """Uses profiles command on older versions of macOS to check if a machine
    is enrolled in MDM."""
    cmd = ['/usr/bin/profiles', '-C', '-o', 'stdout-xml']
    run = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    output, err = run.communicate()

    result = {}
    try:
        plist = plistlib.readPlistFromString(output)
    except: # pylint: disable=bare-except
        plist = {'_computerlevel': []}

    try:
        for possible_plist in plist['_computerlevel']:
            for item_content in possible_plist['ProfileItems']:
                try:
                    profile_type = item_content['PayloadType']
                except KeyError:
                    profile_type = ''
                if profile_type == 'com.apple.mdm':
                    result.update({'mdm_enrolled': "Yes (User Approved)"})
                    return result
    except KeyError:
        result.update({'mdm_enrolled': "No"})
        result.update({'mdm_enrolled_via_dep': "No"})
        return result

    result.update({'mdm_enrolled': "No"})
    result.update({'mdm_enrolled_via_dep': "No"})
    return result


def get_minor_os_version():
    """Returns the minor OS version."""
    os_version_tuple = platform.mac_ver()[0].split('.')
    return int(os_version_tuple[1])

def get_patch_os_version():
    """Returns the minor OS version."""
    os_version_tuple = platform.mac_ver()[0].split('.')
    return int(os_version_tuple[2])

def main():
    """Main"""
    # Create cache dir if it does not exist
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    if not os.path.exists(cachedir):
        os.makedirs(cachedir)

    # Skip manual check
    if len(sys.argv) > 1:
        if sys.argv[1] == 'manualcheck':
            print 'Manual check: skipping'
            exit(0)
    result = dict()
    if get_minor_os_version() >= 13:
        if get_patch_os_version >= 4:
            result = get_mdm_status_modern()
        else:
            result = get_mdm_status_legacy()
    else:
        result = get_mdm_status_legacy()

    # Write mdm status results to cache
    output_plist = os.path.join(cachedir, 'mdm_status.plist')
    plistlib.writePlist(result, output_plist)

if __name__ == "__main__":
    main()
