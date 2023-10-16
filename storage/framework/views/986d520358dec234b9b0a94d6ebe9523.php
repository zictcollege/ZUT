<?php $__env->startSection('page_title', 'Update Student Class'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Class Assessment Exams Manager</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student ID</th>
                                <th>Names</th>
                                <th>Course</th>
                                <th>Assessment Type</th>
                                <th>Marks</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($result->studentID); ?></td>
                                    <td><?php echo e($result->first_name.' '.$result->last_name); ?></td>
                                    <td><?php echo e($result->code.' - '.$result->title); ?></td>
                                    <td><?php echo e($result->name); ?></td>

                                    <td class="<?php echo e(($result->status == 0 ? 'edit-total-link' : '')); ?>">
                                    <span class="display-mode"
                                          id="display-mode<?php echo e(Qs::hash($result->id)); ?>"><?php echo e($result->total); ?></span>
                                        <input type="text" class="edit-mode form-control"
                                               id="class<?php echo e(Qs::hash($result->id)); ?>"
                                               value="<?php echo e($result->total); ?>" style="display: none;" onchange="updateExamResultsToPublish('<?php echo e(Qs::hash($result->id)); ?>')">
                                    </td>
















                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/update_marks.blade.php ENDPATH**/ ?>