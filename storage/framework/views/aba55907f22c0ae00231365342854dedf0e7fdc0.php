        <?php $__env->startSection('content'); ?>



    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>Company detail</b></h4>
                <br>
                <div class="row">
                    <div class="col-md-8">

                        
                        <?php if(Session::has('message')): ?>
                            <div class="alert alert-success"><span
                                        class="glyphicon glyphicon-ok"></span><?php echo e(Session::get('message')); ?>

                            </div>
                        <?php endif; ?>
                        <div class="form-horizontal">
                        
						<?php echo Form::model($company, [
    'method' => 'PATCH',
    'url' => ['company', 1]
]); ?>

						 <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Company Name</label>
                                <div class="col-md-9">
                                    <input value="<?php echo e($company->company_name); ?>" type="text" name="company_name"
                                           class="form-control"
                                           placeholder="Company Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="example-email">Email</label>
                                <div class="col-md-9">
                                    <input value="<?php echo e($company->company_email); ?>" type="email" id="example-email"
                                           name="company_email" class="form-control"
                                           placeholder="Company Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Phone Number</label>
                                <div class="col-md-9">
                                    <input type="text" value="<?php echo e($company->company_phone); ?>" name="company_phone"
                                           class="form-control" placeholder="company phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">website url</label>
                                <div class="col-md-9">
                                    <input type="text" disabled value="" name="company_website_old"
                                           class="form-control" placeholder="Website">
                                </div>
                            </div>
                            <input type="hidden" value="" name="company_website"
                                   class="form-control" placeholder="Website">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Company Logo</label>
                                <div class="col-md-9">
                                    <input type="file" name="company_logo" class="form-control"
                                           placeholder="Company Logo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Mobile Number</label>
                                <div class="col-md-9">
                                    <input type="text" value="<?php echo e($company->company_mobile); ?>" name="company_mobile"
                                           class="form-control" placeholder="Mobile Number" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Address</label>
                                <div class="col-md-9">
                                <textarea class="form-control" name="company_address"
                                          rows="5"><?php echo e($company->company_address); ?></textarea>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-3 control-label">Agent News</label>
                                <div class="col-md-9">
                                <textarea class="form-control" name="news"
                                          rows="5"><?php echo e($company->news); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">Distributor News</label>
                                <div class="col-md-9">
                                <textarea class="form-control" name="recharge_news"
                                          rows="5"><?php echo e($company->recharge_news); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        Save Now
                                    </button>
                                    <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
                                        Cancel
                                    </button>
                                </div>
                            </div>
							<?php echo Form::close(); ?>


                           
                        </div>
                    </div>

                    <div class="col-md-4">

                    </div>


                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>