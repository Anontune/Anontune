#Compile.
cd /srv/www/htdocs/anontune/trunk/netjs
rm -rf *.class
javac -classpath "/usr/share/icedtea-web/plugin.jar" netjs.java Sock.java ApiCall.java Tran.java
#Zip
rm -rf netjs.zip
rm -rf netjs.jar
zip -b . netjs ApiCall.class
zip -b . netjs netjs.class
zip -b . netjs Sock.class
zip -b . netjs Tran.class
cp netjs.zip netjs.jar
rm -rf /srv/www/htdocs/anontune/trunk/public_html/netjs/netjs.jar
#Sign applet
#rm -rf myKeyStore
keytool -genkey -keystore myKeyStore -alias me
keytool -selfcert -keystore myKeyStore -alias me
jarsigner -keystore myKeyStore netjs.jar me
cp netjs.jar /srv/www/htdocs/anontune/trunk/public_html/netjs/netjs.jar
#rm -rf /srv/www/htdocs/javad/*
#cp netjs.jar /srv/www/htdocs/javad/netjs.jar
#cp test.html /srv/www/htdocs/javad/test.html
#Kill firefox.
ps aux | grep firefox | python kill.py
#Restart that bitch.
#/etc/init.d/apache2 restart
firefox /srv/www/htdocs/anontune/trunk/public_html/lab/me_test.html
