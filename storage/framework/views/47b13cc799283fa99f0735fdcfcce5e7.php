<?php $__env->startSection('page_title', 'Publishing Results for '.$period->code); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Publish Results</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Program Name</th>
                    <th>Qualification</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($program['name']); ?></td>
                        <td><?php echo e($program['qualification']); ?></td>
                        <td><?php echo e($program['students']); ?></td>
                        <td>
                            <?php echo e(($program['status'] == 0 ? 'unpublished' : 'published')); ?>





                        </td>


                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-left">

                                        <a href="<?php echo e(route('getPramResults',['aid'=>Qs::hash($apid),'pid'=>Qs::hash($program['id'])])); ?>"
                                           class="dropdown-item">View Results <i class="icon-pencil"></i></a>
                                        <?php $__currentLoopData = $program['levels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('getPramResultsLevel',['aid'=>Qs::hash($apid),'pid'=>Qs::hash($program['id']),'level'=>Qs::hash($level['level_id'] )])); ?>"
                                               class="dropdown-item">View <?php echo e($level['level_name']); ?> Results <i class="icon-pencil"></i></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if($program['status'] == 0): ?>
                                            <form class="ajax-store-publish" method="post" action="<?php echo e(route('publishProgramResults')); ?>">
                                                <?php echo csrf_field(); ?>

                                                <input type="hidden" name="programID" value="<?php echo e($program['id']); ?>">
                                                <input type="hidden" name="academicPeriodID" value="<?php echo e($apid); ?>">

                                                <div class="text-right">
                                                    <button id="ajax-btn" type="submit" class="dropdown-item">Publish Results <i class="icon-paperplane ml-2"></i></button>
                                                </div>
                                            </form>



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

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/edit.blade.php ENDPATH**/ ?>