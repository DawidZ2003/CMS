<?php
session_start();

if(!isset($_POST['username']) || !isset($_POST['password'])){
    die("Brak danych logowania");
}

$user = $_POST['username'];
$pass = $_POST['password'];

$link = mysqli_connect("127.0.0.1","dm81079_z16","Dawidek7003#","dm81079_z16");

if(!$link){
    die("Błąd połączenia: ".mysqli_connect_errno()." ".mysqli_connect_error());
}

mysqli_set_charset($link, "utf8");

// =====================
// LOGOWANIE ADMINA
// =====================
$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rekord = mysqli_fetch_assoc($result);

$ip = $_SERVER['REMOTE_ADDR'];

if($rekord && $rekord['password'] == $pass){

    $_SESSION['loggedin_firma3'] = true;
    $_SESSION['id_firma3'] = $rekord['id'];
    $_SESSION['username_firma3'] = $rekord['username'];
    unset($_SESSION['chat_firma3']);
    unset($_SESSION['fallback_index']);

    // =====================
    // ZAPIS LOGU LOGOWANIA
    // =====================
    $id_cms = 3;
    $datetime = date("Y-m-d H:i:s");

    $log = "INSERT INTO login_history (id_cms, datetime, username, ip_address)
            VALUES (?, ?, ?, ?)";

    $stmtLog = mysqli_prepare($link, $log);
    mysqli_stmt_bind_param($stmtLog, "isss", $id_cms, $datetime, $user, $ip);
    mysqli_stmt_execute($stmtLog);

    mysqli_stmt_close($stmtLog);

    header("Location: panel.php"); 
    exit();
}
else {
    echo "Niepoprawny login lub hasło!";
}

mysqli_close($link);
?>