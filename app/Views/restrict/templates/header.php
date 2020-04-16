<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Odonto Time">
	<meta name="author" content="Vinícius Borges França">

	<title>Odonto Time</title>
	<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-32x32.png'); ?>" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-64x64.png'); ?>" sizes="64x64">
	<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-128x128.png'); ?>" sizes="128x128">

	<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

	<?php foreach ($styles as $style): ?>
		<link href="<?= base_url($style) ?>" rel="stylesheet" type="text/css">
	<?php endforeach; ?>
</head>

<body id="page-top">
	<!-- Page Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

			<!-- Sidebar - Brand -->
			<a class="navbar-brand d-flex align-items-center justify-content-center" href="<?= base_url('index.php/restrict/index') ?>">
				<img class="img-fluid" src="<?= base_url($images['mainLogo']) ?>" alt="logo-odonto-time"/>
			</a>

			<!-- Divider -->
			<hr class="sidebar-divider my-0">

			<!-- Nav Item - Dashboard -->
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('index.php/restrict/index') ?>">
					<i class="fas fa-home"></i>
					<span>Página Inicial</span>
				</a>
			</li>

			<?php if ($controller->session->user['isAdmin'] || $permissions['App\\Controllers\\User']): ?>
				<!-- Divider -->
				<hr class="sidebar-divider">
	
				<!-- Heading -->
				<div class="sidebar-heading">Usuários</div>

				<!-- Nav Item - Pages Collapse Menu -->
				<li class="nav-item">
					<a id="sidebarUser" class="nav-link" href="#">
						<i class="fas fa-user-cog"></i>
						<span>Gerenciar usuários</span>
					</a>
				</li>
			<?php endif; ?>

			<?php if ($controller->session->user['isAdmin'] || $permissions['App\\Controllers\\Patient']): ?>
				<!-- Divider -->
				<hr class="sidebar-divider">
	
				<!-- Heading -->
				<div class="sidebar-heading">Pacientes</div>

				<!-- Nav Item - Pages Collapse Menu -->
				<li class="nav-item">
					<a id="sidebarPatient" class="nav-link" href="#">
						<i class="fas fa-users"></i>
						<span>Gerenciar pacientes</span>
					</a>
				</li>
			<?php endif; ?>

			<!-- Divider -->
			<hr class="sidebar-divider d-none d-md-block">

			<!-- Sidebar Toggler (Sidebar) -->
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>

		</ul>
		<!-- End of Sidebar -->

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main Content -->
			<div id="content">

				<!-- Topbar -->
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

					<!-- Sidebar Toggle (Topbar) -->
					<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 bg-light">
						<i class="fa fa-bars"></i>
					</button>

					<!-- Topbar Navbar -->
					<ul class="navbar-nav ml-auto">

						<div class="topbar-divider d-none d-sm-block"></div>

						<!-- Nav Item - User Information -->
						<li class="nav-item dropdown no-arrow">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="mr-2 d-none d-lg-inline small"><?= $controller->session->user['name'] ?></span>
								<img class="img-profile rounded-circle" src="<?= base_url($images['userAvatar']) ?>">
							</a>
							<!-- Dropdown - User Information -->
							<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
								<a id="profile_user_caller" class="dropdown-item" href="#" data-user_id="<?= $controller->session->user['id'] ?>">
									<i class="fas fa-user fa-sm fa-fw mr-2"></i>
									Perfil
								</a>
								<a id="alter_current_user_pwd_caller" class="dropdown-item" href="#">
									<i class="fas fa-key mr-2"></i>
									Alterar senha
								</a>
								<div class="dropdown-divider border border-dark"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
									<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
									Sair
								</a>
							</div>
						</li>

					</ul>

				</nav>
				<!-- End of Topbar -->

				<!-- Begin Page Content -->
				<div id="dynamic-content" class="container-fluid">