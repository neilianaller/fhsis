<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= esc($filename) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f8f9fa;
    }
    table {
      border-collapse: collapse;
      margin: 0 auto;
      background: #fff;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px 10px;
      text-align: left;
    }
    th {
      background: #e9ecef;
    }
  </style>
</head>
<body>
  <h2>📊 <?= esc($filename) ?></h2>
  <div><?= $htmlOutput ?></div>
</body>
</html>
