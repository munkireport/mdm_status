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
import json
import time
from datetime import datetime, timedelta, tzinfo

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

def string_to_timestamp(time_string):
    time_string = time_string.strip()
    date_str, tz = time_string[:-6], time_string[-6:]
    dt_utc = datetime.strptime(date_str.strip(), "%Y-%m-%dT%H:%M:%S.%f") # 2023-08-17T01:21:54.555443-04:00
    dt = dt_utc.replace(tzinfo=FixedOffset(tz))
    utc_naive = dt.replace(tzinfo=None) - dt.utcoffset()
    return int((utc_naive - datetime(1970, 1, 1)).total_seconds())

class FixedOffset(tzinfo):
    """offset_str: Fixed offset in str: e.g. '-0400'"""
    def __init__(self, offset_str):
        sign, hours, minutes = offset_str[0], offset_str[1:3], offset_str[3:].replace(":","")
        offset = (int(hours) * 60 + int(minutes.replace(":",""))) * (-1 if sign == "-" else 1)
        self.__offset = timedelta(minutes=offset)
        # NOTE: the last part is to remind about deprecated POSIX GMT+h timezones
        # that have the opposite sign in the name;
        # the corresponding numeric value is not used e.g., no minutes
        '<%+03d%02d>%+d' % (int(hours), int(minutes), int(hours)*-1)
    def utcoffset(self, dt=None):
        return self.__offset
    def tzname(self, dt=None):
        return self.__name
    def dst(self, dt=None):
        return timedelta(0)
    def __repr__(self):
        return 'FixedOffset(%d)' % (self.utcoffset().total_seconds() / 60)

def main():
    """Main"""

    # Check if macOS 10.13.4 (Darwin 17.5.0) or higher
    if getFullDarwinVersion() >= 1750:
        result = get_mdm_status_modern()
    else:
        result = get_mdm_status_legacy()

    result.update(get_mdm_server_url())

    # Check if we have mdm-watchdog's state.json
    if os.path.isfile('/Library/Application Support/mdm-watchdog/state.json'):
        watchdog_state = json.loads(open('/Library/Application Support/mdm-watchdog/state.json', 'r').read())
        for item in watchdog_state:
            if item == "last_mdm_kickstart" and "0001-01-01" not in watchdog_state[item]:
                result['last_mdm_kickstart'] = string_to_timestamp(watchdog_state[item])
            elif item == "last_software_update_kickstart" and "0001-01-01" not in watchdog_state[item]:
                result['last_software_update_kickstart'] = string_to_timestamp(watchdog_state[item])

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
