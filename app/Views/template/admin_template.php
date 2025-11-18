<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php if (auth()->loggedIn()) { ?>

            <?php include 'menu.php'; ?>

        <?php } ?>

        <main class="app-main">
            <?= $this->renderSection('content') ?>
        </main>

        <?php if (auth()->loggedIn()) { ?>

            <?php include 'footer.php'; ?>

        <?php } ?>

        <aside class="control-sidebar control-sidebar-dark">

        </aside>

    </div>

    <?php include 'scripts.php'; ?>
    <?= $this->renderSection('javascripts') ?>
</body>

</html>