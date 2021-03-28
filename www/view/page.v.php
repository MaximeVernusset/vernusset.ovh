<!DOCTYPE html>
<html lang="en">
    <head>
	<title><?=$title?></title>
		<meta charset="utf-8"/>
		<meta name="author" content="Maxime Vernusset"/>
		<meta name="copyright" content="Maxime Vernusset"/>
		<meta name="language" content="en"/>
		<meta name="robots" content="noindex, follow">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="description" content="vernusset.ovh"/>
        <link rel="icon" type="image/png" href="public/img/favicon.png" />
        <link rel="stylesheet" href="public/vendor/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="public/css/styles.css"/>
        <link rel="stylesheet" href="public/css/override.css"/>
        <script src="public/vendor/jquery/3.6.0/jquery-3.6.0.min.js"></script>
        <script src="public/vendor/font-awesome/5.11.2/js/all.min.js"></script>
        <script src="public/vendor/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">
				<i class="fa fa-terminal" aria-hidden="true"></i>&nbsp;<code>vernusset.ovh</code>
			</a>
			<button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" aria-label="Sidebar toogle">
				<i class="fas fa-bars" aria-hidden="true"></i>
			</button>
            <ul class="navbar-nav ml-auto mr-0 mr-md-3 my-2 my-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Drop down"><i class="fas fa-user fa-fw" aria-hidden="true"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <!--<a class="dropdown-item" href="#">Settings</a>
                        <div class="dropdown-divider"></div>-->
                        <button class="dropdown-item" onclick="logout()"><i class="fas fa-sign-out-alt" aria-hidden="true"></i>&nbsp;Logout</button>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
<?php if (hasAuthorities([PYLOAD])) { ?>
							<div class="sb-sidenav-menu-heading">pyLoad overlay</div>
                            <a class="nav-link" href="index.php?action=pyload/monitor">
								<div class="sb-nav-link-icon">
									<i class="fas fa-tachometer-alt" aria-hidden="true"></i>
								</div>
                                Monitor
							</a>
                            <a class="nav-link" href="index.php?action=pyload/collect">
								<div class="sb-nav-link-icon">
									<i class="fas fa-folder-plus" aria-hidden="true"></i>
								</div>
                                Collector
							</a>
<?php } ?>
<?php if (hasAuthorities(['test'])) { ?>
                            <div class="sb-sidenav-menu-heading">Other</div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
								<div class="sb-nav-link-icon"><i class="fas fa-columns" aria-hidden="true"></i></div>
                                Dropdown
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down" aria-hidden="true"></i></div>
							</a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
									<a class="nav-link" href="#">1</a>
									<a class="nav-link" href="#">2</a>
								</nav>
                            </div>
<?php } ?>
                        </div>
                    </div>
                    <footer class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?=getUser()?>
                    </footer>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main class="container-fluid h-100">
					<?php require_once $view; ?>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Maxime Vernusset <?=date("Y")?></div>
                            <div>
                                <a href="https://vernusset.fr" target="_blank" rel="noopener noreferrer">Resume</a>
                                &middot;
                                <a href="https://github.com/MaximeVernusset" target="_blank" rel="noopener noreferrer">Github</a>
                                &middot;
                                <a href="https://www.linkedin.com/in/maximevernusset/" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="public/js/scripts.js"></script>
    </body>
</html>
