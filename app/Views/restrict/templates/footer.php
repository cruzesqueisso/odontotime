				</div>
				<!-- End of dynamic content -->
			</div>
			<!-- End of Main Content -->
			<!-- Footer -->
			<footer class="sticky-footer bg-white text">
				<div class="container my-auto">
					<div class="copyright text-center my-auto">
						<span>Para contatar o suporte do Odonto Time envie um e-mail para: <?= $suportEmail ?><br>Copyright &copy; Odonto Time 2020-<?php echo date('Y');?></span>
					</div>
				</div>
			</footer>
			<!-- End of Footer -->
		
		</div>
		<!-- End of Content Wrapper -->

	</div>
	<!-- End of Page Wrapper -->

	<!-- Scroll to Top Button-->
	<a class="scroll-to-top rounded bg-primary" href="#page-top">
		<i class="fas fa-angle-up"></i>
	</a>

	<!-- Profile User Modal-->
	<div class="modal fade" id="profileUserModal" tabindex="-1" role="dialog" aria-labelledby="profileUserModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content text-gray-900">
				<div class="modal-header">
					<h5 class="modal-title" id="profileUserModalLabel"><strong>Perfil do usuário</strong></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="profile_user_form">
						<div class="row">
							<div class="form-group col-md-12">
								<label for="profile_user_form-name">Nome completo:</label>
								<input id="profile_user_form-name" name="name" class="form-control" type="text" maxlength="128">
								<span class="invalid-feedback"></span>
							</div>
							<div class="form-group col-md-12">
								<label for="profile_user_form-email">E-mail:</label>
								<input id="profile_user_form-email" name="email" class="form-control" type="text" maxlength="128">
								<span class="invalid-feedback"></span>
							</div>
						</div>
						<input id="profile_user_form-current_user_id" name="current_user_id" type="text" value="<?= $controller->session->user['id'] ?>" hidden>
						<div>
							<button id="profile_user_form-submit_btn" class="btn btn-block btn-primary my-3" type="submit">Editar dados</button>
							<span class="text-center form-text mb-3"></span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Alter User Password Modal-->
	<div class="modal fade" id="alterCurrentUserPwdModal" tabindex="-1" role="dialog" aria-labelledby="alterUserPasswordModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content text-gray-900">
				<div class="modal-header">
					<h5 class="modal-title" id="alterUserPasswordModalLabel"><strong>Alterar senha do usuário</strong></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="alter_current_user_pwd_form">
						<div class="row">
							<div class="form-group col-md-12">
								<label for="alter_current_user_pwd_form-current_password">Senha atual:</label>
								<input id="alter_current_user_pwd_form-current_password" name="current_password" class="form-control" type="password">
								<span class="invalid-feedback"></span>
							</div>
							<div class="form-group col-md-12">
								<label for="alter_current_user_pwd_form-password">Nova senha:</label>
								<input id="alter_current_user_pwd_form-password" name="password" class="form-control" type="password">
								<span class="invalid-feedback"></span>
							</div>
							<div class="form-group col-md-12">
								<label for="alter_current_user_pwd_form-password_confirm">Confirmar nova senha:</label>
								<input id="alter_current_user_pwd_form-password_confirm" name="password_confirm" class="form-control" type="password">
								<span class="invalid-feedback"></span>
							</div>
						</div>
						<input id="alter_current_user_pwd_form-current_user_id" name="current_user_id" type="text" value="<?= $controller->session->user['id'] ?>" hidden>
						<div>
							<button id="alter_current_user_pwd_form-submit_btn" class="btn btn-block btn-primary my-3" type="submit">Alterar senha</button>
							<span class="text-center form-text mb-3"></span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Logout Modal-->
	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content text-gray-900">
				<div class="modal-header">
					<h5 class="modal-title" id="logoutModalLabel"><strong>Deseja realmente sair?</strong></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">Selecione "Sair" abaixo se você estiver pronto para finalizar a sua sessão.</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
					<a class="btn btn-primary" href="<?= base_url('index.php/restrict/logoff') ?>">Sair</a>
				</div>
			</div>
		</div>
	</div>

	<?php foreach ($scripts as $script): ?>
		<script src="<?= base_url($script) ?>"></script>
	<?php endforeach; ?>
</body>
</html>