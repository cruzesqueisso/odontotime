					<div class="d-sm-flex align-items-center justify-content-between mb-4">
						<h1 class="h3 mb-0 text-gray-900"><strong>Gerenciar pacientes</strong></h1>
					</div>
					<div id="tab_patient" class="tab-pane text-gray-900 shadow">
						<div class="py-3 px-3">
							<div class="row">
								<div class="col-md-12">
									<?php if ($controller->hasPermission('actCreatePatient')): ?>
										<button id="btn_add_patient" class="btn btn-primary text-white mb-3"><i class="fa fa-plus">&nbsp;&nbsp;Adicionar paciente</i></button>
									<?php endif; ?>
									<?php if ($controller->hasPermission('actCreatePatient')): ?>
										<nav>
											<div class="nav nav-tabs" id="nav-tab" role="tablist">
												<?php if ($controller->hasPermission('actListPatients')): ?>
													<a class="nav-item nav-link active" id="nav-patients-tab" data-toggle="tab" href="#nav-patients" role="tab" aria-controls="nav-patients" aria-selected="false">Pacientes</a>
												<?php endif; ?>
												<?php if ($controller->hasPermission('actListAppUsers')): ?>
													<a class="nav-item nav-link" id="nav-app_users-tab" data-toggle="tab" href="#nav-app_users" role="tab" aria-controls="nav-app_users" aria-selected="false">Usuários App</a>
												<?php endif; ?>
											</div>
										</nav>
										<div class="tab-content pt-3" id="nav-tabContent">
											<?php if($controller->hasPermission('actListPatients')): ?>
												<div class="tab-pane fade show active" id="nav-patients" role="tabpanel" aria-labelledby="nav-patients-tab">
													<div class="table-responsive">
														<table id="dt_patients" class="table table-striped table-bordered">
															<thead>
																<tr class="tableheader">
																	<th>*Nome</th>
																	<th>*CPF</th>
																	<th>*E-mail</th>
																	<th>*Endereço</th>
																	<th>*Número</th>
																	<th>*Bairro</th>
																	<th>*Cidade</th>
																	<th>*Estado</th>
																	<th class="dt-center no-sort">Ações</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
											<?php endif; ?>
											<?php if($controller->hasPermission('actListAppUsers')): ?>
												<div class="tab-pane fade show" id="nav-app_users" role="tabpanel" aria-labelledby="nav-app_users-tab">
													<div class="table-responsive">
														<table id="dt_app_users" class="table table-striped table-bordered">
															<thead>
																<tr class="tableheader">
																	<th>*Login</th>
																	<th>*Nome</th>
																	<th>*E-mail</th>
																	<th>Bloqueado</th>
																	<th class="dt-center no-sort">Ações</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php if ($controller->hasPermission('actCreatePatient')
					|| $controller->hasPermission('actUpdatePatient')): ?>
						<!-- Modal Patient Form -->
						<div class="modal fade" id="modal_form_patient" tabindex="-1" role="dialog" aria-labelledby="modal_form_patient_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_form_patient_title"><strong>Cadastro de paciente:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="form_patient">
											<div class="row">
												<div class="form-group col-md-12">
													<label for="form_patient-name">Nome completo:</label>
													<input id="form_patient-name" class="form-control" type="text" name="name" maxlength="128"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-cpf">CPF:</label>
													<input id="form_patient-cpf" class="form-control" type="text" name="cpf"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-phone">Telefone:</label>
													<input id="form_patient-phone" class="form-control" type="text" name="phone" data-phone_type="short"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-email">E-mail:</label>
													<input id="form_patient-email" class="form-control" type="text" name="email"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-email_confirm">Confirmar e-mail:</label>
													<input id="form_patient-email_confirm" class="form-control" type="text" name="email_confirm"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-9">
													<label for="form_patient-address">Endereço:</label>
													<input id="form_patient-address" class="form-control" type="text" name="address" maxlength="128"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-3">
													<label for="form_patient-number">Número:</label>
													<input id="form_patient-number" class="form-control" type="text" name="number" maxlength="16"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-4">
													<label for="form_patient-neighborhood">Bairro:</label>
													<input id="form_patient-neighborhood" class="form-control" type="text" name="neighborhood" maxlength="64"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-8">
													<label for="form_patient-complement">Complemento:</label>
													<input id="form_patient-complement" class="form-control" type="text" name="complement" maxlength="64"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-state">Estado:</label>
													<select id="form_patient-state" class="form-control" name="state">
													</select>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_patient-city">Cidade:</label>
													<select id="form_patient-city" class="form-control" name="city">
													</select>
													<span class="invalid-feedback"></span>
												</div>
												<input id="form_patient-id" name="id" type="text" hidden>
												<div class="form-group col-md-12">
													<button id="form_patient-submit_btn" type="submit" class="btn btn-primary btn-block mt-3 mb-3">Cadastrar</button>
													<span class="text-center form-text mb-3"></span>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($controller->hasPermission('actListPatients')): ?>
						<!-- Modal DT Patients Help -->
						<div class="modal fade" id="modal_dt_patients_help" tabindex="-1" role="dialog" aria-labelledby="modal_dt_patients_help_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_dt_patients_help_title"><strong>Ajuda na procura de pacientes:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<p>É possível realizar a busca por pacientes pelos seguintes campos:</p>
										<ul>
											<li>Nome do paciente</li>
											<li>CPF</li>
											<li>Endereço</li>
											<li>Número</li>
											<li>Bairro</li>
											<li>Cidade</li>
											<li>Estado</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Alter User Password Modal-->
					<?php if ($controller->hasPermission('actAlterAppUserPassword')): ?>
						<div class="modal fade" id="alterAppUserPasswordModal" tabindex="-1" role="dialog" aria-labelledby="alterAppUserPasswordModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="alterAppUserPasswordModalLabel"></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="alter_app_user_password_form">
											<div class="row">
												<div class="form-group col-md-12">
													<label for="alter_app_user_password_form-password">Nova senha:</label>
													<input id="alter_app_user_password_form-password" name="password" class="form-control" type="password">
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-12">
													<label for="alter_app_user_password_form-password_confirm">Confirmar nova senha:</label>
													<input id="alter_app_user_password_form-password_confirm" name="password_confirm" class="form-control" type="password">
													<span class="invalid-feedback"></span>
												</div>
											</div>
											<input id="alter_app_user_password_form-id" name="id" type="text" hidden>
											<div>
												<button id="alter_app_user_password_form-submit_btn" class="btn btn-block btn-primary my-3" type="submit">Alterar senha</button>
												<span class="text-center form-text mb-3"></span>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Modal DT Roles Permissions Help -->
					<?php if ($controller->hasPermission('actListPatients')): ?>
						<div class="modal fade" id="modal_dt_app_users_help" tabindex="-1" role="dialog" aria-labelledby="modal_dt_app_users_help_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_dt_app_users_help_title"><strong>Ajuda na procura de usuários do APP:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<p>É possível realizar a busca por usuários do APP pelos seguintes campos:</p>
										<ul>
											<li>Login</li>
											<li>CPF</li>
											<li>Nome</li>
											<li>E-mail</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if (isset($scripts)):	foreach($scripts as $script): ?>
							<script src="<?= base_url($script) ?>"></script>
					<?php endforeach; endif; ?>