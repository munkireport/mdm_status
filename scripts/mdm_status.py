#!/usr/local/munkireport/munkireport-python3

"""
MDM status reporting tool. Requires macOS 10.13.4+ (Darwin 17.5.0) for command output compatibility.
"""

import subprocess
import os
import plistlib
import sys
import platform
import re

def get_mdm_server_url():
    """Uses profiles command to detect the MDM server hostname."""
    cmd = ['/usr/bin/profiles', '-C', '-o', 'stdout-xml']
    run = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    output, err = run.communicate()

    mdm_server_url = ''
    try:
        try:
            plist = plistlib.readPlistFromString(output)
        except AttributeError as e:
            plist = plistlib.loads(output)
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
                    try:
                        mdm_full_server_url = item_content['PayloadContent']['ServerURL']
                        mdm_server_url = re.match('http.?:\/\/[\S]+(?=\/)', mdm_full_server_url).group()
                    except KeyError:
                        mdm_server_url = ''
    except KeyError:
        mdm_server_url = ''

    result = {'mdm_server_url': mdm_server_url}
    return result

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
    values = output.decode().split('\n')
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

    dep_cmd = ['/usr/bin/profiles', '-e']
    dep_proc = subprocess.Popen(dep_cmd, shell=False, bufsize=-1,
                            stdin=subprocess.PIPE,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    (dep_output, unused_error) = dep_proc.communicate()

    mdm_enrolled_via_dep = ''
    mdm_enrollment = ''
    try:
        try:
            plist = plistlib.readPlistFromString(output)
        except AttributeError as e:
            plist = plistlib.loads(output)
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
                    mdm_enrolled = "Yes (User Approved)"
    except KeyError:
        mdm_enrolled =  "No"
        mdm_enrolled_via_dep = "No"

    try:
        if "ConfigurationURL" in dep_output.decode():
            mdm_enrolled_via_dep = "Yes"
        else: 
            mdm_enrolled_via_dep = "Yes"
    except:
            mdm_enrolled_via_dep = "No"
            
    result = {}
    result.update({'mdm_enrolled': mdm_enrolled})
    result.update({'mdm_enrolled_via_dep': mdm_enrolled_via_dep})
    return result

def getFullDarwinVersion():
    """Returns the Darwin version."""
    # Catalina -> 10.15.7 -> 19.6.0 -> 1960
    darwin_version_tuple = platform.release().replace(".","")
    return int(darwin_version_tuple) 

def main():
    """Main"""

    # Check if macOS 10.13.4 (Darwin 17.5.0) or higher
    if getFullDarwinVersion() >= 1750:
        result = get_mdm_status_modern()
    else:
        result = get_mdm_status_legacy()
    
    result.update(get_mdm_server_url())
    
    # Write mdm status results to cache
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    output_plist = os.path.join(cachedir, 'mdm_status.plist')
    try:
        plistlib.writePlist(result, output_plist)
    except:
        with open(output_plist, 'wb') as fp:
            plistlib.dump(result, fp, fmt=plistlib.FMT_XML)

if __name__ == "__main__":
    main()
