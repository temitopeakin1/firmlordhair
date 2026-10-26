<?php
declare(strict_types=1);
$mode = $mode ?? 'login';
$heading = $mode === 'register' ? 'Save your details.' : 'Welcome back.';
$copy = $mode === 'register'
    ? 'A name, an email, and a phone. No password is stored in this preview.'
    : 'Use the name and email from checkout to see orders placed in this browser.';
?>
<section class="page-hero">
    <div class="narrow">
        <?php crumbs([['Home', '/'], [$mode === 'register' ? 'Register' : 'Sign in', '']]); ?>
        <h1><?= e($heading) ?></h1>
        <p class="lede"><?= e($copy) ?></p>
        <form class="form" method="post" action="<?= e(url($mode === 'register' ? 'register' : 'login')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="account">
            <input type="hidden" name="back" value="<?= e($mode === 'register' ? 'register' : 'login') ?>">
            <?php
            $fields = $mode === 'register'
                ? ['name' => 'Full name', 'email' => 'Email', 'phone' => 'Phone']
                : ['name' => 'Full name', 'email' => 'Email'];
            foreach ($fields as $key => $label):
                $error = field_error($key);
                ?>
                <label class="field<?= $error ? ' is-bad' : '' ?>">
                    <?= e($label) ?>
                    <input name="<?= e($key) ?>" type="<?= $key === 'email' ? 'email' : 'text' ?>" value="<?= e(old_value($key)) ?>"<?= $key === 'phone' ? '' : ' required' ?>>
                    <?php if ($error): ?><small><?= e($error) ?></small><?php endif; ?>
                </label>
            <?php endforeach; ?>
            <button class="btn" type="submit"><?= $mode === 'register' ? 'Save details' : 'Continue' ?></button>
            <?php if ($mode === 'register'): ?>
                <p>Already have details? <a class="text-btn" href="<?= e(url('login')) ?>">Sign in</a></p>
            <?php else: ?>
                <p>New here? <a class="text-btn" href="<?= e(url('register')) ?>">Create an account</a></p>
            <?php endif; ?>
        </form>
    </div>
</section>
