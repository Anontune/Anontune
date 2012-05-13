import gpod
import re
import os
import php_lib
import urllib2
import httplib
import threading
import time
import config
import logging
import random
import time
import shutil
import types
import socket
import sys
import _mysql

def get_lock(process_name):
	global lock_socket
	lock_socket = socket.socket(socket.AF_UNIX, socket.SOCK_DGRAM)
	try:
		lock_socket.bind('\0' + process_name)
	except socket.error:
		print 'lock exists'
		sys.exit()

config.config.anontune.users.administrator.username

logging.basicConfig(level=logging.WARNING, filename="error_log")
log = logging.getLogger('mai_logger')
the_mutex = threading.Lock()
try:
	db = _mysql.connect(config.config.database.host, config.config.database.username, config.config.database.password, "anontune2")
except:
	print "Failed to connect to database server."
	exit()


def insert_playlist(name, username):
    global the_mutex
    if the_mutex == None:
        return
    try:
        name = php_lib.url_escape(str(name), 1)
        username = php_lib.url_escape(username, 1)
        auth_username = php_lib.url_escape(str(config.config.anontune.users.administrator.username), 1)
        auth_password = php_lib.url_escape(str(config.config.anontune.users.administrator.password), 1)  
        api_call  = "http://www.anontune.com/api.php?c=insert_playlist&username=%s&auth_username=%s&auth_password=%s&name=%s&parent_id=0&cmd=0" % (username, auth_username, auth_password, name)



		sql_query = "SELECT * FROM `playlist WHERE `Thumbnail` = '%s'" % s_thumbnail


        req = urllib2.Request(api_call)
        response = urllib2.urlopen(req, None)
        response = response.read()
        #print response
        x = re.search(r"id.*?([0-9]+).*", response, re.DOTALL | re.IGNORECASE)
        if x != None:
            return x.group(1)
        else:
            return "0"

    except:
        return "0"
    #except (urllib2.URLError, httplib.BadStatusLine, IndexError), e:

def insert_track(track, username, playlist_id):
    global the_mutex
    
    if track == None or the_mutex == None:
        return

    try:
        title = php_lib.url_escape(str(track["title"]), 1) if track["title"] != None else ""
        artist = php_lib.url_escape(str(track["artist"]), 1) if track["artist"] != None else ""
        album = php_lib.url_escape(str(track["album"]), 1) if track["album"] != None else ""
        genre = php_lib.url_escape(str(track["genre"]), 1) if track["genre"] != None else ""

        time_added = php_lib.url_escape(str(int(time.mktime(track["time_added"].timetuple()))), 1) if track["time_added"] != None else ""
        time_played = php_lib.url_escape(str(int(time.mktime(track["time_played"].timetuple()))), 1) if track["time_played"] != None else ""
        time_skipped = php_lib.url_escape(str(track["last_skipped"]), 1) if track["last_skipped"] != None else ""
        play_count = php_lib.url_escape(str(track["playcount"]), 1) if track["playcount"] != None else "" #
        skip_count = php_lib.url_escape(str(track["skipcount"]), 1) if track["skipcount"] != None else "" #
        rating = php_lib.url_escape(str(track["rating"]), 1) if track["rating"] != None else "" #
        year = php_lib.url_escape(str(track["year"]), 1) if track["year"] != None else "" #
        username = php_lib.url_escape(username, 1)
        playlist_id = php_lib.url_escape(playlist_id, 1)
        auth_username = php_lib.url_escape(str(config.config.anontune.users.administrator.username), 1)
        auth_password = php_lib.url_escape(str(config.config.anontune.users.administrator.password), 1)  
        
        api_call  = "http://www.anontune.com/api.php?c=insert_track&username=%s&auth_username=%s&auth_password=%s&title=%s&artist_name=%s&playlist_id=%s&album_title=%s&genre=%s&year=%s&time_played=%s&play_count=%s&skip_count=%s&time_skipped=%s&time_added=%s&rating_amount=%s" % (username, auth_username, auth_password, title, artist, playlist_id, album, genre, year, time_played, play_count, skip_count, time_skipped, time_added, rating)
        req = urllib2.Request(api_call)
        response = urllib2.urlopen(req, None)
        #print response
    except:
        return


    #print
    #print response.read()
    #print
    #except (urllib2.URLError, httplib.BadStatusLine, IndexError), e:
    
def main():
    corrupt_path = str(config.config.anontune.home_directory) + "/" + str(config.config.anontune.ipod_db.upload_directory) + "/corrupt_db"
    while 1:
	time.sleep(1)
        timestamp = str(int(time.time()))
        copy_path = ""
        search_path = ""
        try:
            os.chdir(str(config.config.anontune.home_directory))
            process_queue = os.listdir(str(config.config.anontune.home_directory) + "/" + str(config.config.anontune.ipod_db.upload_directory))
            if len(process_queue) <= 1:
                time.sleep(1) #Avoid killing the CPU.
            for username in process_queue:
                """
                Unfortunately, the localdb function doesn't work for all databases apparently.
                What works is creating a fake iPod directory structure, copying the databases
                there and then telling gpod the "mount" directory. So this will have to be
                done for all users.
                """
                #Skip hidden directories.
                if username[0] == ".":
                    continue
                #Skip special directories.
                if username == "corrupt_db":
                    continue
                
                print "> Now processing " + username + "'s iPod."
                
                #Mount ipod DB.
                search_path = str(config.config.anontune.home_directory) + "/" + str(config.config.anontune.ipod_db.upload_directory) + "/" + username
                copy_path = corrupt_path + "/" + username + "_" + timestamp
                ipod_db_path = search_path
                #print "1:", ipod_db_path
                try:
                    #print "a"
                    ipod_db = gpod.Database(ipod_db_path)
                    #print "B"
                    #Ugly code so it works on different ver of py + gpod
                    try:
                        print ipod_db
                        if ipod_db == None or type(ipod_db) == types.NoneType:
                            raise IndexError('')
                    except:
                        raise IndexError('')
                except:
                    try:
                        #print "d"
                        #Try use localdb.
                        ipod_db_path += "/iPod_Control/iTunes"
                        entities = os.listdir(ipod_db_path)
                        for entity in entities:
                            test_path = ipod_db_path + "/" + entity
                            if os.path.isfile(test_path):
                                ipod_db_path = test_path
                                found = 1
                                break
                        #print "e"
                        ipod_db = gpod.Database(localdb=ipod_db_path)
                        #print ipod_db_path
                        #print "f"
                        try:
                            print ipod_db
                            if ipod_db == None or type(ipod_db) == types.NoneType:
                                raise IndexError('')
                        except:
                            raise IndexError('')
                    except:
                            e_msg = "> Failed to open DB for user \"" + username + "\"."
                            print e_msg
                            log.exception(e_msg)
                            if os.path.exists(search_path):
                                shutil.copytree(search_path, copy_path)
                                shutil.rmtree(search_path)
                            exit()
                      
                #Insert music.
                #print "h0h0h0"
                #print ipod_db
                playlist_no = 0
                try:
                    libgpod_playlists = ipod_db.get_playlists()
                    print libgpod_playlists
                    if libgpod_playlists == None or type(libgpod_playlists) == types.NoneType:
                        raise IndexError('')
                except:
                    #No playlists.
                    ipod_db.new_Playlist(ipod_db, title="Anontune Songs") #One playlist to hold all songs.
                    libgpod_playlists = ipod_db.get_playlists() #Get reference to new playlist object.
                    for libgpod_playlist in libgpod_playlists:
                        for track in ipod_db:
                            libgpod_playlist.add(track) #Add all the tracks in the DB to new playlist.
                            
                playlists = []
                for libgpod_playlist in libgpod_playlists:
                    playlist_no += 1
                for libgpod_playlist in libgpod_playlists:
                    #Skip empty playlists.
                    if len(libgpod_playlist) == 0:
                        continue
                    #Skip playlists containing all songs (duplication.)
                    if playlist_no != 1 and len(libgpod_playlist) == len(ipod_db):
                        continue
                    playlist_name = str(libgpod_playlist.get_name())
                    tracks = []
                    for track in libgpod_playlist:
                        tracks.append(track)
                    playlists.append([playlist_name, tracks])
                if playlists == []:
                    playlist_name = "Anontune Songs"
                    tracks = []
                    for track in ipod_db:
                        tracks.append(track)
                    playlists.append([playlist_name, tracks])
                    
                #Add all if required.
                """
                found_all = false
                for playlist in playlists:
                    if playlist[0] == "Anontune Songs":
                        found_all = true
                if found_all == false:
                    tracks = []
                    playlist_name = "Anontune Songs"
                    for track in ipod_db:
                        tracks.append(track)
                    playlists.append([playlist_name, tracks])
                """
                
                #Playlist holding songs not in a playlist.
                not_in_playlist = []
                for ipod_track in ipod_db:
                    found = 0
                    for playlist in playlists:
                        for playlist_track in playlist[1]:
                            if playlist_track["title"] == ipod_track["title"] and playlist_track["artist"] == ipod_track["artist"]:
                                found = 1
                    if found == 0:
                        not_in_playlist.append(ipod_track)
                if not_in_playlist != []:
                    playlist = ["Not In Playlist", not_in_playlist]
                    playlists.append(playlist)
                    
                for playlist in playlists:
                    playlist_name = str(playlist[0])
                    #print playlist_name
                    playlist_id = insert_playlist(playlist_name, username)
                    #print "> Playlist: " + playlist_name
                    #print playlist_id
                    if int(playlist_id) == 0 or playlist_id == "" or playlist_id == None:
                        continue
                    for track in playlist[1]:
                        #print "Track: " + str(track["title"]) + " - " + str(track["artist"])
                        insert_track(track, username, playlist_id)
                        
                #Remove user database.
                shutil.rmtree(search_path)
                print "> Done processing " + username + "'s iPod."
                #Patch for weird error
                #Make sure auto_restart is running.
                exit()
        except Exception, err:
            the_error = "> Unknown exception occured while processing database for %s. The exception was %s." % (username, err)
            print the_error
            log.exception(the_error)
            #print the_error
            if os.path.exists(search_path):
                shutil.copytree(search_path, copy_path)
                shutil.rmtree(search_path)
                
            thread = threading.Thread(target=main, args=())
            thread.start()
            exit()
     
get_lock("import_ipod")       
main()

#Stop the program from closing if the main function
#returns.
while 1:
    time.sleep(1)
