<?php

session_start();

$conn = new mysqli(
    "127.0.0.1",
    "dm81079_z16",
    "Dawidek7003#",
    "dm81079_z16"
);

if ($conn->connect_error) {
    die("Błąd połączenia");
}

/* =========================
   LOGOWANIE
========================= */

if(
    !isset($_SESSION['loggedin_firma1']) ||
    $_SESSION['loggedin_firma1'] !== true
){
    header("Location: index.php");
    exit();
}

/* =========================
   ZAPIS SVG LOGO
========================= */

if(isset($_POST['save_svg_logo'])){

    $svgContent = trim($_POST['svg_logo']);

    // prosta walidacja
    if(
        stripos($svgContent, '<svg') !== false &&
        stripos($svgContent, '</svg>') !== false
    ){

        // folder upload
        if(!is_dir("upload")){
            mkdir("upload", 0777, true);
        }

        // zapis svg
        file_put_contents(
            "upload/logo.svg",
            $svgContent
        );

        // zapis do bazy
        $logoName = "logo.svg";

        $stmt = $conn->prepare("
            UPDATE cms
            SET logo_file = ?
            WHERE id_cms = 1
        ");

        $stmt->bind_param("s", $logoName);

        $stmt->execute();
    }

    header("Location: ?page=logo");
    exit();
}

/* =========================
   ZAPIS CKEDITOR
========================= */

if(isset($_POST['save'])){

    $content = $_POST['content'];
    $field = $_POST['field'];

    $allowed = [
        'about_company',
        'offer',
        'contact',
        'google_map_link'
    ];

    if(in_array($field, $allowed)){

        $stmt = $conn->prepare("
            UPDATE cms
            SET $field = ?
            WHERE id_cms = 1
        ");

        $stmt->bind_param("s", $content);

        $stmt->execute();
    }

    header("Location: ?page=".$_GET['page']);
    exit();
}

/* =========================
   POBRANIE CMS
========================= */

$result = $conn->query("
    SELECT *
    FROM cms
    WHERE id_cms = 1
");

$data = $result->fetch_assoc();

$page = $_GET['page'] ?? 'about';

?>

<!DOCTYPE html>
<html lang="pl">

<head>

<meta charset="UTF-8">

<title>Panel Admina</title>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<style>

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
    font-family:'Segoe UI';
}

body{
    display:flex;
    min-height:100vh;
    background:#f5f7fa;
}

/* SIDEBAR */

.sidebar{
    width:260px;
    background:#1e293b;
    color:white;
    padding:20px;
}

.sidebar h2{
    margin-bottom:20px;
}

.sidebar a{
    display:block;
    color:#cbd5e1;
    text-decoration:none;
    padding:12px;
    border-radius:10px;
    margin-bottom:6px;
    transition:0.2s;
}

.sidebar a:hover{
    background:#334155;
    color:white;
}

.logout{
    background:#ef4444;
    margin-top:15px;
    color:white !important;
    text-align:center;
}

/* MAIN */

.main{
    flex:1;
    display:flex;
    flex-direction:column;
}

/* HEADER */

.header{
    background:white;
    padding:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    display:flex;
    align-items:center;
}

.header img{
    height:70px;
}

/* SVG logo */

.header svg{
    height:70px;
    width:auto;
}

/* CONTENT */

.content{
    padding:30px;
}

.card{
    background:white;
    border-radius:18px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

h2{
    margin-bottom:20px;
}

textarea{
    width:100%;
    height:120px;
    padding:14px;
    border-radius:12px;
    border:1px solid #d1d5db;
    resize:none;
    margin-top:20px;
    font-size:15px;
}

button{
    margin-top:15px;
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#22c55e;
    color:white;
    cursor:pointer;
    transition:0.2s;
}

button:hover{
    opacity:0.9;
}

/* LOGO */

.logo-preview{
    margin-top:40px;
    background:#f8fafc;
    border-radius:14px;
    padding:25px;
    border:1px solid #e2e8f0;
}

.logo-preview svg{
    max-width:300px;
}

/* CHAT HISTORIA */

.history-box{
    background:#f8fafc;
    border-left:5px solid #1e293b;
    padding:18px;
    border-radius:12px;
    margin-bottom:18px;
}

.chat-user{
    background:#dcfce7;
    padding:12px;
    border-radius:10px;
    margin-top:10px;
}

.chat-bot{
    background:#dbeafe;
    padding:12px;
    border-radius:10px;
    margin-top:10px;
}

/* CHAT */

.chat-wrapper{
    display:flex;
    gap:30px;
    align-items:flex-start;
    margin-top:20px;
}

.chat-left{
    width:70%;
}

.chat-container{
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* pojedyncza rozmowa */

.chat-block{
    display:flex;
    flex-direction:column;
    gap:8px;
}

/* pytanie */

.user-msg{
    background:#f1f5f9;
    color:#0f172a;
    padding:14px 16px;
    border-radius:14px;
    border-left:5px solid #22c55e;
    line-height:1.5;
}

/* odpowiedź */

.bot-msg{
    background:#eff6ff;
    color:#1e3a8a;
    padding:14px 16px;
    border-radius:14px;
    border-left:5px solid #3b82f6;
    line-height:1.5;
}

/* AVATAR */

.chat-avatar{
    width:220px;
    min-width:220px;
    position:sticky;
    top:20px;
}

.chat-avatar svg{
    width:100%;
    background:white;
    border-radius:25px;
    padding:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.12);
}

.bot-head{
    animation:float 3s ease-in-out infinite;
}

.bot-eye{
    transform-box: fill-box;
    transform-origin: center;
    animation: blink 4s infinite;
}

@keyframes blink{

    0%, 44%, 100%{
        transform: scaleY(1);
    }

    46%{
        transform: scaleY(0.1);
    }

    48%{
        transform: scaleY(1);
    }
}

.bot-mouth{
    animation:talk 1s infinite;
    transform-origin:center;
}

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-8px);}
    100%{transform:translateY(0);}
}


@keyframes talk{
    0%{transform:scaleX(1);}
    50%{transform:scaleX(1.4);}
    100%{transform:scaleX(1);}
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <h2>Panel Admina</h2>

    <a href="?page=about">
        O firmie
    </a>

    <a href="?page=contact">
        Kontakt
    </a>

    <a href="?page=destination">
        Jak do nas dotrzeć?
    </a>

    <a href="?page=offer">
        Oferta
    </a>

    <a href="?page=logo">
        Edycja logo SVG
    </a>

    <a href="?page=chatbot">
        Chatbot
    </a>

    <a href="?page=chatbot_history">
        Historia Chatbota
    </a>

    <hr style="margin:20px 0; border-color:#334155;">

    <p>
        Zalogowano jako:<br>
        <b><?= $_SESSION['username_firma1'] ?></b>
    </p>

    <a href="wyloguj.php" class="logout">
        Wyloguj
    </a>

</div>

<!-- MAIN -->

<div class="main">

<!-- HEADER -->

<div class="header">

<?php

if(
    !empty($data['logo_file']) &&
    file_exists("upload/" . $data['logo_file'])
){

    $ext = pathinfo(
        $data['logo_file'],
        PATHINFO_EXTENSION
    );

    if($ext == "svg"){

        echo file_get_contents(
            "upload/" . $data['logo_file']
        );

    } else {

?>

<img src="upload/<?= $data['logo_file'] ?>">

<?php
    }

} else {

?>

<h2><?= $data['url'] ?></h2>

<?php } ?>

</div>

<!-- CONTENT -->

<div class="content">

<div class="card">

<?php

/* =========================
   CKEDITOR
========================= */

function editor($field, $data){

?>

<form method="POST">

    <textarea
        name="content"
        id="editor_<?= $field ?>"><?= htmlspecialchars($data[$field]) ?></textarea>

    <input
        type="hidden"
        name="field"
        value="<?= $field ?>">

    <button
        type="submit"
        name="save">

        Zapisz

    </button>

</form>

<script>
CKEDITOR.replace('editor_<?= $field ?>');
</script>

<?php
}

/* =========================
   O FIRMIE
========================= */

if($page == 'about'){

    echo "<h2>O firmie</h2>";

    editor('about_company', $data);
}

/* =========================
   OFERTA
========================= */

elseif($page == 'offer'){

    echo "<h2>Oferta</h2>";

    editor('offer', $data);
}

/* =========================
   KONTAKT
========================= */

elseif($page == 'contact'){

    echo "<h2>Kontakt</h2>";

    editor('contact', $data);
}

/* =========================
   MAPA
========================= */

elseif($page == 'destination'){

    echo "<h2>Mapa Google</h2>";

?>

<form method="POST">

    <textarea
        name="content"><?= htmlspecialchars($data['google_map_link']) ?></textarea>

    <input
        type="hidden"
        name="field"
        value="google_map_link">

    <button
        type="submit"
        name="save">

        Zapisz

    </button>

</form>

<?php
}

/* =========================
   SVG LOGO
========================= */

elseif($page == 'logo'){

    echo "<h2>Edycja logo SVG</h2>";

    $svgFile = "";

    if(
        !empty($data['logo_file']) &&
        file_exists("upload/" . $data['logo_file'])
    ){

        $svgFile = file_get_contents(
            "upload/" . $data['logo_file']
        );
    }

?>

<form method="POST">

    <textarea
        name="svg_logo"
        style="
            height:450px;
            font-family:monospace;
            font-size:14px;
        "
        placeholder="<svg>...</svg>"><?= htmlspecialchars($svgFile) ?></textarea>

    <button
        type="submit"
        name="save_svg_logo">

        Zapisz logo SVG

    </button>

</form>

<?php

if(!empty($data['logo_file'])){

?>

<div class="logo-preview">

    <h3>Podgląd logo</h3>

    <br>

    <?php

    echo file_get_contents(
        "upload/" . $data['logo_file']
    );

    ?>

</div>

<?php
    }
}

/* =========================
   CHATBOT TEST
========================= */


elseif($page == 'chatbot'){

    echo "<h2>Chatbot</h2>";

    if(isset($_POST['question'])){

        $question = trim($_POST['question']);

        $questionLower = mb_strtolower($question);

        $ip = $_SERVER['REMOTE_ADDR'];

        $answer = "";

        // LISTA PYTAŃ

        if($questionLower == "?" || $questionLower == "h"){

            $answer = "
            <b>Lista przykładowych pytań:</b><br><br>

            • oferta<br>
            • kontakt<br>
            • adres<br>
            • telefon<br>
            • mapa<br>
            • godziny otwarcia<br>
            • email<br>
            • cześć<br>
            • czym zajmuje się firma<br>
            • jak dojechać<br>
            • czy macie wsparcie techniczne<br>
            ";
        }

        // POWITANIA

        elseif(
            stripos($questionLower, 'cześć') !== false ||
            stripos($questionLower, 'czesc') !== false ||
            stripos($questionLower, 'hejka') !== false ||
            stripos($questionLower, 'siema') !== false ||
            stripos($questionLower, 'witam') !== false
        ){

            $answer = "Witaj! Miło Cię widzieć 😊";
        }

        // OFERTA

        elseif(
            stripos($questionLower, 'oferta') !== false ||
            stripos($questionLower, 'usługi') !== false
        ){

            $answer = $data['offer'];
        }

        // KONTAKT

        elseif(
            stripos($questionLower, 'kontakt') !== false ||
            stripos($questionLower, 'telefon') !== false ||
            stripos($questionLower, 'adres') !== false ||
            stripos($questionLower, 'email') !== false
        ){

            $answer = $data['contact'];
        }

        // MAPA

        elseif(
            stripos($questionLower, 'mapa') !== false ||
            stripos($questionLower, 'dojazd') !== false ||
            stripos($questionLower, 'jak dojechać') !== false
        ){

            $answer = "Mapa znajduje się w zakładce: Jak do nas dotrzeć.";
        }

        // GODZINY

        elseif(
            stripos($questionLower, 'godziny') !== false ||
            stripos($questionLower, 'otwarcia') !== false
        ){

            $answer = "Nasza firma działa od poniedziałku do piątku w godzinach 8:00 - 16:00.";
        }

        // WSPARCIE

        elseif(
            stripos($questionLower, 'pomoc') !== false ||
            stripos($questionLower, 'wsparcie') !== false
        ){

            $answer = "Tak, zapewniamy wsparcie techniczne dla klientów.";
        }

        // FIRMA

        elseif(
            stripos($questionLower, 'firma') !== false ||
            stripos($questionLower, 'czym się zajmujecie') !== false
        ){

            $answer = $data['about_company'];
        }

        // PODZIĘKOWANIE

        elseif(
            stripos($questionLower, 'dziękuję') !== false ||
            stripos($questionLower, 'dzieki') !== false
        ){

            $answer = "Nie ma sprawy 😊";
        }

        // POŻEGNANIE

        elseif(
            stripos($questionLower, 'pa') !== false ||
            stripos($questionLower, 'do widzenia') !== false
        ){

            $answer = "Do zobaczenia 👋";
        }

        // FALLBACKI

        else{

            $fallbacks = [

                // i. wyświechtane teksty

                "Gadał dziad do obrazu, a obraz ni razu.",

                "Nie wszystko złoto, co się świeci.",

                // ii. pytanie na pytanie

                "A Ty, jak byś odpowiedział na pytanie: <b>" .
                htmlspecialchars($question) .
                "</b> ?",

                "To ciekawe pytanie. Jak myślisz, jaka jest odpowiedź?",

                // iii. dane firmy

                "Nie znam odpowiedzi, ale dane kontaktowe firmy:<br><br>" .
                $data['contact'],

                "Nie znam odpowiedzi, ale mogę pokazać ofertę firmy:<br><br>" .
                $data['offer'],

                "Nie znam odpowiedzi, ale tutaj informacje o firmie:<br><br>" .
                $data['about_company'],

                // iv. google

                "Nie znam odpowiedzi, ale Mr Google podpowiada:<br><br>
                <a href='https://www.google.com/search?q=" .
                urlencode($question) .
                "' target='_blank'>
                Kliknij tutaj aby wyszukać w Google
                </a>",

                "Spróbuj poszukać odpowiedzi w Google:<br><br>
                <a href='https://www.google.com/search?q=" .
                urlencode($question) .
                "' target='_blank'>
                Otwórz wyszukiwarkę
                </a>",

                "To pytanie wykracza poza moją wiedzę 🤖"
            ];

            $index = $_SESSION['fallback_index'];

            $answer = $fallbacks[$index];

            $index++;

            if($index >= count($fallbacks)){
                $index = 0;
            }

            $_SESSION['fallback_index'] = $index;
        }

        // ZAPIS DO BAZY

        $stmt = $conn->prepare("
            INSERT INTO chatbot(
                id_cms,
                datetime,
                question,
                question_ip,
                answer
            )
            VALUES (?, NOW(), ?, ?, ?)
        ");

        $idcms = 1;

        $stmt->bind_param(
            "isss",
            $idcms,
            $question,
            $ip,
            $answer
        );

        $stmt->execute();

        // SESJA

        $_SESSION['chat_firma1'][] = [

            'question' => $question,
            'answer' => $answer
        ];
    }

?>

<!-- CHAT -->

<div class="chat-wrapper">

<div class="chat-left">

<div class="chat-container">

<?php

if(isset($_SESSION['chat_firma1'])){

    foreach($_SESSION['chat_firma1'] as $msg){

        echo "<div class='chat-block'>";

        echo "<div class='user-msg'>";
        echo htmlspecialchars($msg['question']);
        echo "</div>";

        echo "<div class='bot-msg'>";
        echo $msg['answer'];
        echo "</div>";

        echo "</div>";
    }
}

?>

</div>

<form method="POST">

<textarea
    name="question"
    placeholder="Zadaj pytanie..."
    required></textarea>

<button type="submit">
    Wyślij pytanie
</button>

</form>

</div>

<!-- AVATAR -->

<div class="chat-avatar">

<svg viewBox="0 0 300 300"
     xmlns="http://www.w3.org/2000/svg">

    <g class="bot-head">

        <rect
            x="70"
            y="60"
            width="160"
            height="160"
            rx="35"
            fill="#1e293b"/>

<ellipse
    class="bot-eye"
    cx="120"
    cy="120"
    rx="15"
    ry="15"
    fill="white"/>

<ellipse
    class="bot-eye"
    cx="180"
    cy="120"
    rx="15"
    ry="15"
    fill="white"/>

        <circle
            cx="120"
            cy="120"
            r="5"
            fill="#2563eb"/>

        <circle
            cx="180"
            cy="120"
            r="5"
            fill="#2563eb"/>

        <rect
            class="bot-mouth"
            x="110"
            y="170"
            width="80"
            height="14"
            rx="7"
            fill="white"/>

        <line
            x1="150"
            y1="60"
            x2="150"
            y2="20"
            stroke="#1e293b"
            stroke-width="6"/>

        <circle
            cx="150"
            cy="15"
            r="10"
            fill="#3b82f6"/>

        <text
            x="150"
            y="255"
            text-anchor="middle"
            font-size="24"
            fill="#1e293b"
            font-weight="bold">

            BOT

        </text>

    </g>

</svg>

</div>

</div>

<?php
}

/* =========================
   HISTORIA CHATBOTA
========================= */

elseif($page == 'chatbot_history'){

    echo "<h2>Historia Chatbota</h2>";

    $resultChat = $conn->query("
        SELECT *
        FROM chatbot
        ORDER BY id DESC
    ");

    if($resultChat->num_rows > 0){

        while($row = $resultChat->fetch_assoc()){

            echo "<div class='history-box'>";

            echo "<b>Data:</b> ";
            echo $row['datetime'];

            echo "<br><br>";

            echo "<b>IP:</b> ";
            echo $row['question_ip'];

            echo "<div class='chat-user'>";

            echo "<b>Pytanie:</b><br><br>";

            echo htmlspecialchars($row['question']);

            echo "</div>";

            echo "<div class='chat-bot'>";

            echo "<b>Odpowiedź:</b><br><br>";

            echo $row['answer'];

            echo "</div>";

            echo "</div>";
        }

    } else {

        echo "Brak historii rozmów.";
    }
}

?>

</div>

</div>

</div>

</body>
</html>