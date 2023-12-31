<?php $__env->startSection('page_title', 'My Account => '.$data['first_name'].' '.$data['first_name']); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">My Account</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#change-pass" class="nav-link active" data-toggle="tab">Change Password</a></li>
                <?php if(true): ?>
                    <li class="nav-item"><a href="#edit-profile" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Manage Profile</a></li>
                <?php endif; ?>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="change-pass">
                    <div class="row">
                        <div class="col-md-8">
                            <form method="post" action="<?php echo e(route('my_account.change_pass')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('put'); ?>

                                <div class="form-group row">
                                    <label for="current_password" class="col-lg-3 col-form-label font-weight-semibold">Current Password <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input id="current_password" name="current_password"  required type="password" class="form-control" >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-lg-3 col-form-label font-weight-semibold">New Password <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input id="password" name="password"  required type="password" class="form-control" >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-lg-3 col-form-label font-weight-semibold">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input id="password_confirmation" name="password_confirmation"  required type="password" class="form-control" >
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-danger">Submit form <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="edit-profile">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Gender</td>
                                    <td><?php echo e($data['gender']); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Email</td>
                                    <td><?php echo e($data['email']); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">NRC</td>
                                    <td><?php echo e($data['nrc']); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Date of Birth</td>
                                    <?php if($data['personalinfo']): ?>
                                    <td><?php echo e($data['personalinfo']->dob); ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Marital Status</td>
                                    <?php if($data['personalinfo']): ?>
                                    <td><?php echo e($data['personalinfo']->marital_status); ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Street</td>
                                    <?php if($data['personalinfo']): ?>
                                    <td><?php echo e($data['personalinfo']->street_main); ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Telephone</td>
                                    <?php if($data['personalinfo']): ?>
                                    <td><?php echo e($data['personalinfo']->telephone); ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-justify">Mobile</td>
                                    <?php if($data['personalinfo'] ): ?>
                                    <td><?php echo e($data['personalinfo']->mobile); ?></td>
                                    <?php endif; ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/support_team/my_account.blade.php ENDPATH**/ ?>