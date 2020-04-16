					<div class="d-sm-flex align-items-center justify-content-between mb-4">
						<h1 class="h3 mb-0 text-gray-900"><strong>Gerenciar usuários</strong></h1>
					</div>
					<div id="tab_user" class="tab-pane text-gray-900 shadow">
						<div class="py-3 px-3">
							<div class="row">
								<div class="col-md-12">
									<?php if ($controller->hasPermission('actCreateUser')): ?>
										<button id="btn_add_user" class="btn btn-primary text-white mb-3 mr-1"><i class="fa fa-plus">&nbsp;&nbsp;Adicionar usuário</i></button>
									<?php endif; ?>
									<?php if ($controller->hasPermission('actCreateRole')): ?>
										<button id="btn_add_role" class="btn btn-primary text-white mb-3 mr-1"><i class="fa fa-plus">&nbsp;&nbsp;Adicionar papel de usuário</i></button>
									<?php endif; ?>
									<?php if ($controller->hasPermission('actListUsers')
									|| $controller->hasPermission('actListRoles')
									|| $controller->hasPermission('actListRolesPermissions')): ?>
										<nav>
											<div class="nav nav-tabs" id="nav-tab" role="tablist">
												<?php if ($controller->hasPermission('actListUsers')): ?>
													<a class="nav-item nav-link active" id="nav-users-tab" data-toggle="tab" href="#nav-users" role="tab" aria-controls="nav-users" aria-selected="false">Usuários</a>
												<?php endif; ?>
												<?php if ($controller->hasPermission('actListRoles')): ?>
													<a class="nav-item nav-link" id="nav-roles-tab" data-toggle="tab" href="#nav-roles" role="tab" aria-controls="nav-roles" aria-selected="false">Papéis de usuários</a>
												<?php endif; ?>
												<?php if ($controller->hasPermission('actListRolesPermissions')): ?>
													<a class="nav-item nav-link" id="nav-roles-permissions-tab" data-toggle="tab" href="#nav-roles-permissions" role="tab" aria-controls="nav-roles-permissions" aria-selected="false">Ações de papéis</a>
												<?php endif; ?>
											</div>
										</nav>
										<div class="tab-content pt-3" id="nav-tabContent">
											<?php if ($controller->hasPermission('actListUsers')): ?>
												<div class="tab-pane fade show active" id="nav-users" role="tabpanel" aria-labelledby="nav-users-tab">
													<div class="table-responsive">
														<table id="dt_users" class="table table-striped table-bordered">
															<thead>
																<tr class="tableheader">
																	<th>*Login</th>
																	<th>*Nome</th>
																	<th>*E-mail</th>
																	<th>Administrador</th>
																	<th>Bloqueado</th>
																	<th class="no-sort">Papéis</th>
																	<th class="dt-center no-sort">Ações</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
											<?php endif; ?>
											<?php if ($controller->hasPermission('actListRoles')): ?>
												<div class="tab-pane fade" id="nav-roles" role="tabpanel" aria-labelledby="nav-roles-tab">
													<div class="table-responsive">
														<table id="dt_roles" class="table table-striped table-bordered">
															<thead>
																<tr class="tableheader">
																	<th>*Nome</th>
																	<th>Descrição</th>
																	<th class="dt-center no-sort">Ações</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
											<?php endif; ?>
											<?php if ($controller->hasPermission('actListRolesPermissions')): ?>
												<div class="tab-pane fade" id="nav-roles-permissions" role="tabpanel" aria-labelledby="nav-roles-permissions-tab">
													<div class="table-responsive">
														<table id="dt_roles_permissions" class="table table-striped table-bordered">
															<thead>
																<tr class="tableheader">
																	<th>*Nome</th>
																	<th>*Controlador</th>
																	<th>Ação</th>
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
					<!-- User Form Modal-->
					<?php if ($controller->hasPermission('actCreateUser')
					|| $controller->hasPermission('actUpdateUser')): ?>
						<div class="modal fade" id="modal_form_user" tabindex="-1" role="dialog" aria-labelledby="modal_form_user_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_form_user_title"><strong>Cadastro de usuário:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="form_user">
											<div class="row">
												<div class="form-group col-md-12">
													<label for="form_user-username">Login:</label>
													<input id="form_user-username" class="form-control" type="text" name="username" maxlength="32"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-12">
													<label for="form_user-name">Nome completo:</label>
													<input id="form_user-name" class="form-control" type="text" name="name" maxlength="128"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-email">E-mail:</label>
													<input id="form_user-email" class="form-control" type="text" name="email" maxlength="128"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-email_confirm">Confirmar e-mail:</label>
													<input id="form_user-email_confirm" class="form-control" type="text" name="email_confirm" maxlength="128"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-password">Senha:</label>
													<input id="form_user-password" class="form-control" type="password" name="password"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-password_confirm">Confirmar senha:</label>
													<input id="form_user-password_confirm" class="form-control" type="password" name="password_confirm"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-is_blocked">Usuário bloqueado:</label>
													<select id="form_user-is_blocked" name="is_blocked" class="form-control">
														<option value="0">Não</option>
														<option value="1">Sim</option>
													</select>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-6">
													<label for="form_user-is_admin">Tipo de usuário:</label>
													<select id="form_user-is_admin" name="is_admin" class="form-control">
														<option value="0">Convencional</option>
														<option value="1">Administrador</option>
													</select>
													<span class="invalid-feedback"></span>
												</div>
												<div class="col-md-12">
													<h5>Papéis:</h5>
													<div id="container_roles" class="row">
													</div>
													<button id="add_role" class="btn btn-primary" type="button" data-toggle="tooltip" title="Adicionar papel"><i class="fa fa-plus"></i></button>
													<span class="form-text"></span>
												</div>
												<input id="form_user-id" name="id" type="text" hidden>
												<div class="form-group col-md-12">
													<button id="form_user-submit_btn" type="submit" class="btn btn-primary btn-block my-3">Cadastrar</button>
													<span class="text-center form-text mb-3"></span>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Alter User Password Modal-->
					<?php if ($controller->hasPermission('actAlterUserPassword')): ?>
						<div class="modal fade" id="alterUserPasswordModal" tabindex="-1" role="dialog" aria-labelledby="alterUserPasswordModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="alterUserPasswordModalLabel"></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="alter_user_password_form">
											<div class="row">
												<div class="form-group col-md-12">
													<label for="alter_user_password_form-password">Nova senha:</label>
													<input id="alter_user_password_form-password" name="password" class="form-control" type="password">
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-12">
													<label for="alter_user_password_form-password_confirm">Confirmar nova senha:</label>
													<input id="alter_user_password_form-password_confirm" name="password_confirm" class="form-control" type="password">
													<span class="invalid-feedback"></span>
												</div>
											</div>
											<input id="alter_user_password_form-user_id" name="user_id" type="text" hidden>
											<div>
												<button id="alter_user_password_form-submit_btn" class="btn btn-block btn-primary my-3" type="submit">Alterar senha</button>
												<span class="text-center form-text mb-3"></span>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Register User Role-->
					<?php if ($controller->hasPermission('actCreateRole')
					|| $controller->hasPermission('actUpdateRole')): ?>
						<div class="modal fade" id="modal_form_role" tabindex="-1" role="dialog" aria-labelledby="modal_form_role_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_form_role_title"><strong>Cadastro de papel de usuários:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="form_role">
											<div class="row">
												<div class="form-group col-md-12">
													<label for="form_role-name">Nome:</label>
													<input id="form_role-name" class="form-control" type="text" name="name" maxlength="64"/>
													<span class="invalid-feedback"></span>
												</div>
												<div class="form-group col-md-12">
													<label for="form_role-description">Descrição:</label>
													<textarea id="form_role-description" class="form-control" name="description" maxlength="255"></textarea>
													<span class="invalid-feedback"></span>
												</div>
												<div class="col-md-12">
													<h5>Permissões:</h5>
													<div id="container_permissions" class="row">
													</div>
													<button id="add_permission" class="btn btn-primary mt-3" type="button" data-toggle="tooltip" title="Adicionar permissão"><i class="fa fa-plus"></i></button>
													<span class="form-text"></span>
												</div>
												<input id="form_role-id" name="id" type="text" hidden>
												<div class="form-group col-md-12">
													<button id="form_role-submit_btn" type="submit" class="btn btn-primary btn-block my-3">Cadastrar</button>
													<span class="text-center form-text mb-3"></span>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Modal DT Users Help -->
					<?php if ($controller->hasPermission('actListUsers')): ?>
						<div class="modal fade" id="modal_dt_users_help" tabindex="-1" role="dialog" aria-labelledby="modal_dt_users_help_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_dt_users_help_title"><strong>Ajuda na procura de usuários:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<p>É possível realizar a busca por usuários pelos seguintes campos:</p>
										<ul>
											<li>Login</li>
											<li>Nome</li>
											<li>E-mail</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Modal DT Roles Help -->
					<?php if ($controller->hasPermission('actListRoles')): ?>
						<div class="modal fade" id="modal_dt_roles_help" tabindex="-1" role="dialog" aria-labelledby="modal_dt_roles_help_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_dt_roles_help_title"><strong>Ajuda na procura de papéis de usuários:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<p>É possível realizar a busca por papéis de usuários pelo seguinte campo:</p>
										<ul>
											<li>Nome do papel</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Modal DT Roles Permissions Help -->
					<?php if ($controller->hasPermission('actListRolesPermissions')): ?>
						<div class="modal fade" id="modal_dt_roles_permissions_help" tabindex="-1" role="dialog" aria-labelledby="modal_dt_roles_permissions_help_title" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content text-gray-900">
									<div class="modal-header">
										<h5 class="modal-title" id="modal_dt_roles_permissions_help_title"><strong>Ajuda na procura de ações em papéis de usuários:</strong></h5>
										<button class="close" type="button" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
									<div class="modal-body">
										<p>É possível realizar a busca por ações em papéis de usuários pelos seguintes campos:</p>
										<ul>
											<li>Nome do papel</li>
											<li>Nome canônico do controlador</li>
											<li>Nome canônico da ação</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if (isset($scripts)):	foreach($scripts as $script): ?>
							<script src="<?= base_url($script) ?>"></script>
					<?php endforeach; endif; ?>
