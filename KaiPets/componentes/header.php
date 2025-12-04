<?php
$usuario = $_SESSION['usuario'] ?? null;
?>

<nav class="navbar navbar-expand-lg kai-header shadow-sm">
    <div class="container">

        <!-- LOGO + TÍTULO -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="/KaiPets/index.php">
            <img src="/KaiPets/src/img/logo_1.png" alt="LOGO" height="65">
            <span class="kai-title">Kai Pets</span>
        </a>

        <!-- BOTÓN RESPONSIVE -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuHeader">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="menuHeader">

            <!-- IZQUIERDA -->
            <ul class="navbar-nav">

                <!-- SERVICIOS -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Servicios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/KaiPets/pags/servicio.php?id=1">Paseo de perros</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/pags/servicio.php?id=2">Canguro</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/pags/servicio.php?id=3">Alojamiento</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/pags/servicio.php?id=4">Cuidado matinal</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/pags/servicio.php?id=5">Cuidados adicionales</a></li>
                    </ul>
                </li>

                <!-- CIUDADES -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Ciudades
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/KaiPets/index.php?ciudad_id=1">Málaga</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/index.php?ciudad_id=2">Marbella</a></li>
                        <li><a class="dropdown-item" href="/KaiPets/index.php?ciudad_id=3">Fuengirola</a></li>

                    </ul>
                </li>

            </ul>

            <!-- DERECHA -->
            <div class="d-flex">

                <?php if ($usuario): ?>
                    <a href="/KaiPets/pags/perfil.php" class="btn btn-kai-green btn-round ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-user-round-check">
                            <path d="M2 21a8 8 0 0 1 13.292-6" />
                            <circle cx="10" cy="8" r="5" />
                            <path d="m16 19 2 2 4-4" />
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="/KaiPets/php/login.php" class="btn btn-kai-brown btn-round ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="#fff" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21v-1a7 7 0 0 1 14 0v1" />
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($usuario) && isset($usuario['cuidador']) && $usuario['cuidador'] == 0): ?>
                    <a href="/KaiPets/php/form_cuidador.php" class="btn btn-kai-green ms-2">
                        Hazte cuidador!!
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>