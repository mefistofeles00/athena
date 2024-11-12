<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Athena Framework'; ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<?php require VIEW_PATH . '/partials/header.php'; ?>

<main class="container">
    <?php echo $content; ?>
</main>

<?php require VIEW_PATH . '/partials/footer.php'; ?>

<script src="/assets/js/app.js"></script>
</body>
</html>