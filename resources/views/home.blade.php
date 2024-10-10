@extends('layouts.app')

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

<div class="card-body">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif

    {{ __('You are logged in!') }}
</div>
</div>
</div>
</div>
</div> --}}

<!--====== PRELOADER PART START ======-->

<div class="preloader">
    <div class="loader">
        <div class="ytp-spinner">
            <div class="ytp-spinner-container">
                <div class="ytp-spinner-rotator">
                    <div class="ytp-spinner-left">
                        <div class="ytp-spinner-circle"></div>
                    </div>
                    <div class="ytp-spinner-right">
                        <div class="ytp-spinner-circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--====== PRELOADER PART ENDS ======-->

<!--====== NAVBAR TWO PART START ======-->

<section class="navbar-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg">

                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('logo.svg') }}" alt="Logo">
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTwo"
                        aria-controls="navbarTwo" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse sub-menu-bar" id="navbarTwo">
                        <ul class="navbar-nav m-auto">
                            <li class="nav-item active"><a class="page-scroll" href="#home">home</a></li>
                            <li class="nav-item"><a class="page-scroll" href="#services">Services</a></li>
                            <li class="nav-item"><a class="page-scroll" href="#portfolio">Portfolio</a></li>
                            <li class="nav-item"><a class="page-scroll" href="#pricing">Pricing</a></li>
                            <li class="nav-item"><a class="page-scroll" href="#about">About</a></li>
                            <li class="nav-item"><a class="page-scroll" href="#contact">Contact</a></li>
                        </ul>
                    </div>

                    <div class="navbar-btn d-none d-sm-inline-block">
                        <ul>
                            <li><a class="solid" href="{{ route('admin.login') }}">{{ __('Login') }}</a></li>
                        </ul>
                    </div>
                </nav> <!-- navbar -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section>

<!--====== NAVBAR TWO PART ENDS ======-->

<!--====== SLIDER PART START ======-->

<section id="home" class="slider_area">
    <div id="carouselThree" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselThree" data-slide-to="0" class="active"></li>
            <li data-target="#carouselThree" data-slide-to="1"></li>
            <li data-target="#carouselThree" data-slide-to="2"></li>
        </ol>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider-content">
                                <h1 class="title">Business is Now Digital</h1>
                                <p class="text">We blend insights and strategy to create digital products for
                                    forward-thinking organisations.</p>
                                <ul class="slider-btn rounded-buttons">
                                    <li><a class="main-btn rounded-one" href="#">GET STARTED</a></li>
                                    <li><a class="main-btn rounded-two" href="#">DOWNLOAD</a></li>
                                </ul>
                            </div>
                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
                <div class="slider-image-box d-none d-lg-flex align-items-end">
                    <div class="slider-image">
                        <img src="{{ asset('backend/assets/images/slider/1.png') }}" alt="Hero">
                    </div> <!-- slider-imgae -->
                </div> <!-- slider-imgae box -->
            </div> <!-- carousel-item -->

            <div class="carousel-item">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider-content">
                                <h1 class="title">Crafted for Business</h1>
                                <p class="text">We blend insights and strategy to create digital products for
                                    forward-thinking organisations.</p>
                                <ul class="slider-btn rounded-buttons">
                                    <li><a class="main-btn rounded-one" href="#">GET STARTED</a></li>
                                    <li><a class="main-btn rounded-two" href="#">DOWNLOAD</a></li>
                                </ul>
                            </div> <!-- slider-content -->
                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
                <div class="slider-image-box d-none d-lg-flex align-items-end">
                    <div class="slider-image">
                        <img src="{{ asset('backend/assets/images/slider/2.png') }}" alt="Hero">
                    </div> <!-- slider-imgae -->
                </div> <!-- slider-imgae box -->
            </div> <!-- carousel-item -->

            <div class="carousel-item">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider-content">
                                <h1 class="title">Based on Bootstrap 4</h1>
                                <p class="text">We blend insights and strategy to create digital products for
                                    forward-thinking organisations.</p>
                                <ul class="slider-btn rounded-buttons">
                                    <li><a class="main-btn rounded-one" href="#">GET STARTED</a></li>
                                    <li><a class="main-btn rounded-two" href="#">DOWNLOAD</a></li>
                                </ul>
                            </div> <!-- slider-content -->
                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
                <div class="slider-image-box d-none d-lg-flex align-items-end">
                    <div class="slider-image">
                        <img src="{{ asset('backend/assets/images/slider/3.png') }}" alt="Hero">
                    </div> <!-- slider-imgae -->
                </div> <!-- slider-imgae box -->
            </div> <!-- carousel-item -->
        </div>

        <a class="carousel-control-prev" href="#carouselThree" role="button" data-slide="prev">
            <i class="lni lni-arrow-left"></i>
        </a>
        <a class="carousel-control-next" href="#carouselThree" role="button" data-slide="next">
            <i class="lni lni-arrow-right"></i>
        </a>
    </div>
</section>

<!--====== SLIDER PART ENDS ======-->

<!--====== FEATRES TWO PART START ======-->

<section id="services" class="features-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="section-title text-center pb-10">
                    <h3 class="title">Our Services</h3>
                    <p class="text">Stop wasting time and money designing and managing a website that doesn’t get
                        results. Happiness guaranteed!</p>
                </div> <!-- row -->
            </div>
        </div> <!-- row -->
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="single-features mt-40">
                    <div class="features-title-icon d-flex justify-content-between">
                        <h4 class="features-title"><a href="#">Graphics Design</a></h4>
                        <div class="features-icon">
                            <i class="lni lni-brush"></i>
                            <img class="shape" src="{{ asset('f-shape-1.svg') }}" alt="Shape">
                        </div>
                    </div>
                    <div class="features-content">
                        <p class="text">Short description for the ones who look for something new. Short description for
                            the ones who look for something new.</p>
                        <a class="features-btn" href="#">LEARN MORE</a>
                    </div>
                </div> <!-- single features -->
            </div>
            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="single-features mt-40">
                    <div class="features-title-icon d-flex justify-content-between">
                        <h4 class="features-title"><a href="#">Website Design</a></h4>
                        <div class="features-icon">
                            <i class="lni lni-layout"></i>
                            <img class="shape" src="{{ asset('f-shape-1.svg') }}" alt="Shape">
                        </div>
                    </div>
                    <div class="features-content">
                        <p class="text">Short description for the ones who look for something new. Short description for
                            the ones who look for something new.</p>
                        <a class="features-btn" href="#">LEARN MORE</a>
                    </div>
                </div> <!-- single features -->
            </div>
            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="single-features mt-40">
                    <div class="features-title-icon d-flex justify-content-between">
                        <h4 class="features-title"><a href="#">Digital Marketing</a></h4>
                        <div class="features-icon">
                            <i class="lni lni-bolt"></i>
                            <img class="shape" src="{{ asset('f-shape-1.svg') }}" alt="Shape">
                        </div>
                    </div>
                    <div class="features-content">
                        <p class="text">Short description for the ones who look for something new. Short description for
                            the ones who look for something new.</p>
                        <a class="features-btn" href="#">LEARN MORE</a>
                    </div>
                </div> <!-- single features -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section>

<!--====== FEATRES TWO PART ENDS ======-->

<!--====== PORTFOLIO PART START ======-->

{{-- <section id="portfolio" class="portfolio-area portfolio-four pb-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="section-title text-center pb-10">
                    <h3 class="title">You are using free lite version</h3>
                    <p class="text">Please, purchase full version to get all pages and features</p>
                    <div class="light-rounded-buttons mt-30">
                        <a href="https://rebrand.ly/smash-ud" rel="nofollow" class="main-btn light-rounded-two">Purchase
                            Now</a>
                    </div>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section> --}}

<!--====== PORTFOLIO PART ENDS ======-->

<!--====== PRINICNG START ======-->

<section id="pricing" class="pricing-area ">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="section-title text-center pb-25">
                    <h3 class="title">Pricing Plans</h3>
                    <p class="text">Stop wasting time and money designing and managing a website that doesn’t get
                        results. Happiness guaranteed!</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="pricing-style mt-30">
                    <div class="pricing-icon text-center">
                        <img src="{{ asset('basic.svg') }}" alt="basic">
                    </div>
                    <div class="pricing-header text-center">
                        <h5 class="sub-title">Basic</h5>
                        <p class="month"><span class="price">$ 199</span>/month</p>
                    </div>
                    <div class="pricing-list">
                        <ul>
                            <li><i class="lni lni-check-mark-circle"></i> Carefully crafted components</li>
                            <li><i class="lni lni-check-mark-circle"></i> Amazing page examples</li>
                        </ul>
                    </div>
                    <div class="pricing-btn rounded-buttons text-center">
                        <a class="main-btn rounded-one" href="#">GET STARTED</a>
                    </div>
                </div> <!-- pricing style one -->
            </div>

            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="pricing-style mt-30">
                    <div class="pricing-icon text-center">
                        <img src="{{ asset('pro.svg') }}" alt="pro">
                    </div>
                    <div class="pricing-header text-center">
                        <h5 class="sub-title">Pro</h5>
                        <p class="month"><span class="price">$ 399</span>/month</p>
                    </div>
                    <div class="pricing-list">
                        <ul>
                            <li><i class="lni lni-check-mark-circle"></i> Carefully crafted components</li>
                            <li><i class="lni lni-check-mark-circle"></i> Amazing page examples</li>
                        </ul>
                    </div>
                    <div class="pricing-btn rounded-buttons text-center">
                        <a class="main-btn rounded-one" href="#">GET STARTED</a>
                    </div>
                </div> <!-- pricing style one -->
            </div>

            <div class="col-lg-4 col-md-7 col-sm-9">
                <div class="pricing-style mt-30">
                    <div class="pricing-icon text-center">
                        <img src="{{ asset('enterprise.svg') }}" alt="enterprise">
                    </div>
                    <div class="pricing-header text-center">
                        <h5 class="sub-title">Enterprise</h5>
                        <p class="month"><span class="price">$ 699</span>/month</p>
                    </div>
                    <div class="pricing-list">
                        <ul>
                            <li><i class="lni lni-check-mark-circle"></i> Carefully crafted components</li>
                            <li><i class="lni lni-check-mark-circle"></i> Amazing page examples</li>
                        </ul>
                    </div>
                    <div class="pricing-btn rounded-buttons text-center">
                        <a class="main-btn rounded-one" href="#">GET STARTED</a>
                    </div>
                </div> <!-- pricing style one -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section>

<!--====== PRINICNG ENDS ======-->

<!--====== ABOUT PART START ======-->

<section id="about" class="about-area">
    <div class="section-title text-center pb-10">
        <h3 class="title">You are using free lite version</h3>
        <p class="text">Please, purchase full version to get all pages and features</p>
        <div class="light-rounded-buttons mt-30">
            <a href="https://rebrand.ly/smash-ud" rel="nofollow" class="main-btn light-rounded-two">Purchase Now</a>
        </div>
    </div> <!-- section title -->
</section>

<!--====== ABOUT PART ENDS ======-->



<!--====== CONTACT PART START ======-->

<section id="contact" class="contact-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="section-title text-center pb-30">
                    <h3 class="title">Contact</h3>
                    <p class="text">Stop wasting time and money designing and managing a website that doesn’t get
                        results. Happiness guaranteed!</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="contact-map mt-30">
                    <iframe id="gmap_canvas"
                        src="https://maps.google.com/maps?q=Mission%20District%2C%20San%20Francisco%2C%20CA%2C%20USA&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                </div> <!-- row -->
            </div>
        </div> <!-- row -->
        <div class="contact-info pt-30">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single-contact-info contact-color-1 mt-30 d-flex ">
                        <div class="contact-info-icon">
                            <i class="lni lni-map-marker"></i>
                        </div>
                        <div class="contact-info-content media-body">
                            <p class="text"> Elizabeth St, Melbourne<br>1202 Australia.</p>
                        </div>
                    </div> <!-- single contact info -->
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-contact-info contact-color-2 mt-30 d-flex ">
                        <div class="contact-info-icon">
                            <i class="lni lni-envelope"></i>
                        </div>
                        <div class="contact-info-content media-body">
                            <p class="text">admin@mraauae.com</p>
                            <p class="text">support@mraauae.com</p>
                        </div>
                    </div> <!-- single contact info -->
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single-contact-info contact-color-3 mt-30 d-flex ">
                        <div class="contact-info-icon">
                            <i class="lni lni-phone"></i>
                        </div>
                        <div class="contact-info-content media-body">
                            <p class="text">+333 789-321-654</p>
                            <p class="text">+333 985-458-609</p>
                        </div>
                    </div> <!-- single contact info -->
                </div>
            </div> <!-- row -->
        </div> <!-- contact info -->

        {{-- <div class="row">
            <div class="col-lg-12">
                <div class="contact-wrapper form-style-two pt-115">
                    <h4 class="contact-title pb-10"><i class="lni lni-envelope"></i> Leave <span>A Message.</span></h4>

                    <form id="contact-form" action="assets/contact.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-input mt-25">
                                    <label>Name</label>
                                    <div class="input-items default">
                                        <input name="name" type="text" placeholder="Name">
                                        <i class="lni lni-user"></i>
                                    </div>
                                </div> <!-- form input -->
                            </div>
                            <div class="col-md-6">
                                <div class="form-input mt-25">
                                    <label>Email</label>
                                    <div class="input-items default">
                                        <input type="email" name="email" placeholder="Email">
                                        <i class="lni lni-envelope"></i>
                                    </div>
                                </div> <!-- form input -->
                            </div>
                            <div class="col-md-12">
                                <div class="form-input mt-25">
                                    <label>Massage</label>
                                    <div class="input-items default">
                                        <textarea name="massage" placeholder="Massage"></textarea>
                                        <i class="lni lni-pencil-alt"></i>
                                    </div>
                                </div> <!-- form input -->
                            </div>
                            <p class="form-message"></p>
                            <div class="col-md-12">
                                <div class="form-input light-rounded-buttons mt-30">
                                    <button class="main-btn light-rounded-two">Send Message</button>
                                </div> <!-- form input -->
                            </div>
                        </div> <!-- row -->
                    </form>
                </div> <!-- contact wrapper form -->
            </div>
        </div> <!-- row Leave A Message --> --}}

    </div> <!-- container -->
</section>

<!--====== CONTACT PART ENDS ======-->

<!--====== FOOTER PART START ======-->

<section class="footer-area footer-dark">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="footer-logo text-center">
                    <a class="mt-30" href="index.html"><img src="{{ asset('/logo.svg') }}" alt="Logo"></a>
                </div> <!-- footer logo -->
                <ul class="social text-center mt-60">
                    <li><a href="https://facebook.com/uideckHQ"><i class="lni lni-facebook-filled"></i></a></li>
                    <li><a href="https://twitter.com/uideckHQ"><i class="lni lni-twitter-original"></i></a></li>
                    <li><a href="https://instagram.com/uideckHQ"><i class="lni lni-instagram-original"></i></a></li>
                    <li><a href="#"><i class="lni lni-linkedin-original"></i></a></li>
                </ul> <!-- social -->
                <div class="footer-support text-center">
                    <span class="number">+8801234567890</span>
                    <span class="mail">support@mraauae.com</span>
                </div>
                <div class="copyright text-center mt-35">
                    <p class="text">Designed by <b>MRAAUAE International Limited</b></p>
                </div> <!--  copyright -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section>

<!--====== FOOTER PART ENDS ======-->

<!--====== BACK TOP TOP PART START ======-->

<a href="#" class="back-to-top"><i class="lni lni-chevron-up"></i></a>

<!--====== BACK TOP TOP PART ENDS ======-->
@endsection
