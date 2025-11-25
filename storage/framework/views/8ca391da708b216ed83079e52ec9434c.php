<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="St. Matthew Senior High School">
    <title>St. Matthew Academy of Cavite</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/image.png')); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <!-- Main CSS (from admin template) -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <!-- Landing overrides (corporate style) -->
    <link rel="stylesheet" href="<?php echo e(asset('css/landing.css')); ?>">
</head>
<body class="corporate-landing">

    <!-- Top Navbar (Corporate) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-white corporate-nav shadow-sm sticky-top">
        <div class="container px-lg-5">
            <a class="navbar-brand d-flex align-items-center text-dark" href="#">
                <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="Logo" height="50" class="me-2 logo-img" style="max-height: 50px; width: auto;">
                <div class="brand-text">
                    <div class="brand-name">St. Matthew Academy</div>
                    <div class="brand-sub">of Cavite</div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="#mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link text-muted" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="#features">Programs</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="#school-news">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="#contact">Contact</a></li>
                </ul>
                <div class="ms-lg-3 d-flex gap-2">
                    <a href="<?php echo e(route('login')); ?>" class="btn bg-info btn-sm fw-semibold rounded-pill px-4">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero (Corporate) - centered, image removed -->
    <header class="hero-section py-6">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 text-center">
                    <h1 class="hero-title">Inspiring Excellence, Nurturing Values</h1>
                    <p class="hero-lead">A forward-looking K–12 school with modern facilities, expert faculty, and a supportive community in Bacoor, Cavite.</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <a href="<?php echo e(route('login')); ?>" class="btn bg-info btn-lg rounded-pill px-4">Get Started</a>
                        <a href="#contact" class="btn btn-outline-primary btn-lg rounded-pill px-4">Contact Us</a>
                    </div>
                    <ul class="hero-meta list-unstyled mt-4 d-inline-block text-start" style="max-width:480px;">
                        <li><strong>Location:</strong> Little Pasay St., Brgy. Niog 1, Bacoor</li>
                        <li><strong>Email:</strong> <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Features -->
    <section id="features" class="py-5 bg-white bg-opacity-10 border-bottom border-info rounded-bottom-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 text-center"> 
                    <h3 class="fw-bold mb-2 text-black">Why St. Matthew Academy of Cavite is The Best Choice? </h3>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-info shadow-sm rounded-4">
                        <div class="card-body bg-white rounded-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="avatar avatar-md rounded-circle bg-info text-white me-2"><i class="ti ti-books"></i></span>
                                <h5 class="mb-0" style="color:#313131">Holistic Curriculum</h5>
                            </div>
                            <p class="mb-0 text-muted">Balanced academics and character formation designed for K-12 learners.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-info shadow-sm rounded-4">
                        <div class="card-body bg-white rounded-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="avatar avatar-md rounded-circle bg-info text-white me-2"><i class="ti ti-chalkboard"></i></span>
                                <h5 class="mb-0" style="color:#313131">Expert Teachers</h5>
                            </div>
                            <p class="mb-0 text-muted">Passionate educators dedicated to student growth and success.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-info shadow-sm rounded-4">
                        <div class="card-body bg-white rounded-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="avatar avatar-md rounded-circle bg-info text-white me-2"><i class="ti ti-building-warehouse"></i></span>
                                <h5 class="mb-0" style="color:#313131">Modern Facilities</h5>
                            </div>
                            <p class="mb-0 text-muted">Safe campus with labs, library, and spaces for activities and growth.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Offered Strands Section -->
    <section id="strands" class="py-5 bg-white border-top">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="fw-bold mb-2" style="color:#313131">Senior High School Strands</h3>
                    <p class="text-muted">Explore the academic tracks offered at St. Matthew Academy of Cavite.</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center align-items-center">
                <div class="col-12 col-lg-10 mx-auto">
                    <div class="d-flex flex-column flex-md-column flex-lg-column gap-4 flex-wrap">
                        <!-- STEM-->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between bg-info bg-gradient bg-opacity-10 border border-info border-3 rounded-4 shadow-lg py-3 px-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-info text-white me-3 shadow-lg" style="width:70px;height:70px;font-size:2rem;"><i class="ti ti-atom"></i></span>
                                <h4 class="mb-0 fw-bold text-info" style="min-width:80px;">STEM</h4>
                            </div>
                            <span class="text-muted fs-3 fw-semibold">Science, Technology, Engineering, and Mathematics</span>
                        </div>
                        <!-- ABM-->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between flex-md-row-reverse bg-info bg-gradient bg-opacity-10 border border-info border-3 rounded-4 shadow-lg py-3 px-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0 flex-md-row-reverse">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-info text-white ms-3 shadow-lg" style="width:70px;height:70px;font-size:2rem;"><i class="ti ti-coins"></i></span>
                                <h4 class="mb-0 fw-bold text-info me-3" style="min-width:80px;">ABM</h4>
                            </div>
                            <span class="text-muted fs-3 fw-semibold">Accountancy, Business, and Management</span>
                        </div>
                        <!-- HUMSS-->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between bg-info bg-gradient bg-opacity-10 border border-info border-3 rounded-4 shadow-lg py-3 px-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-info text-white me-3 shadow-lg" style="width:70px;height:70px;font-size:2rem;"><i class="ti ti-users"></i></span>
                                <h4 class="mb-0 fw-bold text-info" style="min-width:80px;">HUMSS</h4>
                            </div>
                            <span class="text-muted fs-3 fw-semibold">Humanities and Social Sciences</span>
                        </div>
                        <!-- TVL-->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between flex-md-row-reverse bg-info bg-gradient bg-opacity-10 border border-info border-3 rounded-4 shadow-lg py-3 px-3">
                            <div class="d-flex align-items-center mb-2 mb-md-0 flex-md-row-reverse">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-info text-white ms-3 shadow-lg" style="width:70px;height:70px;font-size:2rem;"><i class="ti ti-device-laptop"></i></span>
                                <h4 class="mb-0 fw-bold text-info me-3" style="min-width:80px;">TVL</h4>
                            </div>
                            <span class="text-muted fs-3 fw-semibold me-2">Technical-Vocational-Livelihood <span class="badge bg-info text-white ms-2">ICT</span><span class="badge bg-info text-white ms-1">HE</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- School News & Updates Section -->
    <section id="school-news" class="py-5 bg-white bg-opacity-10 border-top">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="fw-semibold mb-2 text-black">Announcements</h3>
                    <p class="text-muted">Stay up to date with the latest happenings and achievements at St. Matthew Academy of Cavite.</p>
                </div>
            </div>
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-4">
                    <div class="card border-info shadow-sm rounded-4 h-100">
                        <?php if($announcement->hasImage()): ?>
                        <img src="<?php echo e($announcement->image); ?>" class="card-img-top rounded-top-4" style="height:180px;object-fit:cover;" alt="<?php echo e($announcement->title); ?>">
                        <?php else: ?>
                        <div class="card-img-top rounded-top-4 bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="height:180px;">
                            <i class="ti ti-speakerphone text-info" style="font-size: 4rem;"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title" style="color:#313131"><?php echo e($announcement->title); ?></h5>
                            <p class="card-text text-muted"><?php echo e(Str::limit($announcement->content, 120)); ?></p>
                            <?php if($announcement->published_at): ?>
                            <span class="badge bg-info bg-opacity-25 text-info">
                                <i class="ti ti-calendar me-1"></i><?php echo e($announcement->published_at->format('F d, Y')); ?>

                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ti ti-speakerphone text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">No announcements at this time. Check back soon!</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-5 bg-white border-top">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-3">About St. Matthew Academy of Cavite</h3>
                    <p class="text-black">We provide a supportive environment where students are encouraged to explore, create, and lead. From Kindergarten to Senior High School, our programs nurture curiosity and build strong foundations.</p>
                    <ul class="text-muted mb-0">
                        <li>Science and computer laboratories</li>
                        <li>Library and learning resources</li>
                        <li>Student clubs and leadership</li>
                        <li>Community outreach programs</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-heart-handshake text-danger fs-3 me-2"></i>
                                <div>
                                    <div class="fw-semibold">Admissions Assistance</div>
                                    <div class="text-muted small">Email us at <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer / Contact -->
    <footer id="contact" class="py-4 bg-white border-top">
        <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <div class="text-muted small mb-2 mb-lg-0">
                &copy; <?php echo e(date('Y')); ?> St. Matthew Academy of Cavite
            </div>
            <div class="text-muted small">
                <i class="ti ti-map-pin me-1"></i> Little Pasay St., Brgy. Niog 1, Bacoor •
                <i class="ti ti-mail me-1 ms-2"></i> <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/feather.min.js')); ?>"></script>
    <script>
        window.addEventListener('load', () => { if (window.feather) feather.replace(); });
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/welcome.blade.php ENDPATH**/ ?>