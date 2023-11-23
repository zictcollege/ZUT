<?php $__env->startSection('page_title', 'Publishing Results for '.$period->code); ?>
<?php $__env->startSection('content'); ?>
    <?php
        use App\Helpers\Qs;
    ?>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Results Reports</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body mb-4">
            <div class="container">
                <form method="post" class="mt-1 mb-4" action="<?php echo e(route('get_reports_results')); ?>" >
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="hidden" name="academic" value="<?php echo e($period->id); ?>">
                                <label for="intake_id">Program <span class="text-danger">*</span></label>
                                <select onchange="getLevelAssess(this.value)" data-placeholder="Choose..." required name="programID" id="programID"
                                        class="select-search form-control">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->code.' - '.$p->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="level_idAss">Year of Study: <span class="text-danger">*</span></label>
                                <select data-placeholder="Choose..." required name="level_id" id="level_idAss"
                                        class="select-search form-control level_idAss">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-primary">Submit form <i
                                            class="icon-paperplane ml-2"></i></button>
                            </div>
                        </div>
                    </div>

                </form>

                <div class="row">
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="myChart" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="analysisCount" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
































































        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/pages/academics/class_assessments/reports/index.blade.php ENDPATH**/ ?>