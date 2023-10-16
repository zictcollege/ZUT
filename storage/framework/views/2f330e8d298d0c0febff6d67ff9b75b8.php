<?php $__env->startSection('page_title', 'Manage Class Assessment'); ?>
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
            <ul class="nav nav-tabs nav-tabs-highlight" role="tablist" id="myTabs">
                <li class="nav-item">
                    <a href="#new-class-assessment" class="nav-link active" data-toggle="tab">Assign Assessment</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#tab" class="nav-link dropdown-toggle" data-toggle="dropdown">Manage class Assessment</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php $__currentLoopData = $academicPeriodsArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $academicPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#ut-<?php echo e(Qs::hash($academicPeriod['academic_period_id'])); ?>" class="dropdown-item"
                               data-toggle="tab"><?php echo e($academicPeriod['academic_period_code']); ?>s</a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-class-assessment">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="<?php echo e(route('classAssessments.store')); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold" for="nal_id">Academic
                                        Period: <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select onchange="getAcClassesPD(this.value)" data-placeholder="Choose..."
                                                name="academic" required id="nal_id" class="select-search form-control">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $open; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option  value="<?php echo e($nal->id); ?>"><?php echo e($nal->code); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="classID" class="col-lg-3 col-form-label font-weight-semibold">Class:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Choose..." required name="classID" id="classID"
                                                class=" select-search form-control">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="assesmentID" class="col-lg-3 col-form-label font-weight-semibold">Assessment
                                        Type: <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Choose..." required name="assesmentID"
                                                id="assesmentID" class=" select-search form-control">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $assess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option <?php echo e((old('id') == $a->id ? 'selected' : '')); ?> value="<?php echo e($a->id); ?>"><?php echo e($a->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Weighting (%)<span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="total" value="<?php echo e(old('total')); ?>" required type="number" min="1"
                                               class="form-control" placeholder="Total">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Due Date<span
                                                class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input autocomplete="off" name="end_date" value="<?php echo e(old('end_date')); ?>"
                                               type="text" class="form-control date-pick" placeholder="ADue Date">
                                    </div>
                                </div>


                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i
                                                class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <?php $__currentLoopData = $academicPeriodsArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $academicPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tab-pane fade reloadThisDiv"
                         id="ut-<?php echo e(Qs::hash($academicPeriod['academic_period_id'])); ?>">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Class Name</th>
                                <th>Class code</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $academicPeriod['class_assessments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classAssessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($classAssessment['course_name']); ?></td>
                                    <td><?php echo e($classAssessment['course_code']); ?></td>
                                    <td><?php echo e($classAssessment['assessment_type_name']); ?></td>
                                    <td>
                                    <span class="display-mode"
                                          id="display-mode<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"><?php echo e($classAssessment['total']); ?></span>
                                        <input type="text" class="edit-mode form-control"
                                               id="class<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"
                                               value="<?php echo e($classAssessment['total']); ?>" style="display: none;"
                                               onchange="updateExamResults('<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>')">
                                    </td>
                                    <td>
                                    <span class="display-mode"
                                          id="display-mode-enddate<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"><?php echo e($classAssessment['end_date']); ?></span>
                                        <input autocomplete="off" type="text" class="edit-mode form-control date-pick"
                                               id="enddate<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"
                                               value="<?php echo e($classAssessment['end_date']); ?>" style="display: none;"
                                               onchange="updateExamResults('<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>')">
                                    </td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">




                                                    <a href="#" class="dropdown-item edit-total-link"><i
                                                                class="icon-pencil"></i> Edit</a>
                                                    <?php if(Qs::userIsSuperAdmin()): ?>
                                                        <a id="<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"
                                                           onclick="confirmDelete(this.id)" href="#"
                                                           class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post"
                                                              id="item-delete-<?php echo e(Qs::hash($classAssessment['class_assessment_id'])); ?>"
                                                              action="<?php echo e(route('classAssessments.destroy', Qs::hash($classAssessment['class_assessment_id']))); ?>"
                                                              class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?></form>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </div>

    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/index.blade.php ENDPATH**/ ?>