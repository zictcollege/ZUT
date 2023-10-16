<?php
    use App\Helpers\Qs;
?>
<div class="navbar navbar-expand-lg navbar-light">
    <div class="text-center d-lg-none w-100">
        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
            <i class="icon-unfold mr-2"></i>
            More Links
        </button>
    </div>

    <div class="navbar-collapse collapse" id="navbar-footer">
					<span class="navbar-text">
						&copy; <?php echo e(date('Y')); ?>. <a href="#"><?php echo e(Qs::getSystemName()); ?></a> by <a href="#" >LV</a>
					</span>

        <ul class="navbar-nav ml-lg-auto">



        </ul>
    </div>
</div>
<?php /**PATH /Users/lavumweemba/Documents/zictcollegenewsms/zictcollege/resources/views/partials/login/footer.blade.php ENDPATH**/ ?>