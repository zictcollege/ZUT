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

        <div class="card-body">
            <div class="container">

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
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Results Reports</h6>
            <?php echo Qs::getPanelOptions(); ?>

        </div>

        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div style="text-align: center;">
                            <canvas id="BestBasedOnClass" style="max-width: 500px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/lavumweemba/Documents/test/zictcollege 08-10-40-916/resources/views/pages/academics/class_assessments/reports/index.blade.php ENDPATH**/ ?>