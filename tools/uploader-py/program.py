# 
#  This file is part of Anontune.
# 
#  Anontune is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
# 
#  Anontune is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero Public License for more details.
# 
#  You should have received a copy of the GNU Affero Public License
#  along with Anontune.  If not, see <http://www.gnu.org/licenses/>.
#  
#  (c) 2011 Anontune developers

import urllib2
import MultipartPostHandler
import re
import os
import sys

def url_escape(s, action):
    i = 0
    s_len = len(s)
    new = ""
    while i < s_len:
        #Encode.
        if action:
            # "7-bit ASCII alphanumerics and the characters "-._~" do not need to be escaped."
            if re.match("[-a-zA-Z0-9._~]", s[i]) != None:
                new += s[i]
            else:
                my_ord = ord(s[i])
                hex_ord = "%X" % my_ord
                new += "%" + str(hex_ord)
            i += 1
        #Decode.
        else:
            #Prevent overflow.
            if i + 2 < s_len:
                encoded = s[i:i + 3]
                if re.match("%[0-9a-fA-F][0-9a-fA-F]", encoded) != None:
                    encoded = encoded[1:]
                    encoded = int(encoded, 16)
                    new += chr(encoded)
                    i += 3
                    continue
            new += s[i]
            i += 1
                
    return new

class prog():
    def __init__(self):
        self.banner = "Anontune iPod Upload - V1.0.0"
        self.api_url = "http://anontune.com/api.php"
        self.ipod_db_path = ""
        self.auth_username = ""
        self.auth_password = ""
        self.username = ""
        
    def usage(self):
        u = \
        """
Usage:
binary anontune_username anontune_password ipod_db_path

Example:
upload_ipod_db.py test s3cr3t /mount/ipod/iPod_Control/iTunes/itunesDB"""
        return u

    def interactive(self):
        raw_input("Press enter when your Apple iPod is plugged in . . .")
        print
        self.auth_username = raw_input("Anontune Username: ")
        self.auth_password = raw_input("Anontune Password: ")
        self.ipod_db_path = raw_input("Ipod DB Path (if known): ")
        print
        self.username = self.auth_username

    def ipod_db_upload(self, path):
        try:
            self.auth_username = url_escape(self.auth_username, 1)
            self.auth_password = url_escape(self.auth_password, 1)
            self.username = url_escape(self.username, 1)
            api_call = self.api_url + "?c=upload_ipod_db" + "&username=" + self.username + "&auth_username=" + self.auth_username + "&auth_password=" + self.auth_password
            params = {'uploaded_file' : open(path, 'rb')}
            opener = urllib2.build_opener(MultipartPostHandler.MultipartPostHandler)
            urllib2.install_opener(opener)
            req = urllib2.Request(api_call, params)
            response = urllib2.urlopen(req).read().strip()
            if(response == ""):
                raise Exception("x")
            error = re.search(r"[\"]error[\"].*?[\"](.*?)[\"]", response, re.DOTALL | re.IGNORECASE)
            if error != None:
                print "> Error: \"%s\"" % (error.group(1))
                raise Exception("x")
            return "> Success.\r\n> Your database has been added to the process queue.\r\n> It may take some time before the new playlists show up on your user page.\r\n"
        except:
            return "> Failure."
            
    def find_ipod_db_path(self):
        self.ipod_db_path = ""
        print "> Brute forcing iPod paths."
        #Assume OS is Windows and brute force path.
        for i in range(97, 123):
            drive = chr(i)
            path = drive + ":\\iPod_Control\\iTunes"
            if os.path.exists(path):
                self.ipod_db_path = path
                break
        if self.ipod_db_path == "": #OS wasn't Windows; *nix then
            mount_paths = ["/media", "/mnt", "/Volumes"]
            for mount_path in mount_paths:
                if os.path.exists(mount_path):
                    devices = os.listdir(mount_path)
                    for device in devices:
                        path = mount_path + "\\" + device
                        if os.path.isdir(path):
                            path += "/iPod_Control/iTunes"
                            if os.path.exists(path):
                                self.ipod_db_path = path
                                break

        #Get database name.
        if self.ipod_db_path != "":
            print "> Found iPod path."
            print "> Searching for database."
            entities = os.listdir(self.ipod_db_path)
            for entity in entities:
                if re.match(r"^itunes[a-zA-Z0-9]*?db[a-zA-Z0-9]*?$", entity, re.IGNORECASE) != None:
                    temp = self.ipod_db_path + "/" + entity
                    if os.path.getsize(temp) > (1024):
                        self.ipod_db_path = temp
                        break
        else:
            print "> Could not find iPod path."
            
    def do(self):
        #Find ipod_db_path
        if(not os.path.isfile(self.ipod_db_path)):
            self.find_ipod_db_path()
            
        #Upload database.
        if os.path.isfile(self.ipod_db_path):
            print "> Uploading database. Please wait . . ."
            print self.ipod_db_upload(self.ipod_db_path)
        else:
            print "> Database not found."
            print "> You will have to find it yourself."
            print "> Once found, run this program again."
        print ""
        raw_input("Press enter to exit.")
            
    def main(self):
        print
        print self.banner
        print
        argc = len(sys.argv)
        argv = sys.argv
        if argc > 1 and argc != 4:
            print self.usage()
            exit()
        elif argc == 4:
            self.auth_username = argv[1]
            self.username = argv[1]
            self.auth_password = argv[2]
            self.ipod_db_path = argv[3]
        else:
            self.interactive()
        self.do()

black_box = prog()
black_box.main()
