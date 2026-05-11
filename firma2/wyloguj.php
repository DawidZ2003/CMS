<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<BODY>
<?php
session_start();
unset($_SESSION['loggedin_firma2']);
unset($_SESSION['id_firma2']);
unset($_SESSION['username_firma2']);
unset($_SESSION['chat_firma2']);
unset($_SESSION['fallback_index']);
header('Location: index.php');
exit();
?>
</BODY>
</html>