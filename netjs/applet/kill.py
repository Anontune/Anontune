import os
import sys
import subprocess

p =  sys.stdin.read()
lines = p.rsplit("\n")
for line in lines:
	parts = line.rsplit(" ")
	for i in range(0, len(parts)):
		if(parts[i].isdigit()):
			os.system("kill -9 " + parts[i])
	#break
	#if(len(parts) >= 8):
	#	os.system("kill -9 " + parts[7])
	#print "Line 1"
	#print parts[7]
	#print parts
#x = input()
#print x
