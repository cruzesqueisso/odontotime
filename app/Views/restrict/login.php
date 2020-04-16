<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Odonto Time">
		<meta name="author" content="Vinícius Borges França">
		<title>Odonto Time :: Login</title>
		<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-32x32.png'); ?>" sizes="32x32">
		<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-64x64.png'); ?>" sizes="64x64">
		<link rel="icon" type="image/png" href="<?php echo base_url('images/icons/odonto-time-128x128.png'); ?>" sizes="128x128">

		<?php foreach ($styles as $style): ?>
			<link href="<?= base_url($style) ?>" rel="stylesheet" type="text/css">
		<?php endforeach; ?>
	</head>
	<body class="bg-gradient-primary">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="painel" class="shadow-lg bg-white border rounded my-5">
						<div id="logo" class="text-center">
							<img src="<?= base_url('images/logos/odonto-time.png');?>" alt="logo-odonto-time">
							<span class="h1">Odonto Time</span>
						</div>
						<div class="container text-gray-900">
							<form id="login_form" method="post">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="username">Usuário:</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend rounded-left">
													<div class="input-group-text">
														<span class="fas fa-user"></span>
													</div>
												</div>
												<input type="text" class="form-control rounded-right" id="login_form-username" name="username">
												<span class="invalid-feedback"></span>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="password">Senha:</label>
											<div class="input-group mb-3">
												<div class="input-group-prepend rounded-left">
													<div class="input-group-text">
														<span class="fas fa-lock"></span>
													</div>
												</div>
												<input type="password" class="form-control rounded-right" id="login_form-password" name="password">
												<span class="invalid-feedback"></span>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
											<a id="call_recovery_password" href="#">Recuperar senha</a>
									</div>
								</div>
								<div>
									<button type="submit" id="login_form-submit_btn" class="btn btn-block btn-primary my-3">Entrar</button>
									<span class="text-center form-text mb-3"></span>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal Recovery Password Form -->
		<div class="modal fade" id="modal_form_recovery_password" tabindex="-1" role="dialog" aria-labelledby="modal_form_recovery_password_title" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content text-gray-900">
					<div class="modal-header">
						<h5 class="modal-title" id="modal_form_recovery_password_title"><strong>Recuperação de senha:</strong></h5>
						<button class="close" type="button" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="form_recovery_password">
							<div class="row">
								<div class="form-group col-md-12">
									<label for="form_recovery_password-username">Usuário:</label>
									<input id="form_recovery_password-username" class="form-control" type="text" name="username"/>
									<span class="invalid-feedback"></span>
								</div>
								<div class="form-group col-md-12">
									<button id="form_recovery_password-submit_btn" type="submit" class="btn btn-primary btn-block my-3">Recuperar senha</button>
									<span class="text-center form-text mb-3"></span>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php foreach ($scripts as $script): ?>
			<script src="<?= base_url($script) ?>"></script>
		<?php endforeach; ?>
	</body>
</html>
