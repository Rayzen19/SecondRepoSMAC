<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="St. Matthew Senior High School">
    <title>St. Matthew Academy of Cavite</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/image.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- Main CSS (from admin template) -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('assets/images/image.png') }}" alt="Logo" height="25 " class="me-2">
                <span class="fw-semibold">St. Matthew Academy of Cavite</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <div class="ms-lg-3 d-flex gap-2">
                    <a href="/admin" class="btn btn-info text-white fw-semibold shadow-sm rounded-pill px-4"><i class="ti ti-lock me-1"></i> Admin Portal</a>
                    <a href="/student" class="btn btn-outline-info disabled fw-semibold rounded-pill px-4" aria-disabled="true"><i class="ti ti-users me-1"></i> Student Portal</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="py-5 py-lg-6 bg- bg-opacity-10 border-bottom border-info rounded-bottom-4">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h1 class="display-5 fw-bold mb-3" style="color:#313131">Inspiring Excellence, Nurturing Values</h1>
                    <p class="lead text-black">Private, non-sectarian K-12 school in Bacoor, Cavite. Modern facilities, dedicated teachers, and an inclusive community.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="/admin" class="btn btn-info btn-lg text-white fw-semibold shadow-sm rounded-pill px-4"><i class="ti ti-layout-navbar me-1"></i> Go to Admin</a>
                        <a href="#contact" class="btn btn-outline-info btn-lg fw-semibold rounded-pill px-4"><i class="ti ti-mail me-1"></i> Contact Us</a>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-info bg-opacity-25 text-info me-2"><i class="ti ti-map-pin me-1"></i> Little Pasay St., Brgy. Niog 1, Bacoor, Cavite</span>
                        <span class="badge bg-info bg-opacity-25 text-info"><i class="ti ti-mail me-1"></i> <a href="mailto:stmatthew2015@yahoo.com" class="text-info">stmatthew2015@yahoo.com</a></span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-info shadow-lg rounded-4">
                        <img src="https://scontent.fmnl40-2.fna.fbcdn.net/v/t39.30808-6/506345504_1314140270713666_6764660474252277220_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeF7EWPvW7h1B1iaXLx3QtAfWncKOGg8oBJadwo4aDygErvoPblQx2XoQCRfxZ32P2YZvfhqjSZLLdkcwau8p177&_nc_ohc=HfaTQaR2tOAQ7kNvwEjRHPr&_nc_oc=Admv-JK4W3A2qfrhtmXmvyz-1yiWYOMmfFZdjxZnUnkS7KJRBLpkF69uk5j9gAXedK0&_nc_zt=23&_nc_ht=scontent.fmnl40-2.fna&_nc_gid=saPqWWb9gwO8HhfHDS0iaQ&oh=00_AfZ8xa8oLl1NJcnfUIsmnOGMMlZx5qhg0b2eVpTbQ3noig&oe=68E2EDAC" alt="Campus">
                        <div class="card-body bg-info bg-opacity-10 rounded-bottom-4">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-building-community text-info fs-3 me-2"></i>
                                <div>
                                    <div class="fw-semibold text-info">St. Matthew Academy of Cavite</div>
                                    <div class="text-info small">A community of learners and leaders</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                @forelse($announcements as $announcement)
                <div class="col-md-4">
                    <div class="card border-info shadow-sm rounded-4 h-100">
                        @if($announcement->hasImage())
                        <img src="{{ $announcement->image }}" class="card-img-top rounded-top-4" style="height:180px;object-fit:cover;" alt="{{ $announcement->title }}">
                        @else
                        <div class="card-img-top rounded-top-4 bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="height:180px;">
                            <i class="ti ti-speakerphone text-info" style="font-size: 4rem;"></i>
                        </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title" style="color:#313131">{{ $announcement->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($announcement->content, 120) }}</p>
                            @if($announcement->published_at)
                            <span class="badge bg-info bg-opacity-25 text-info">
                                <i class="ti ti-calendar me-1"></i>{{ $announcement->published_at->format('F d, Y') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ti ti-speakerphone text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3">No announcements at this time. Check back soon!</p>
                    </div>
                </div>
                @endforelse
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
                &copy; {{ date('Y') }} St. Matthew Academy of Cavite
            </div>
            <div class="text-muted small">
                <i class="ti ti-map-pin me-1"></i> Little Pasay St., Brgy. Niog 1, Bacoor •
                <i class="ti ti-mail me-1 ms-2"></i> <a href="mailto:stmatthew2015@yahoo.com">stmatthew2015@yahoo.com</a>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script>
        window.addEventListener('load', () => { if (window.feather) feather.replace(); });
    </script>
</body>
</html>