<?php if(!empty($results)): ?>
    <?php
        $firstAcademicData = reset($results);
        $academicData = $firstAcademicData['academic'];
    ?>
    <?php $__env->startSection('page_title', $firstAcademicData['academicperiodname'] .'s Results'); ?>

<?php else: ?>
    <?php $__env->startSection('page_title', 'No results found'); ?>

<?php endif; ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>

    <div class="card overflow-scroll">
        <div class="card-header header-elements-inline">
            
        </div>

        <div class="card-body">
            
            
            
            
            
            
            
            
            
            <div class="row p-3">
                <div class="container">
                    <div class="row justify-content-end">
                        <div class="col-md-12">
                            <?php if(!empty($results)): ?>
                                <div class="d-flex justify-content-between align-items-center float-right">
                                    <label class="mb-2">
                                        Publish All <input type="checkbox" value="1" name="user-all"
                                                           class="user-all form-check">
                                    </label>
                                </div>
                                <h3>Program: <?php echo e($firstAcademicData['program_name']); ?>

                                    (<?php echo e($firstAcademicData['program_code']); ?>

                                    )</h3>
                                <h4><?php echo e($firstAcademicData['level_name']); ?>'s Results</h4>
                                <h4 class="mb-4 mt-0">Results for <?php echo e($firstAcademicData['total_students']); ?>

                                    Students</h4>
                                <div class="row">
                                    <label for="assesmentID" class="col-lg-3 col-form-label font-weight-semibold">Course(Moderate
                                        for all): <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <?php
                                            $uniqueCourseCodes = [];
                                        ?>
                                        <select data-placeholder="Choose..." required name="assesmentID"
                                                id="assesmentID" class=" select-search form-control"
                                                onchange="StrMod4All('<?php echo e($firstAcademicData['program']); ?>','<?php echo e($firstAcademicData['academic']); ?>', this.value)">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $academicData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $academicData['students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $__currentLoopData = $student['courses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $code = $course['code'];
                                                            $title = $course['title'];
                                                            $optionValue = $code . ' - ' . $title;
                                                        ?>
                                                        <?php if(!in_array($optionValue, $uniqueCourseCodes)): ?>
                                                            <option value="<?php echo e($code); ?>"><?php echo e($optionValue); ?></option>
                                                            <?php
                                                                $uniqueCourseCodes[] = $optionValue;
                                                            ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <hr>

                            <?php else: ?>
                                <h3>Results not found</h3>
                            <?php endif; ?>
                            <div class="loading-more-results pr-4" style="height: 600px; overflow-y: scroll">
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $academicData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="height: 800px;overflow-y: scroll">
                                        <?php $__currentLoopData = $academicData['students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <table class="table table-hover table-striped-columns mb-3">
                                                <div class="justify-content-between">
                                                    <h5><strong><?php echo e($student['name']); ?></strong></h5>
                                                    <h5><strong><?php echo e($student['student_id']); ?></strong></h5>
                                                    <input type="hidden" name="academic"
                                                           value="<?php echo e($firstAcademicData['academic']); ?>">
                                                    <input type="hidden" name="program"
                                                           value="<?php echo e($firstAcademicData['program']); ?>">
                                                    <input type="hidden" name="level_name"
                                                           value="<?php echo e($firstAcademicData['level_id']); ?>">
                                                </div>

                                                <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Course Code</th>
                                                    <th>Course Name</th>
                                                    <th>CA</th>
                                                    <th>Exam</th>
                                                    <th>Total</th>
                                                    <th>Grade</th>
                                                    <th>Modify</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $__currentLoopData = $student['courses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <th><?php echo e($loop->iteration); ?></th>
                                                        <td><?php echo e($course['code']); ?></td>
                                                        <td><?php echo e($course['title']); ?></td>
                                                        <td><?php echo e($course['CA']); ?></td>
                                                        <td>
                                                            <?php $__currentLoopData = $course['assessments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if(!empty($assess['assessment_name']) && $assess['assessment_name']=='Exam'): ?>
                                                                    <?php echo e($assess['total']); ?>

                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </td>
                                                        
                                                        <td><?php echo e($course['total']); ?></td>
                                                        <td><?php echo e($course['grade']); ?></td>
                                                        <td>
                                                            <?php if(Qs::userIsTeamSA()): ?>
                                                                <a onclick="modifyMarks('<?php echo e($student['student_id']); ?>','<?php echo e($firstAcademicData['program']); ?>','<?php echo e($firstAcademicData['academic']); ?>','<?php echo e($course['code']); ?>')"
                                                                   class="nav-link"><i class="icon-pencil"></i></a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>

                                            </table>
                                            <p class="bg-success p-3 align-bottom">Comment
                                                : <?php echo e($student['commentData']); ?>

                                                <?php echo e(Form::checkbox('ckeck_user', 1, false,['class'=>'ckeck_user  float-right p-5','data-id' => $student['student_id'] ])); ?> <?php echo e(Form::label('publish', 'Publish', ['class' => 'mr-3 float-right'])); ?></p>
                                            <hr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary publish-results-board btn-sm mt-3"
                                        disabled="disabled"><i class="fa fa-share"></i> Publish Results
                                </button>
                                <?php if($firstAcademicData['current_page'] === $firstAcademicData['last_page']): ?>

                                <?php else: ?>
                                    <button type="button" class="float-right mr-5 btn btn-primary load-more-results load-more-results-first btn-sm mt-3"
                                    onclick="LoadMoreResults('<?php echo e($firstAcademicData['current_page']); ?>','<?php echo e($firstAcademicData['last_page']); ?>','<?php echo e($firstAcademicData['per_page']); ?>','<?php echo e($firstAcademicData['program']); ?>','<?php echo e($firstAcademicData['academic']); ?>','<?php echo e($firstAcademicData['level_id']); ?>')">
                                    <i class="fa fa-share"></i> Load More
                                    </button>
                                <?php endif; ?>


                                <p class="text-center" id="pagenumbers">page <?php echo e($firstAcademicData['current_page']); ?>

                                    of <?php echo e($firstAcademicData['last_page']); ?></p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content row col card card-body">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                        
                        
                        
                        
                    </div>
                    <div class="modal-body p-3">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary closeModalButton"
                                onclick="modifyMarksCloseModal()"
                                id="closeModalButton" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" id="submitButton" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/results_review_board.blade.php ENDPATH**/ ?>