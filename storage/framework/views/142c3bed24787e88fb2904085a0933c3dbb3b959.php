<?php if(Auth::user()->role_id==5): ?>  
    <ul class="nav nav-tabs">
        <?php if(Auth::user()->member->paytm_my_wallet ==1): ?>
            <li><a href="<?php echo e(route('my-wallet')); ?>" class="<?php echo e((Request::is('my-wallet') ? 'active' : '')); ?>">A2Z Plus Wallet</a></li>
        <?php endif; ?>
        <?php if(Auth::user()->member->paytm_my_wallet ==0): ?>
    	<li><a href="<?php echo e(route('premium-wallet')); ?>" class="<?php echo e((Request::is('premium-wallet') ? 'active' : '')); ?>">A2Z Wallet</a></li>
    	<?php endif; ?>
    	<li><a href="<?php echo e(route('money')); ?>" class="<?php echo e((Request::is('money') ? 'active' : '')); ?>">DMT 1</a></li>
    	<?php if(Auth::user()->member->dmt_three): ?>
       <li><a href="<?php echo e(route('dmt-two')); ?>" class="<?php echo e((Request::is('dmt-two') ? 'active' : '')); ?>">DMT 2</a></li><?php endif; ?>
       <li><a href="<?php echo e(url('account/search')); ?>" class="<?php echo e((Request::is('account/search') ? 'active' : '')); ?>">Account Search</a></li>
    </ul>
<?php endif; ?>
<br>