<html>
<head>
<title>Anontune Import</title>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30429650-1']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<center>
<div style="width:50%;">
<h1>Import Apple iPod</h1>

<h2>Tutorial!</h2>
<iframe width="560" height="315" src="http://www.youtube.com/embed/sa-05F4X8nU" frameborder="0" allowfullscreen></iframe>
<p>
The software discussed on this page will allow you to "upload" your Apple iPod's music to this website. The music itself will be played through third parties so none of your music is actually uploaded -- just information about your music. This means you won't be breaking copyright laws.
<p>
<b>This will only work with Apple iPods and your iPod will need to be mounted as a disk drive. By default iPod Classic, iPod Nano, and iPod shuffle can be mounted as a disk drive but for the iPhone, iPod Touch, and iPad you will need to go to special means. Make sure the songs you want to add are in a playlist.</b>
<p>
<h1><font color="red">Download:</font></h1><br>
<a href="http://www.anontune.com/import/ipod/windows_program.zip">Windows</a>&nbsp;&nbsp;&nbsp;<a href="http://www.anontune.com/import/ipod/linux_program.zip">Linux/Other</a>
<p>
The software is open source. All it does is upload your iPod's music database to Anontune where it is processed. The Windows binary has been generated from the Python code. Linux users will have to use the Python code directly, no hand holding, as it goes.
<p>
<h3>Read this if it didn't find the database.</h3>
Your iPod needs to be mounted as a disk drive for the software to find the database. If it is then you will have to specify the path to the database manually in the software. When you open your iPod as a disk drive you should go find something resembling iPod_Control/iTunes/itunesDB (or itunesCDB.)
<p>
<b>Make sure you DON'T rename the database. The name is highly significant to my software because it indicates which type of database it is. Also, if you know where the file is you can use the upload form bellow to import it directly.</b>
<p>
<form enctype="multipart/form-data" action="http://www.anontune.com/api.php?c=upload_ipod_db&username=<?php echo isset($_COOKIE["auth_username"]) ? urlencode($_COOKIE["auth_username"]) : ""; ?>&auth_username=<?php echo isset($_COOKIE["auth_username"]) ? urlencode($_COOKIE["auth_username"]) : ""; ?>&auth_password=<?php echo isset($_COOKIE["auth_password"]) ? urlencode($_COOKIE["auth_password"]) : ""; ?>" method="POST">
<input name="uploaded_file" type="file" /><br />
<input type="submit" value="Upload!">
</form>

<b>It's a little harder for iPod Touch/iPhone/iPad users</b>
<p>
<u><font color="red">You will need to mount your iPod as a disk drive if it is not.</font></u> There are programs out there that can do this (but I couldn't find any, lololol). I found something which allowed me to read the device's file system. For Windows I used <a href="http://www.macroplant.com/iexplorer/">iExplorer</a> to extract the database to the Desktop and then specify it's location manually in the software.
<p>
That's basically it. You just need a way to extract the itunesDB or itunesCDB (use the one which is largest in size) to a place where the software can read it. Don't expect any help with this, this is a prototype as it is. Things may be easier in the future but for now you need to be a power user to fix any problems.
<p>
<h3>Read this if it didnt't even run.</h3>
Download "<a href="http://www.anontune.com/import/ipod/msvcr71.dll">MSVCR71.dll</a>", "<a href="http://www.anontune.com/import/ipod/w9xpopen.exe">w9xpopen.exe</a>", "<a href="http://www.anontune.com/import/ipod/python25.dll">python25.dll</a>" to the same directory as the program. Also try installing <a href="http://www.microsoft.com/download/en/details.aspx?displaylang=en&id=3387">Microsoft Visual C++ 2005 Redistributable Package (x86)</a>. Try again? If it didn't work try using the Python code directly. Install <a href="http://www.python.org/getit/releases/2.5/">Python 2.5</a> press [win] + r, type "cmd" without the quotes, cd to the directory with the <a href="http://www.anontune.com/import/ipod/linux_program.zip">Python code/Linux version</a> and call it through Python.
<p>
<b>Oh yeah, If you specify the path manually make sure the database file is called either itunesDB or itunesCDB -- these names denote what type the database is! I can't even open them without that (admittedly it's trivial to add a code check for this but I'm lazy.)</b>
<p>
Remember, this is a prototype. Report bugs to the person who sent you here.<br>
<font size="12" color="red">EXPERT WEB DESIGN</font>
</div>
</body>
</html>
