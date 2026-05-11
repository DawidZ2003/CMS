<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="UTF-8">

    <title>CMS</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#0f172a,#1e293b);
        }

        .container{
            width:420px;
            background:white;
            padding:40px;
            border-radius:24px;
            box-shadow:0 15px 40px rgba(0,0,0,0.25);
            text-align:center;
        }

        .container h1{
            color:#0f172a;
            margin-bottom:10px;
            font-size:32px;
        }

        .container p{
            color:#64748b;
            margin-bottom:35px;
            font-size:15px;
        }

        .portal-link{
            display:block;
            text-decoration:none;
            background:#1e293b;
            color:white;
            padding:16px;
            border-radius:14px;
            margin-bottom:18px;
            font-size:18px;
            font-weight:600;
            transition:0.25s;
            box-shadow:0 4px 10px rgba(0,0,0,0.12);
        }

        .portal-link:hover{
            transform:translateY(-3px);
            background:#334155;
            box-shadow:0 10px 20px rgba(0,0,0,0.2);
        }

        .footer{
            margin-top:20px;
            color:#94a3b8;
            font-size:13px;
        }

    </style>

</head>

<body>

    <div class="container">

        <h1>CMS Firmowy</h1>

        <p>Wybierz portal firmy</p>

        <a href="firma1" class="portal-link">
            Portal firmy nr 1
        </a>

        <a href="firma2" class="portal-link">
            Portal firmy nr 2
        </a>

        <a href="firma3" class="portal-link">
            Portal firmy nr 3
        </a>

        <div class="footer">
            System zarządzania treścią
        </div>

    </div>

</body>

</html>