<?php $__env->startSection('page_title', 'Assessment'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Assessment</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-assessments" class="nav-link active" data-toggle="tab">Existing Assessments</a></li>
                <li class="nav-item"><a href="#new-assessments" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Create Assessments</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-assessments">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Assessment Type</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($assessment->name); ?></td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    <?php if(Qs::userIsTeamSA()): ?>
                                                    <a href="<?php echo e(route('assessments.edit', $assessment->id)); ?>" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                   <?php endif; ?>
                                                        <?php if(Qs::userIsSuperAdmin()): ?>
                                                    <a id="<?php echo e($assessment->id); ?>" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-<?php echo e($assessment->id); ?>" action="<?php echo e(route('assessments.destroy', $assessment->id)); ?>" class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?></form>
                                                        <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                <div class="tab-pane fade" id="new-assessments">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="<?php echo e(route('assessments.store')); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Assessment Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="<?php echo e(old('name')); ?>" required type="text" class="form-control" placeholder="Course Name">
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/assessment_types/index.blade.php ENDPATH**/ ?>