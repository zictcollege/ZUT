<?php $__env->startSection('page_title', 'Class Results Entry Form'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h3><?php echo e($class[0]['code']); ?></h3>
            <h6 class="card-title">Enter Assessment And Exam Results for <?php echo e($class[0]['courseCode'].' - '.$class[0]['courseName']); ?></h6>
            <h6 class="card-title assess-total">Being Marked out of <?php echo e($class[0]['assess_total']); ?></h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#ut-post-results" class="nav-link active" data-toggle="tab"><i
                                class="icon-plus2"></i>Enter results</a></li>








            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="ut-post-results">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Assessment Type</th>
                            <th>Marks</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $class[0]['students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classAssessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($classAssessment['first_name'].' '.$classAssessment['last_name']); ?></td>
                                <td><?php echo e($classAssessment['student_id']); ?></td>
                                <td><?php echo e($class[0]['assessmentName']); ?></td>
                                <td class="edit-total-link">
                                    <input type="hidden" id="course<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($class[0]['courseCode']); ?>">
                                    <input type="hidden" id="title<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($class[0]['courseName']); ?>">
                                    <input type="hidden" id="idc<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($class[0]['classID']); ?>">
                                    <input type="hidden" id="program<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($classAssessment['program']); ?>">
                                    <input type="hidden" id="apid<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($class[0]['apid']); ?>">
                                    <input type="hidden" id="assessid<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($class[0]['assessmentId']); ?>">
                                    <input type="hidden" id="userid<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($classAssessment['userID']); ?>">
                                    <span class="display-mode"
                                          id="display-mode<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"><?php echo e($classAssessment['total']); ?></span>
                                    <input type="text" class="edit-mode form-control"
                                           id="class<?php echo e(Qs::hash($classAssessment['student_id'])); ?>"
                                           value="<?php echo e($classAssessment['total']); ?>" style="display: none;"
                                           onchange="EnterResults('<?php echo e(Qs::hash($classAssessment['student_id'])); ?>')">
                                </td>



















                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show "
                     id="post-results">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Add Student Results
                                </div>
                                <div class="card-body">

                                        <!-- Import Form -->
                                        <form method="POST" action="<?php echo e(route('import.process')); ?>"
                                              enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label font-weight-semibold"
                                                       for="nal_id">Academic Period: <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select onchange="getRunningPrograms(this.value)"
                                                            data-placeholder="Choose..." name="academic" required
                                                            id="nal_id" class="select-search form-control">
                                                        <option value="">Choose</option>
                                                        <option value="<?php echo e(Qs::hash($class[0]['apid'] )); ?>"><?php echo e($class[0]['code']); ?></option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="classID"
                                                       class="col-lg-3 col-form-label font-weight-semibold">Class: <span
                                                            class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select data-placeholder="Choose..." required name="programID"
                                                            id="classID" class=" select-search form-control">
                                                        <option value="">Choose</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="classID"
                                                       class="col-lg-3 col-form-label font-weight-semibold">Choose File
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <input type="file" class="form-control-file" id="file" name="file"
                                                           required>
                                                    <input type="hidden" name="instructor" value="instructorav"
                                                           required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit Results</button>
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show <?php echo e((!empty($isInstructor) && $isInstructor == 1)? 'active' :''); ?>"
                     id="Upload-results">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Import CSV or Excel File
                                </div>
                                <div class="card-body">
                                    <?php if(session('success')): ?>
                                        <div class="alert alert-success">
                                            <?php echo e(session('success')); ?>

                                        </div>
                                    <?php endif; ?>

                                    <?php if(empty($data)): ?>
                                        <!-- Import Form -->
                                        <form method="POST" action="<?php echo e(route('import.process')); ?>"
                                              enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label font-weight-semibold"
                                                       for="nal_id">Academic Period: <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select onchange="getRunningPrograms(this.value)"
                                                            data-placeholder="Choose..." name="academic" required
                                                            id="nal_id" class="select-search form-control">
                                                        <option value="">Choose</option>
                                                        <option value="<?php echo e(Qs::hash($class[0]['apid'] )); ?>"><?php echo e($class[0]['code']); ?></option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="classID"
                                                       class="col-lg-3 col-form-label font-weight-semibold">Class: <span
                                                            class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <select data-placeholder="Choose..." required name="programID"
                                                            id="classID" class=" select-search form-control">
                                                        <option value="">Choose</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="classID"
                                                       class="col-lg-3 col-form-label font-weight-semibold">Choose File
                                                    <span class="text-danger">*</span></label>
                                                <div class="col-lg-9">
                                                    <input type="file" class="form-control-file" id="file" name="file"
                                                           required>
                                                    <input type="hidden" name="instructor" value="instructorav"
                                                           required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Upload and Preview</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Data Preview Table -->
                                        <h2>Results Preview</h2>
                                        <table class="table table-bordered table-hover datatable-button-html5-columns">
                                            <thead>
                                            <tr>



                                                <th> SIN </th>
                                                <th> CODE </th>
                                                <th> COURSE </th>
                                                <th> MARK </th>
                                                <th> ACADEMIC PERIOD </th>
                                                <th> PROGRAM </th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <td><?php echo e($value); ?></td>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>

                                        <!-- Import Button -->
                                        <div class="row col mb-4 mt-3">
                                            <form method="POST" action="<?php echo e(route('import.process')); ?>"
                                                  enctype="multipart/form-data">
                                                <?php echo csrf_field(); ?>
                                                <div class="form-group">
                                                    <input type="file" class="form-control-file" id="file" name="file"
                                                           required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Import Data</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/instructor_assessment/index.blade.php ENDPATH**/ ?>