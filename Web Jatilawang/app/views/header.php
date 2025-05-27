<!-- app/views/header.php -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Jatilawang Adventure</title>
  <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
  <header>
    <h1>Jatilawang Adventure</h1>
    <nav>
      <a href="index.php">Produk</a> |
      <a href="keranjang.php">Keranjang</a> |
      <?php if (isset($_SESSION['user'])): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </nav>
    <hr>
  </header>