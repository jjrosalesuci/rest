<?php echo get_header(); ?>
<?php echo get_partial('content_top'); ?>

<?php if ($this->alert->get('', 'alert')) { ?>
    <div id="notification">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->alert->display('', 'alert'); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div id="heading">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="heading-section">
			    	<h3><?php echo lang('text_heading'); ?></h3>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="myModal" class="modal fade register-restaurant" role="dialog">
  <div class="modal-dialog register-restaurant-modal">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo lang('text_heading'); ?></h4>
      </div>
      <div class="modal-body">
       
	  	<ul class="nav nav-pills nav-justified thumbnail">
				<li id="baslic_li" class="active">
					   <a data-toggle="tab" id="basic_link" href="#basic">
							<h4 class="list-group-item-heading"><?php echo lang('text_step_one'); ?></h4>
							<p class="list-group-item-text hidden-xs"><?php echo lang('text_step_one_res_details'); ?></p>
						</a>
				</li>
				<li id="account_li" class="disabled">
					    <a  data-toggle="tab" id="account_link" href="#account">
							<h4 class="list-group-item-heading"><?php echo lang('text_step_two'); ?></h4>
							<p class="list-group-item-text hidden-xs"><?php echo lang('text_step_two_acount_administrator'); ?></p>
						</a>
				</li>
				<li id="confirm_li" class="disabled">
					    <a  data-toggle="tab" id="confirm_link" href="#confirm">
							<h4 class="list-group-item-heading"><?php echo lang('text_step_three'); ?></h4>
							<p class="list-group-item-text hidden-xs"><?php echo lang('text_step_three_confirmation'); ?></p>
						</a>
				</li>
		</ul>

		<div class="tab-content">
            <!-- Basics -->
			<div id="basic"  class="tab-pane fade in active">
				<div class="row form-horizontal">

							 <form id="step1">

								<div class="col-xs-12 col-md-6">
									<h4 class="tab-pane-title">Basic</h4>
									<div class="form-group">
										<label for="input-name" class="col-sm-4 control-label"> <b> <?php echo lang('label_name'); ?> </b> </label>
										<div class="col-sm-8">
											<input type="text" name="location_name" id="input_location_name" class="form-control" autofocus />											
										</div>
									</div>
									<div class="form-group">
										<label for="input-email" class="col-sm-4 control-label"> <b> <?php echo lang('label_email'); ?> </b> </label>
										<div class="col-sm-8">
											<input type="text" name="email" id="input_email" class="form-control"  />										
										</div>
									</div>
									<div class="form-group">
										<label for="input-telephone" class="col-sm-4 control-label"> <b><?php echo lang('label_telephone'); ?> </b> </label>
										<div class="col-sm-8">
											<input type="text" name="telephone" id="input_telephone" class="form-control" />										
										</div>
									</div>
								</div>
								
								<div class="col-xs-12 col-md-6">
											<h4 class="tab-pane-title">Address</h4>											
											<div class="form-group">
												<label for="address_1" class="col-sm-4 control-label"> <b> <?php echo lang('label_address_1'); ?> </b> </label>
												<div class="col-sm-8">
													<input type="text" name="address_1" id="address_1" class="form-control"  />													
												</div>
											</div>
											<div class="form-group">
												<label for="address_2" class="col-sm-4 control-label"> <b> <?php echo lang('label_address_2'); ?> </b> </label>
												<div class="col-sm-8">
													<input type="text" name="address_2" id="address_2" class="form-control" />											
												</div>
											</div>
											<div class="form-group">
												<label for="address_city" class="col-sm-4 control-label"> <b> <?php echo lang('label_city'); ?> </b> </label>
												<div class="col-sm-8">
													<input type="text" name="address_city" id="address_city" class="form-control" />													
												</div>
											</div>
											<div class="form-group">
												<label for="input-state" class="col-sm-4 control-label"> <b> <?php echo lang('label_state'); ?> </b> </label>
												<div class="col-sm-8">
													<input type="text" name="address_state" id="address_state" class="form-control"  />												
												</div>
											</div>
											<div class="form-group">
												<label for="input-postcode" class="col-sm-4 control-label"> <b> <?php echo lang('label_postcode'); ?> </b> </label>
												<div class="col-sm-8">
													<input type="text" name="address_postcode" id="address_postcode" class="form-control"  />													
												</div>
											</div>
											<div class="form-group">
												<label for="input-postcode" class="col-sm-4 control-label"> <b> <?php echo lang('label_country'); ?> </b> </label>
												<div class="col-sm-8">												
												 <input type="hidden" name="address_country" id="address_country" value="<?php  echo $country["country_name"]; ?>" />
											  	 <label class="country-text" > <?php  echo $country["country_name"]; ?> <label>
												</div>
											</div>

											<span id="label_address_error" class="help-block register-restaurant-span-error" style="display:none">
											  <?php  echo lang('label_address_error'); ?> 
											</span>
								</div>	

							</form>

				</div>
			</div>

			<!-- Acount adminsitrator -->
			<div id="account"  class="tab-pane fade">
					  
				<div class="row form-horizontal">								
						<form id="step2">

								<div class="col-xs-12 col-md-6">									
									
									<div class="form-group">
										<label for="input-name" class="col-sm-4 control-label"><b><?php echo lang('label_firt_name'); ?></b></label>
										<div class="col-sm-8">
											<input type="text" name="first_name" id="input_first_name" class="form-control"/>										
										</div>
									</div>

									<div class="form-group">
										<label for="input-address-1" class="col-sm-4 control-label"><b><?php echo lang('label_last_name'); ?></b></label>
										<div class="col-sm-8">
										  <input type="text" name="last_name" id="input_last_name" class="form-control"  />												
										</div>
									</div>

									<div class="form-group lesss-margin-botton">
										<div class="col-sm-4">
											<label for="input-email" class="control-label"> <b> <?php echo lang('label_email2'); ?></b></label>
											<div class="info"> <?php echo lang('label_email_required'); ?> </div>
										</div>
										<div class="col-sm-8">
										 	    <input type="email" name="admin_email" id="input_admin_email" class="form-control" />
												<span id="label_emailexists_error" class="help-block register-restaurant-span-error" style="display:none">
													<?php  echo lang('label_email_error'); ?> 
												</span>
										</div>
									</div>


								
									

									<div class="form-group lesss-margin-botton">
												<div class="col-sm-4">
													<label for="input-address-2" class="control-label"><b><?php echo lang('label_mobilephone'); ?></b></label>
													<div class="info"> <?php echo lang('label_phone_required'); ?> </div>
												</div>
												<div class="col-sm-8">
													<input type="phone" name="admin_mobilephone" id="input_admin_mobilephone" class="form-control" />
												</div>
											</div>

									

								</div>
								<div class="col-xs-12 col-md-6">

											

											<div class="form-group">
												<label for="input-telephone" class="col-sm-4 control-label"> <b> <?php echo lang('label_password'); ?></b></label>
												<div class="col-sm-8">
													<input type="password" name="password" id="input_password" class="form-control" />
												</div>
											</div>
											
										

											<div class="form-group">
												<label for="input-city" class="col-sm-4 control-label"><b><?php echo lang('label_password_confirm');?></b></label>
												<div class="col-sm-8">
													<input type="password" name="password_confirm" id="input_password_confirm" class="form-control" />
												</div>
											</div>
								</div>

						</form>					
				</div>

			</div>

			<!-- Comfirmation -->
			<div id="confirm"  class="tab-pane fade confirm">
			  <div class="row form-horizontal">
				<div class="col-xs-12 col-md-6">
				 	<h4 class="tab-pane-title"><?php echo lang('label_Location_details');?></h4>
					<!-- Restaurant Name -->
					<h4 class="restaurant-title" id="info_restaurant_title"></h4>
					<!-- Restaurant Addres -->
					<div class="address">
						<span class="text-muted" id="info_restaurant_address"></span>
					</div>
					
					<!-- Map -->
					<div id="map" class="map"></div>	
				
				    <span class="help-block"> <?php echo lang('label_drag_drop');?> </span>
				</div>
				
				<div class="col-xs-12 col-md-6">
			        	<h4 class="tab-pane-title"><?php echo lang('label_account_validation');?></h4>						 
						</br>

						<form id="step3">
							<div class="form-group">
								<div class="col-sm-4">
									<label for="input-postcode" class=" control-label"><b><?php echo lang('label_email_code');?> </b></label>
									<div class="help-block">The email code was send to <label id="confirm_email_info"></label> </div>
								</div>
								<div class="col-sm-6">
									<input type="text" name="email_code" id="input_email_code" class="form-control"  />													
								</div>						
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<label for="input-postcode" class="control-label"> <b> <?php echo lang('label_phone_code');?> </b></label>
									<div class="help-block">The mobile phone code was send to <label id="confirm_phone_info"> </div>
								</div>							
								<div class="col-sm-6">
									<input type="text" name="mobile_code" id="input_mobile_code" class="form-control"  />													
								</div>
							</div>

							<div class="form-group">
							    <div class="col-xs-1">
									<input type="checkbox"  class="control-label" name="accept_terms" id="accept_terms" value="1"></input>
								</div>
								<div class="col-xs-11">
								 
									<?php echo lang('accept_terms_text');?> <a><?php echo lang('accept_terms_link');?></a>

									<span id="label_terms_and_conditions_error" class="help-block register-restaurant-span-error" style="display:none">
										<?php  echo lang('label_accept_terms_error'); ?> 
									</span>

								</div>
							</div>

						 </form>

				    </div>
		    	</div>

			  </div>
		    </div>

         </div>
     
	  
	  
	  <div class="modal-footer">
	    	<div  id="next" class="btn btn-default">  <?php echo lang('btn_next'); ?>  </div>
			<div  id="confirm_andstart" style="display:none" class="btn btn-primary"> <?php echo lang('btn_confirm'); ?> </div>
      </div>

    </div>

  </div>
</div>








<div id="page-content">
	<div class="container top-spacing">
		<div class="row">
			<?php echo get_partial('content_left'); ?><?php echo get_partial('content_right'); ?>
			<?php
				if (partial_exists('content_left') AND partial_exists('content_right')) {
					$class = "col-sm-6 col-md-6";
				} else if (partial_exists('content_left') OR partial_exists('content_right')) {
					$class = "col-sm-9 col-md-9";
				} else {
					$class = "col-md-12";
				}
			?>
			<div class="content-wrap <?php echo $class; ?>">
					<div class="row">
						<div class="col-xs-4"></div>
						<div class="col-xs-4">						
							<button type="submit" data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-block"><?php echo lang('text_btn_register');?></button>
						</div>
						<div class="col-xs-4"></div>
					</div>
			</div>
		</div>
	</div>
</div>
<?php echo get_footer(); ?>