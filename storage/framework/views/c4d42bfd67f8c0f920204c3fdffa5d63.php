<?php $__env->startSection('page_title', 'Manage Class Assessment for '.$infor->code); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Class Assessment And Exams Manager</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Course Name</th>
                    <th>Code</th>
                    <th>Instructor</th>
                    <th>Students</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $apClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classAssessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($classAssessment['name']); ?></td>
                        <td><?php echo e($classAssessment['code']); ?></td>
                        <th><?php echo e($classAssessment['first_name'].' '.$classAssessment['last_name']); ?></th>
                        <td><?php echo e($classAssessment['enrollment_count']); ?></td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-left">
                                    <?php $__currentLoopData = $classAssessment['assessments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('myClassStudentList', ['class' => Qs::hash($classAssessment['id']),'assessid' => Qs::hash($assess['assessTypeId'])])); ?>"
                                               class="dropdown-item"><i class="icon-eye"></i>Enter <?php echo e($assess['assessTypeName']); ?> Results</a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/show_classes.blade.php ENDPATH**/ ?>