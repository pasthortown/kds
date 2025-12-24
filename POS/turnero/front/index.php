<!DOCTYPE html><html lang="en"><head>
  <meta charset="utf-8">
  <title>Turnero</title>

<?php $response = file_get_contents('http://localhost:8090/credencial');
$response = json_decode($response);

$hostFormat = strpos($response->servidor, ',');

if($hostFormat){
    $url=explode(",",$response->servidor);
    $ip_servidor=$url[0];
}else{
    $url=explode("\\",$response->servidor);
    $ip_servidor=$url[0];
}

 ?>
  <base href="http://<?php echo $ip_servidor; ?>:880/pos/turnero/front/">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script src="https://code.responsivevoice.org/responsivevoice.js?key=fc8tH3A4"></script>
<link rel="stylesheet" href="styles.ef46db3751d8e999.css"></head>
<body>
  <app-root></app-root>
<script src="runtime.d664f5225170fcb0.js" type="module"></script><script src="polyfills.e59d8183086d088b.js" type="module"></script><script src="main.9337c302565d62a1.js" type="module"></script>

</body></html>
