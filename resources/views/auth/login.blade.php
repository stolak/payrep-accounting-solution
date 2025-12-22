<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('Coy_Name', '') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <!-- Favicons -->
    <link href="assets2/img/njc-logo2.jpg" rel="icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets2/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets2/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets2/plugins/fontawesome/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets2/css/style.css">
</head>

<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <header class="header">
            <nav class="navbar navbar-expand-lg header-nav">
                <div class="navbar-header">
                    <a href="/" class="navbar-brand logo">
                        <img src="assets/img/logo.jpeg" class="img-fluid" alt="">
                    </a>
                </div>

                <ul class="nav header-navbar-rht">

                    <li class="nav-item">
                        <a class="nav-link header-login" href="/">login </a>
                    </li>
                </ul>
            </nav>
        </header>
        <!-- /Header -->

        <!-- Page Content -->
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-8 offset-md-2">

                        <!-- Login Tab Content -->
                        <div class="account-content">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-md-7 col-lg-6 login-left">
                                    <h2 style="text-align: center">FINANCE SOLUTIONS</h2>
                                    <img src="assets2/img/budget-banner.jpg" class="img-fluid"
                                        alt="Budget Management System">
                                </div>
                                <div class="col-md-12 col-lg-6 login-right">
                                    <div class="login-header">
                                        <h3>Login </h3>
                                    </div>
                                    <form action="{{ url('/login') }}" method="POST">
                                        {{ csrf_field() }}
                                        <div class="form-group form-focus">
                                            <input type="text" class="form-control floating" type="email"
                                                name="email" :value="old('email')" required autofocus>
                                            <label class="focus-label">Username</label>
                                        </div>
                                        <div class="form-group form-focus">
                                            <input type="password" class="form-control floating" name="password">
                                            <label class="focus-label">Password</label>
                                        </div>

                                        <button class="btn btn-primary btn-block btn-lg login-btn"
                                            type="submit">Login</button>
                                        <div class="text-right">
                                            <a class="forgot-link" href="#">Forgot Password ?</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /Login Tab Content -->

                    </div>
                </div>

            </div>

        </div>
        <!-- /Page Content -->

        <!-- Footer -->
        <footer class="footer">



            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container-fluid">

                    <!-- Copyright -->
                    <div class="copyright">
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="copyright-text">
                                    <p class="mb-0">&copy; {{ date('Y') }} {{ env('Coy_Name', '') }}. All rights
                                        reserved.</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">

                                <!-- Copyright Menu -->
                                <div class="copyright-menu">
                                    <ul class="policy-menu">
                                        <li><a href="/">Designed by STOLAK SOFTECH</a></li>
                                    </ul>
                                </div>
                                <!-- /Copyright Menu -->

                            </div>
                        </div>
                    </div>
                    <!-- /Copyright -->

                </div>
            </div>
            <!-- /Footer Bottom -->

        </footer>
        <!-- /Footer -->

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets2/js/jquery.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets2/js/popper.min.js"></script>
    <script src="assets2/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script src="assets2/js/script.js"></script>

</body>

</html>
