<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Fragancias Prime - Home</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body>

    <?php
    require __DIR__ . '../../vista/layouts/header.php';
    ?>

    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">

        <!-- puntos -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <div id="heroCarousel" class="carousel slide full-carousel" data-bs-ride="carousel">

            <!-- indicadores -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <img src="img/Carrousel-Home/scandal.webp"
                        class="d-block w-100 carousel-img"
                        alt="Fragancias 1">
                </div>

                <div class="carousel-item">
                    <img src="img/Carrousel-Home/d&g.webp"
                        class="d-block w-100 carousel-img"
                        alt="Fragancias 2">
                </div>

                <div class="carousel-item">
                    <img src="img/Carrousel-Home/swy.webp"
                        class="d-block w-100 carousel-img"
                        alt="Fragancias 3">
                </div>

            </div>

            <!-- Flechas -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>

        <!-- marcas -->
        <div class="carousel-marca-container">

            <button class="carousel-marca-btn carousel-marca-btn-prev" id="carouselMarcaPrev">&#10094;</button>

            <div class="carousel-marca-track" id="carouselMarcaTrack">

                <?php foreach ($ProductoModelo as $prod): ?>
                    <div class="carousel-marca-slide">

                        <div class="carousel-marca-img">
                            <img src="/public/imagenes/<?= htmlspecialchars($prod['imagen']) ?>"
                                alt="<?= htmlspecialchars($prod['nombre']) ?>">
                        </div>

                        <div class="carousel-marca-info">
                            <h2 class="carousel-marca-titulo"><?= htmlspecialchars($prod['nombre']) ?></h2>
                            <p class="carousel-marca-desc"><?= htmlspecialchars($prod['descripcion']) ?></p>

                            <p class="carousel-marca-precio">
                                $<?= number_format($prod['precio'], 0, ',', '.') ?>
                            </p>

                            <a href="index.php?controlador=producto&accion=ver&id=<?= $prod['id'] ?>"
                                class="carousel-marca-btn-ver">
                                Ver producto
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

            <button class="carousel-marca-btn carousel-marca-btn-next" id="carouselMarcaNext">&#10095;</button>

        </div>



        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>

</html>

<?php require_once __DIR__ . '../../vista/layouts/footer.php'; ?>