<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragancias Prime - Home</title>
    <link rel="stylesheet" href="/Fragancias Prime/public/css/style.css">
</head>

<body>

    <?php
    require __DIR__ . '../../vista/layouts/header.php';
    ?>

    <section id="heroCarousel" class="hero-carousel">

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/scandal.webp"
                    alt="Fragancia Scandal"
                    class="carousel-img">
            </div>

            <div class="carousel-item">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/d&g.webp"
                    alt="Dolce & Gabbana"
                    class="carousel-img">
            </div>

            <div class="carousel-item">
                <img src="/Fragancias Prime/public/img/Carrousel-Home/swy.webp"
                    alt="Fragancia SWY"
                    class="carousel-img">
            </div>

        </div>

        <button class="carousel-control-prev">
            <span class="arrow-icon">❮</span>
        </button>
        <button class="carousel-control-next">
            <span class="arrow-icon">❯</span>
        </button>

        <div class="carousel-indicators">
            <button data-slide="0" class="active"></button>
            <button data-slide="1"></button>
            <button data-slide="2"></button>
        </div>

    </section>

    <script src="/Fragancias Prime/public/js/app.js"></script>

    <?php require_once __DIR__ . '../../vista/layouts/footer.php'; ?>

</body>

</html>