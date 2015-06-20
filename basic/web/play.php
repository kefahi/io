<html>
<body>
<pre>
<?php
system("/bin/whoami"); echo "---------------\n"; system("/bin/who am i");
$ret = mkdir("/home/io/mytest");

echo ("the operation returned " . ($ret?"True":"False"));
?>

</pre>

</body>
</html>
