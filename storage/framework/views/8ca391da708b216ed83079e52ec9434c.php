<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="St. Matthew Academy of Cavite">
    <title>St. Matthew Academy of Cavite</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/logo.png')); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/landing.css')); ?>">
</head>
<body>
    <nav class="lm-navbar navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?php echo e(asset('assets/images/image.png')); ?>" alt="Logo" height="25 " class="me-2">
                <span class="fw-semibold">St. Matthew Academy of Cavite</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#strands">Strands</a></li>
                    <li class="nav-item"><a class="nav-link" href="#school-news">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-2"><a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-sm">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero py-6">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Inspiring Excellence, Nurturing Values</h1>
                    <p class="hero-sub">A private K–12 school in Bacoor, Cavite — modern facilities, dedicated teachers, and a caring community.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-cta btn-lg">Login</a>
                        <a href="#school-news" class="btn btn-outline-cta btn-lg">Announcements</a>
                    </div>
                    <div class="mt-3 text-muted small">
                        <i class="ti ti-map-pin"></i> Little Pasay St., Brgy. Niog 1 • <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-visual">
                        <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="campus" class="img-fluid rounded shadow-sm">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-5">
        <div class="container">
            <div class="row text-center mb-4">
                <div class="col-md-8 mx-auto">
                    <h2 class="section-title">Why Choose St. Matthew?</h2>
                    <p class="text-muted">Holistic education, caring teachers, and facilities that support learning and growth.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon bg-primary text-white"><i class="ti ti-books"></i></div>
                        <h5 class="mt-3">Holistic Curriculum</h5>
                        <p class="text-muted small">Balanced academics and character formation designed for K–12 learners.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon bg-primary text-white"><i class="ti ti-chalkboard"></i></div>
                        <h5 class="mt-3">Expert Teachers</h5>
                        <p class="text-muted small">Passionate educators committed to student success.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon bg-primary text-white"><i class="ti ti-device-laptop"></i></div>
                        <h5 class="mt-3">Modern Facilities</h5>
                        <p class="text-muted small">Labs, library, and learning spaces that encourage exploration.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="strands" class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-4">
                <div class="col-md-8 mx-auto">
                    <h3 class="section-title">Senior High School Strands</h3>
                    <p class="text-muted">Choose the track that fits your future: STEM, ABM, HUMSS, or TVL.</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="strand-card p-3 text-center h-100">
                        <div class="strand-icon bg-primary text-white mb-2"><i class="ti ti-atom"></i></div>
                        <strong>STEM</strong>
                        <div class="text-muted small">Science, Technology, Engineering, Mathematics</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="strand-card p-3 text-center h-100">
                        <div class="strand-icon bg-primary text-white mb-2"><i class="ti ti-coins"></i></div>
                        <strong>ABM</strong>
                        <div class="text-muted small">Accountancy, Business, Management</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="strand-card p-3 text-center h-100">
                        <div class="strand-icon bg-primary text-white mb-2"><i class="ti ti-users"></i></div>
                        <strong>HUMSS</strong>
                        <div class="text-muted small">Humanities & Social Sciences</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="strand-card p-3 text-center h-100">
                        <div class="strand-icon bg-primary text-white mb-2"><i class="ti ti-tools"></i></div>
                        <strong>TVL</strong>
                        <div class="text-muted small">Technical-Vocational-Livelihood</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="school-news" class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-8 mx-auto text-center">
                    <h3 class="section-title">Announcements</h3>
                    <p class="text-muted">Latest news and updates from the school.</p>
                </div>
            </div>
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-4">
                    <article class="news-card h-100 p-0 overflow-hidden">
                        <?php if($announcement->hasImage()): ?>
                        <img src="<?php echo e($announcement->image); ?>" alt="<?php echo e($announcement->title); ?>" class="news-img w-100" />
                        <?php else: ?>
                        <div class="news-img placeholder d-flex align-items-center justify-content-center">
                            <i class="ti ti-speakerphone fs-2 text-primary"></i>
                        </div>
                        <?php endif; ?>
                        <div class="p-3">
                            <h5 class="mb-1"><?php echo e($announcement->title); ?></h5>
                            <p class="text-muted small mb-2"><?php echo e(Str::limit(strip_tags($announcement->content), 120)); ?></p>
                            <?php if($announcement->published_at): ?>
                            <div class="text-muted small">Published: <?php echo e($announcement->published_at->format('F d, Y')); ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No announcements at this time.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="about" class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>About St. Matthew Academy of Cavite</h3>
                    <p class="text-muted">We provide a supportive environment where students are encouraged to explore, create, and lead. From Kindergarten to Senior High School, our programs nurture curiosity and build strong foundations.</p>
                    <ul class="text-muted">
                        <li>Science and computer laboratories</li>
                        <li>Library and learning resources</li>
                        <li>Student clubs and leadership</li>
                        <li>Community outreach programs</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card p-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-heart-handshake text-primary fs-3 me-2"></i>
                            <div>
                                <div class="fw-semibold">Admissions Assistance</div>
                                <div class="text-muted small">Email: <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="small text-muted">&copy; <?php echo e(date('Y')); ?> St. Matthew Academy of Cavite</div>
            <div class="small text-muted">Little Pasay St., Brgy. Niog 1, Bacoor • <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a></div>
        </div>
    </footer>

    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/welcome.blade.php ENDPATH**/ ?>