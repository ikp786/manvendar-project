
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon_1.ico">

    <title>Ubold - Responsive Admin Dashboard Template</title>

    <!-- bootstrap table css -->
    <link href="{{ asset('ada/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('ada/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/components.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/pages.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/menu.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('ada/responsive.css') }}" rel="stylesheet" type="text/css" />


    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') }}"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js') }}"></script>
    <![endif]-->

    <script src="{{ asset('ada/modernizr.min.js') }}"></script>



</head>


<body>


<!-- Navigation Bar-->
<header id="topnav">
    <div class="topbar-main">
        <div class="container">

            <!-- Logo container-->
            <div class="logo">
                <a href="index.html" class="logo"><span>Ub<i class="md md-album"></i>ld</span></a>
            </div>
            <!-- End Logo container-->

            <div class="menu-extras">

                <ul class="nav navbar-nav navbar-right pull-right">
                    <li>
                        <form role="search" class="navbar-left app-search pull-left hidden-xs">
                            <input type="text" placeholder="Search..." class="form-control">
                            <a href=""><i class="fa fa-search"></i></a>
                        </form>
                    </li>
                    <li class="dropdown hidden-xs">
                        <a href="#" data-target="#" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                            <i class="icon-bell"></i> <span class="badge badge-xs badge-danger">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg">
                            <li class="notifi-title"><span class="label label-default pull-right">New 3</span>Notification</li>
                            <li class="list-group nicescroll notification-list">
                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-diamond fa-2x text-primary"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">A new order has been placed A new order has been placed</h5>
                                            <p class="m-0">
                                                <small>There are new settings available</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-cog fa-2x text-custom"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">New settings</h5>
                                            <p class="m-0">
                                                <small>There are new settings available</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-bell-o fa-2x text-danger"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">Updates</h5>
                                            <p class="m-0">
                                                <small>There are <span class="text-primary font-600">2</span> new updates available</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-user-plus fa-2x text-info"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">New user registered</h5>
                                            <p class="m-0">
                                                <small>You have 10 unread messages</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-diamond fa-2x text-primary"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">A new order has been placed A new order has been placed</h5>
                                            <p class="m-0">
                                                <small>There are new settings available</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <!-- list item-->
                                <a href="javascript:void(0);" class="list-group-item">
                                    <div class="media">
                                        <div class="pull-left p-r-10">
                                            <em class="fa fa-cog fa-2x text-custom"></em>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading">New settings</h5>
                                            <p class="m-0">
                                                <small>There are new settings available</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="list-group-item text-right">
                                    <small class="font-600">See all notifications</small>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="" class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown" aria-expanded="true"><img src="assets/images/users/avatar-1.jpg" alt="user-img" class="img-circle"> </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)"><i class="ti-user m-r-5"></i> Profile</a></li>
                            <li><a href="javascript:void(0)"><i class="ti-settings m-r-5"></i> Settings</a></li>
                            <li><a href="javascript:void(0)"><i class="ti-lock m-r-5"></i> Lock screen</a></li>
                            <li><a href="javascript:void(0)"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>

        </div>
    </div>
    <!-- End topbar -->


    <!-- Navbar Start -->
    <div class="navbar-custom">
        <div class="container">
            <div id="navigation">
                <!-- Navigation Menu-->
                <ul class="navigation-menu">
                    <li class="has-submenu">
                        <a href="#"><i class="md md-dashboard"></i>Dashboard</a>
                        <ul class="submenu">
                            <li>
                                <a href="index.html">Dashboard 01</a>
                            </li>
                            <li>
                                <a href="dashboard_2.html">Dashboard 02</a>
                            </li>
                            <li>
                                <a href="dashboard_3.html">Dashboard 03</a>
                            </li>
                        </ul>
                    </li>
                    <li class="has-submenu">
                        <a href="#"><i class="md md-color-lens"></i>UI Kit</a>
                        <ul class="submenu">
                            <li><a href="ui-buttons.html">Buttons</a></li>
                            <li><a href="ui-panels.html">Panels</a></li>
                            <li><a href="ui-portlets.html">Portlets</a></li>
                            <li><a href="ui-checkbox-radio.html">Checkboxs-Radios</a></li>
                            <li><a href="ui-tabs.html">Tabs</a></li>
                            <li><a href="ui-modals.html">Modals</a></li>
                            <li><a href="ui-progressbars.html">Progress Bars</a></li>
                            <li><a href="ui-notification.html">Notification</a></li>
                            <li><a href="ui-images.html">Images</a></li>
                            <li><a href="ui-carousel.html">Carousel</a>
                            <li><a href="ui-bootstrap.html">Bootstrap UI</a></li>
                            <li><a href="ui-typography.html">Typography</a></li>
                        </ul>
                    </li>


                    <li class="has-submenu">
                        <a href="#"><i class="md md-layers"></i>Components</a>
                        <ul class="submenu megamenu">
                            <li>
                                <ul>
                                    <li>
                                        <span>Elements</span>
                                    </li>
                                    <li><a href="components-grid.html">Grid</a></li>
                                    <li><a href="components-widgets.html">Widgets</a></li>
                                    <li><a href="components-nestable-list.html">Nesteble</a></li>
                                    <li><a href="components-range-sliders.html">Range sliders</a></li>
                                    <li><a href="components-animation.html">Animation</a></li>
                                    <li><a href="components-sweet-alert.html">Sweet Alerts</a></li>
                                    <li><a href="components-treeview.html">Tree view</a></li>
                                    <li><a href="components-tour.html">Tour</a></li>
                                </ul>
                            </li>
                            <li>
                                <ul>
                                    <li>
                                        <span>Forms</span>
                                    </li>
                                    <li><a href="form-elements.html">General Elements</a></li>
                                    <li><a href="form-advanced.html">Advanced Form</a></li>
                                    <li><a href="form-validation.html">Form Validation</a></li>
                                    <li><a href="form-pickers.html">Form Pickers</a></li>
                                    <li><a href="form-wizard.html">Form Wizard</a></li>

                                </ul>
                            </li>

                            <li>
                                <ul>
                                    <li>
                                        <span>Forms</span>
                                    </li>
                                    <li><a href="form-mask.html">Form Masks</a></li>
                                    <li><a href="form-summernote.html">Summernote</a></li>
                                    <li><a href="form-wysiwig.html">Wysiwig Editors</a></li>
                                    <li><a href="form-uploads.html">Multiple File Upload</a></li>
                                    <li><a href="form-xeditable.html">X-editable</a></li>
                                    <li><a href="form-image-crop.html">Image Crop</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="has-submenu active">
                        <a href="#"><i class="md md-class"></i>Other</a>
                        <ul class="submenu">
                            <li class="has-submenu">
                                <a href="#">Tables</a>
                                <ul class="submenu">
                                    <li><a href="tables-basic.html">Basic Tables</a></li>
                                    <li><a href="tables-datatable.html">Data Table</a></li>
                                    <li><a href="tables-editable.html">Editable Table</a></li>
                                    <li><a href="tables-responsive.html">Responsive Table</a></li>
                                    <li><a href="tables-foo-tables.html">FooTable</a></li>
                                    <li class="active"><a href="tables-bootstrap.html">Bootstrap Tables</a></li>
                                    <li><a href="tables-tablesaw.html">Tablesaw</a></li>
                                </ul>
                            </li>
                            <li class="has-submenu">
                                <a href="#">Charts</a>
                                <ul class="submenu">
                                    <li><a href="chart-flot.html">Flot Chart</a></li>
                                    <li><a href="chart-morris.html">Morris Chart</a></li>
                                    <li><a href="chart-chartjs.html">Chartjs</a></li>
                                    <li><a href="chart-peity.html">Peity Charts</a></li>
                                    <li><a href="chart-chartist.html">Chartist Charts</a></li>
                                    <li><a href="chart-c3.html">C3 Charts</a></li>
                                    <li><a href="chart-nvd3.html"> Nvd3 Charts</a></li>
                                    <li><a href="chart-sparkline.html">Sparkline charts</a></li>
                                    <li><a href="chart-radial.html">Radial charts</a></li>
                                    <li><a href="chart-other.html">Other Chart</a></li>
                                    <li><a href="chart-ricksaw.html">Ricksaw Chart</a></li>
                                </ul>
                            </li>
                            <li class="has-submenu">
                                <a href="#">Icons</a>
                                <ul class="submenu">
                                    <li><a href="icons-glyphicons.html">Glyphicons</a></li>
                                    <li><a href="icons-materialdesign.html">Material Design</a></li>
                                    <li><a href="icons-ionicons.html">Ion Icons</a></li>
                                    <li><a href="icons-fontawesome.html">Font awesome</a></li>
                                    <li><a href="icons-themifyicon.html">Themify Icons</a></li>
                                    <li><a href="icons-simple-line.html">Simple line Icons</a></li>
                                    <li><a href="icons-weather.html">Weather Icons</a></li>
                                    <li><a href="icons-typicons.html">Typicons</a></li>
                                </ul>
                            </li>
                            <li class="has-submenu">
                                <a href="#">Maps</a>
                                <ul class="submenu">
                                    <li><a href="map-google.html"> Google Map</a></li>
                                    <li><a href="map-vector.html"> Vector Map</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="has-submenu">
                        <a href="#"><i class="md md-pages"></i>Pages</a>
                        <ul class="submenu megamenu">
                            <li>
                                <ul>
                                    <li><a href="page-starter.html">Starter Page</a></li>
                                    <li><a href="page-login.html">Login</a></li>
                                    <li><a href="page-login-v2.html">Login v2</a></li>
                                    <li><a href="page-register.html">Register</a></li>
                                    <li><a href="page-register-v2.html">Register v2</a></li>
                                    <li><a href="page-signup-signin.html">Signin - Signup</a></li>
                                    <li><a href="page-recoverpw.html">Recover Password</a></li>
                                </ul>
                            </li>
                            <li>
                                <ul>
                                    <li><a href="page-lock-screen.html">Lock Screen</a></li>
                                    <li><a href="page-400.html">Error 400</a></li>
                                    <li><a href="page-403.html">Error 403</a></li>
                                    <li><a href="page-404.html">Error 404</a></li>
                                    <li><a href="page-404_alt.html">Error 404-alt</a></li>
                                    <li><a href="page-500.html">Error 500</a></li>
                                    <li><a href="page-503.html">Error 503</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li class="has-submenu">
                        <a href="#"><i class="md md-folder-special"></i>Extras</a>
                        <ul class="submenu megamenu">
                            <li>
                                <ul>
                                    <li><a href="extra-profile.html">Profile</a></li>
                                    <li><a href="extra-timeline.html">Timeline</a></li>
                                    <li><a href="extra-sitemap.html">Site map</a></li>
                                    <li><a href="extra-invoice.html">Invoice</a></li>
                                    <li><a href="extra-email-template.html">Email template</a></li>
                                    <li><a href="extra-maintenance.html">Maintenance</a></li>
                                    <li><a href="extra-coming-soon.html">Coming-soon</a></li>
                                </ul>
                            </li>
                            <li>
                                <ul>
                                    <li><a href="extra-faq.html">FAQ</a></li>
                                    <li><a href="extra-search-result.html">Search result</a></li>
                                    <li><a href="extra-gallery.html">Gallery</a></li>
                                    <li><a href="extra-pricing.html">Pricing</a></li>
                                    <li><a href="apps-inbox.html"> Email</a></li>
                                    <li><a href="apps-calendar.html"> Calendar</a></li>
                                    <li><a href="apps-contact.html"> Contact</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="has-submenu">
                        <a href="#"><i class="md md-account-circle"></i>CRM</a>
                        <ul class="submenu">
                            <li><a href="crm-dashboard.html"> Dashboard </a></li>
                            <li><a href="crm-contact.html"> Contacts </a></li>
                            <li><a href="crm-opportunities.html"> Opportunities </a></li>
                            <li><a href="crm-leads.html"> Leads </a></li>
                            <li><a href="crm-customers.html"> Customers </a></li>
                        </ul>
                    </li>
                    <li class="has-submenu">
                        <a href="#"><i class="md md-shopping-cart"></i>eCommerce</a>
                        <ul class="submenu">
                            <li><a href="ecommerce-dashboard.html"> Dashboard</a></li>
                            <li><a href="ecommerce-products.html"> Products</a></li>
                            <li><a href="ecommerce-product-detail.html"> Product Detail</a></li>
                            <li><a href="ecommerce-orders.html"> Orders</a></li>
                            <li><a href="ecommerce-sellers.html"> Sellers</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- End navigation menu        -->
            </div>
        </div>
    </div>
</header>
<!-- End Navigation Bar-->


<!-- =======================
     ===== START PAGE ======
     ======================= -->

<div class="wrapper">
    <div class="container">

        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="btn-group pull-right m-t-15">
                    <button type="button" class="btn btn-default dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Settings <span class="m-l-5"><i class="fa fa-cog"></i></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </div>
                <h4 class="page-title">Bootstrap Tables</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="#">Ubold</a>
                    </li>
                    <li>
                        <a href="#">Tables</a>
                    </li>
                    <li class="active">
                        Bootstrap Tables
                    </li>
                </ol>

            </div>
        </div>
        <!-- Page-Title -->




        <!--Basic Columns-->
        <!--===================================================-->

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Basic Columns</b></h4>
                    <p class="text-muted font-13">
                        Example of basic columns (Your text goes here).
                    </p>

                    <table data-toggle="table"
                           data-show-columns="false"
                           data-page-list="[5, 10, 20]"
                           data-page-size="5"
                           data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="id" data-switchable="false">First Name</th>
                            <th data-field="name">Last Name</th>
                            <th data-field="date">Job Title</th>
                            <th data-field="amount">DOB</th>
                            <th data-field="user-status" class="text-center">Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!--Checkbox Select-->
        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Checkbox Select</b></h4>
                    <p class="text-muted m-b-30 font-13">
                        Example of checkbox select (Your text goes here).
                    </p>

                    <table data-toggle="table"
                           data-page-size="10"
                           data-pagination="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-field="id" data-switchable="false">First Name</th>
                            <th data-field="name">Last Name</th>
                            <th data-field="date">Job Title</th>
                            <th data-field="amount">DOB</th>
                            <th data-field="user-status" class="text-center">Status</th>
                        </tr>
                        </thead>


                        <tbody>
                        <tr>
                            <td></td>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!--Radio Select-->
        <!--===================================================-->

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Radio Select</b></h4>
                    <p class="text-muted m-b-30 font-13">
                        Example of checkbox select (Your text goes here).
                    </p>

                    <table data-toggle="table"
                           data-page-size="10"
                           data-pagination="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="state" data-radio="true"></th>
                            <th data-field="id" data-switchable="false">First Name</th>
                            <th data-field="name">Last Name</th>
                            <th data-field="date">Job Title</th>
                            <th data-field="amount">DOB</th>
                            <th data-field="user-status" class="text-center">Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td></td>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Isidra</td>
                            <td>Boudreaux</td>
                            <td>Traffic Court Referee</td>
                            <td>22 Jun 1972</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Shona</td>
                            <td>Woldt</td>
                            <td>Airline Transport Pilot</td>
                            <td>3 Oct 1981</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Granville</td>
                            <td>Leonardo</td>
                            <td>Business Services Sales Representative</td>
                            <td>19 Apr 1969</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Easer</td>
                            <td>Dragoo</td>
                            <td>Drywall Stripper</td>
                            <td>13 Dec 1977</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maple</td>
                            <td>Halladay</td>
                            <td>Aviation Tactical Readiness Officer</td>
                            <td>30 Dec 1991</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Maxine</td>
                            <td><a href="#">Woldt</a></td>
                            <td><a href="#">Business Services Sales Representative</a></td>
                            <td>17 Oct 1987</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lorraine</td>
                            <td>Mcgaughy</td>
                            <td>Hemodialysis Technician</td>
                            <td>11 Nov 1983</td>
                            <td><span class="label label-table label-inverse">Disabled</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lizzee</td>
                            <td><a href="#">Goodlow</a></td>
                            <td>Technical Services Librarian</td>
                            <td>1 Nov 1961</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Judi</td>
                            <td>Badgett</td>
                            <td>Electrical Lineworker</td>
                            <td>23 Jun 1981</td>
                            <td><span class="label label-table label-success">Active</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lauri</td>
                            <td>Hyland</td>
                            <td>Blackjack Supervisor</td>
                            <td>15 Nov 1985</td>
                            <td><span class="label label-table label-danger">Suspended</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!--Sort & Format Column-->
        <!--===================================================-->

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Sort &amp; Format Column</b></h4>
                    <p class="text-muted font-13">
                        Example of Sort & Format Column (Your text goes here).
                    </p>

                    <table data-toggle="table"
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="5"
                           data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="id" data-sortable="true" data-formatter="invoiceFormatter">Order ID</th>
                            <th data-field="name" data-sortable="true">Name</th>
                            <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Order date</th>
                            <th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Price</th>
                            <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>UB-1609</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1610</td>
                            <td>Woldt</td>
                            <td>24 Jun 2015</td>
                            <td>$ 15.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1611</td>
                            <td>Leonardo</td>
                            <td>25 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1612</td>
                            <td>Halladay</td>
                            <td>27 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1613</td>
                            <td>Badgett</td>
                            <td>28 Jun 2015</td>
                            <td>$ 95.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1614</td>
                            <td>Boudreaux</td>
                            <td>29 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1615</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1616</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1617</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1618</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1619</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1620</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1621</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1622</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1623</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1624</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!--Basic Toolbar-->
        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Basic Toolbar</b></h4>
                    <p class="text-muted font-13">
                        Example of Basic Toolbar (Your text goes here).
                    </p>

                    <table data-toggle="table"
                           data-search="true"
                           data-show-refresh="true"
                           data-show-toggle="true"
                           data-show-columns="true"
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="5"
                           data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="id" data-sortable="true" data-formatter="invoiceFormatter">Order ID</th>
                            <th data-field="name" data-sortable="true">Name</th>
                            <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Order Date</th>
                            <th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Price</th>
                            <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status</th>

                        </tr>
                        </thead>


                        <tbody>
                        <tr>
                            <td>UB-1609</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1610</td>
                            <td>Woldt</td>
                            <td>24 Jun 2015</td>
                            <td>$ 15.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1611</td>
                            <td>Leonardo</td>
                            <td>25 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1612</td>
                            <td>Halladay</td>
                            <td>27 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1613</td>
                            <td>Badgett</td>
                            <td>28 Jun 2015</td>
                            <td>$ 95.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1614</td>
                            <td>Boudreaux</td>
                            <td>29 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1615</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1616</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1617</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1618</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1619</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1620</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td>UB-1621</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td>UB-1622</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td>UB-1623</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td>UB-1624</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!--Custom Toolbar-->
        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Custom Toolbar</b></h4>
                    <p class="text-muted font-13">
                        Example of Custom Toolbar (Your text goes here).
                    </p>

                    <button id="demo-delete-row" class="btn btn-danger" disabled><i class="fa fa-times m-r-5"></i>Delete</button>
                    <table id="demo-custom-toolbar"  data-toggle="table"
                           data-toolbar="#demo-delete-row"
                           data-search="true"
                           data-show-refresh="true"
                           data-show-toggle="true"
                           data-show-columns="true"
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="5"
                           data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                        <thead>
                        <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-field="id" data-sortable="true" data-formatter="invoiceFormatter">Order ID</th>
                            <th data-field="name" data-sortable="true">Name</th>
                            <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Order Date</th>
                            <th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Price</th>
                            <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td></td>
                            <td>UB-1609</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>UB-1610</td>
                            <td>Woldt</td>
                            <td>24 Jun 2015</td>
                            <td>$ 15.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1611</td>
                            <td>Leonardo</td>
                            <td>25 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1612</td>
                            <td>Halladay</td>
                            <td>27 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1613</td>
                            <td>Badgett</td>
                            <td>28 Jun 2015</td>
                            <td>$ 95.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>UB-1614</td>
                            <td>Boudreaux</td>
                            <td>29 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1615</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1616</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1617</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>UB-1618</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1619</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1620</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1621</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Unpaid</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>UB-1622</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Paid</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1623</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Shipped</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>UB-1624</td>
                            <td>Boudreaux</td>
                            <td>22 Jun 2015</td>
                            <td>$ 35.00</td>
                            <td>Refunded</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End row-->




        <!-- Footer -->
        <footer class="footer text-right">
            <div class="container">
                <div class="row">
                    <div class="col-xs-6">
                        2016 ?? Ubold.
                    </div>
                    <div class="col-xs-6">
                        <ul class="pull-right list-inline m-b-0">
                            <li>
                                <a href="#">About</a>
                            </li>
                            <li>
                                <a href="#">Help</a>
                            </li>
                            <li>
                                <a href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End Footer -->

    </div> <!-- end container -->
</div>
<!-- End wrapper -->



<!-- jQuery  -->
<script src="{{ asset('ada/jquery.min.js') }}"></script>
<script src="{{ asset('ada/bootstrap.min.js') }}"></script>
<script src="{{ asset('ada/detect.js') }}"></script>
<script src="{{ asset('ada/fastclick.js') }}"></script>
<script src="{{ asset('ada/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('ada/jquery.blockUI.js') }}"></script>
<script src="{{ asset('ada/waves.js') }}"></script>
<script src="{{ asset('ada/wow.min.js') }}"></script>
<script src="{{ asset('ada/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('ada/jquery.scrollTo.min.js') }}"></script>

<!-- Bootstrap table -->
<script src="{{ asset('ada/bootstrap-table.min.js') }}"></script>

<script src="{{ asset('ada/jquery.core.js') }}"></script>
<script src="{{ asset('ada/jquery.app.js') }}"></script>


<!-- Bootstrap table init -->
<script src="{{ asset('ada/jquery.bs-table.js') }}"></script>

</body>
</html>