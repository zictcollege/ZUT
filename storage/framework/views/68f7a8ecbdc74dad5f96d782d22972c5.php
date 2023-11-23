<?php $__env->startSection('page_title', 'Manage Academics'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Academic Periods</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <?php if(true): ?>
                <li class="nav-item"><a href="#all-open" class="nav-link" data-toggle="tab">Open Academic Periods</a></li>
                    <li class="nav-item"><a href="#all-closed" class="nav-link" data-toggle="tab">Closed Academic Periods</a></li>
                    <li class="nav-item"><a href="#add-tt" class="nav-link active" data-toggle="tab">Create Academic Year</a></li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Show Academic Periods</a>
                    <div class="dropdown-menu dropdown-menu-right">



                    </div>
                </li>
            </ul>


            <div class="tab-content">

                <?php if(true): ?>
                <div class="tab-pane fade show active" id="add-tt">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>When a an Academic year is created, go ahead and create intakes and programs running there <a target="_blank" href="<?php echo e('#'); ?>">Manage Sections</a></span>
                            </div>
                        </div>
                    </div>

                   <div class="col-md-8">
                       <form class="ajax-store" method="post" action="<?php echo e(route('create')); ?>">
                           <?php echo csrf_field(); ?>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Code <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="code" value="<?php echo e(old('code')); ?>" required type="text" class="form-control" placeholder="code">
                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Start Date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="acStartDate" value="<?php echo e(old('acStartDate')); ?>" type="text" class="form-control date-pick" placeholder="Academic Start Date">

                               </div>
                           </div>

                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                       <input autocomplete="off" name="acEndDate" value="<?php echo e(old('acEndDate')); ?>" type="text" class="form-control date-pick" placeholder="Select Date...">

                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Registration End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="registrationDate" value="<?php echo e(old('registrationDate')); ?>" type="text" class="form-control date-pick" placeholder="Select Date...">

                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Late Registration End date <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="lateRegistrationDate" value="<?php echo e(old('lateRegistrationDate')); ?>" type="text" class="form-control date-pick" placeholder="Select Date...">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Registration Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="registrationThreshold" value="<?php echo e(old('registrationThreshold')); ?>" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">View Results Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="resultsThreshold" value="<?php echo e(old('resultsThreshold')); ?>" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label class="col-lg-3 col-form-label font-weight-semibold">Download Exam Slip Threshold <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input name="examSlipThreshold" value="<?php echo e(old('examSlipThreshold')); ?>" required type="text" class="form-control" placeholder="%">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="exam_id" class="col-lg-3 col-form-label font-weight-semibold">Period ID <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <input autocomplete="off" name="periodID" value="<?php echo e(old('periodID')); ?>" type="text" class="form-control" placeholder="Select Date...">
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="study-mode" class="col-lg-3 col-form-label font-weight-semibold">Select Study Mode <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <select required data-placeholder="Select Class" class="form-control select" name="studyModeIDAllowed" id="study-mode">
                                       <option value="">Choose .....</option>
                                       <?php $__currentLoopData = $studymode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                           <option <?php echo e(old('id') == $c->id ? 'selected' : ''); ?> value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                   </select>
                               </div>
                           </div>
                           <div class="form-group row">
                               <label for="period-type" class="col-lg-3 col-form-label font-weight-semibold">Academic Period type <span class="text-danger">*</span></label>
                               <div class="col-lg-9">
                                   <select required data-placeholder="Select Class" class="form-control select" name="type" id="period-type">
                                       <option value="">Choose .....</option>
                                      <?php $__currentLoopData = $periodstypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <option <?php echo e(old('id') == $c->id ? 'selected' : ''); ?> value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                   </select>
                               </div>
                           </div>


                           <div class="text-right">
                               <button id="ajax-btn" type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                           </div>
                       </form>
                   </div>

                </div>
                <?php endif; ?>

                        <div class="tab-pane fade" id="all-open">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info border-0 alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                        <span>When a an Academic year is created, go ahead and create intakes and programs running there <a target="_blank" href="<?php echo e('#'); ?>">Manage Sections</a></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Reg Date</th>
                                        <th>Late Date</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Mode</th>
                                        <th>type</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $open; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($m->code); ?></td>
                                            <td><?php echo e($m->registrationDate); ?></td>
                                            <td><?php echo e($m->lateRegistrationDate); ?></td>
                                            <td><?php echo e($m->acStartDate); ?></td>
                                            <td><?php echo e($m->acEndDate); ?></td>
                                            <td><?php echo e($m->studyMode->name); ?></td>
                                            <td><?php echo e($m->periodType->name); ?></td>
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <div class="dropdown">
                                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>

                                                        <div class="dropdown-menu dropdown-menu-left">
                                                            <?php if(true): ?>
                                                                <a href="<?php echo e(route('update',Qs::hash($m->id))); ?>" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                            <?php endif; ?>
                                                                <?php if(true): ?>
                                                                    <a href="<?php echo e(route('academic.show', Qs::hash($m->id))); ?>" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                                <?php endif; ?>
                                                            <?php if(true): ?>
                                                                <a id="<?php echo e($m->id); ?>" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
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
                    <div class="tab-pane fade" id="all-closed">
                            <div class="col-md-12">
                                <table class="table datatable-button-html5-columns">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Code</th>
                                        <th>Registration</th>
                                        <th>Late Date</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $closed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($m->code); ?></td>
                                            <td><?php echo e($m->registrationDate); ?></td>
                                            <td><?php echo e($m->lateRegistrationDate); ?></td>
                                            <td><?php echo e($m->acStartDate); ?></td>
                                            <td><?php echo e($m->acEndDate); ?></td>
                                            <td><?php echo e($m->studyMode->name); ?></td>
                                            <td><?php echo e($m->periodType->name); ?></td>
                                            <td class="text-center">
                                                <div class="list-icons">
                                                    <div class="dropdown">
                                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>

                                                        <div class="dropdown-menu dropdown-menu-left">
                                                            <?php if(true): ?>
                                                                <a href="<?php echo e(route('studymodes.edit', Qs::hash($m->id))); ?>" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                            <?php endif; ?>
                                                                <?php if(true): ?>
                                                                    <a href="<?php echo e(route('academic.show', Qs::hash($m->id))); ?>" class="dropdown-item"><i class="icon-eye"></i> Show</a>
                                                                <?php endif; ?>
                                                            <?php if(true): ?>
                                                                <a id="<?php echo e($m->id); ?>" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                                <form method="post" id="item-delete-<?php echo e($m->id); ?>" action="<?php echo e(route('academics.destroy', Qs::hash($m->id))); ?>" class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('delete'); ?></form>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/academic_periods/create.blade.php ENDPATH**/ ?>