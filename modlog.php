<?php
define('IN_PHPBB', true);
/**
 * modlog.php created by samy-oak-tree (2020)
 *
 * Basic modlog reading for admins.
 */
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include 'include.php';
include 'partials/template.php';
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

//
//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}

if (!is_admin($user)) include 'lost.php';

$offset = $request->variable('page', 0) * 100;
$sql = "SELECT * from gallery_logs ORDER BY log_id desc LIMIT 100 OFFSET $offset";
$result = $db->sql_query($sql);
$logs = $db->sql_fetchrowset($result);
$log_string = '';
foreach ($logs as $log) {
    $log_string .=
        '[' . str_pad(strtoupper($log['target_type'] . ':' . $log['action']), 16) . ' - ' . $log['timestamp'] . ']';
    if($log['target_type'] === 'report') {
        $log_string .=
            ' ' . $log['mod_username'] . ' ' . $log['action'] . ' report <a href="old_reports.php?r=' . $log['target_id'] . '">#' . $log['target_id'] . '</a>';

    } else {
        $log_string .=
            ' ' . $log['mod_username'] . ' ' . $log['action'] . ' ' . $log['target_type'] . ' <a href="picture.php?p=' . $log['target_id'] . '">#' . $log['target_id'] . '</a>';

    }
    if ($log['reason']) $log_string .= ' (' . $log['reason'] . ')';
    $log_string .= "\n";
}
?>
<html>
<head>
    <title>Gallery Modlog</title>
    <?= headHTML() ?>
    <link rel="stylesheet" href="css/libs/toastr.min.css">
    <script src="js/toastr.min.js"></script>
    <link rel="stylesheet" href="css/libs/lightgallery.min.css">

    <link rel="stylesheet" href="css/gallery.css">
    <script src="js/masonry.pkgd.min.js"></script>
    <script src="js/bodyScrollLock.js?a"></script>
    <script>
        window.PAGE = <?= $request->variable('page', 0); ?>;
        let queryParams = new URLSearchParams(location.search);
        const nextPage = () => {
            queryParams.set('page', PAGE + 1);
            location.search = queryParams.toString();
        }
        const goBackAPage = () => {
            if (PAGE > 0) {
                queryParams.set('page', (PAGE - 1).toString());
                location.search = queryParams.toString();
            } else {
                // do nothing
            }
        }
    </script>
    <style>
        .lg-outer {
            width: 100%;
            height: calc(100% - 50px);
        }

        .lg-outer .lg-inner {
            height: calc(100% - 50px);
            margin-top: 50px;
        }

        #lightroom_banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            min-height: 0;
            background-color: #555;
            color: #ffffff;
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        #lightroom_banner a {
            margin: 0
        }
    </style>
</head>
<body>
<?= navigationHTML($user) ?>
<?= reportBox() ?>
<div class="container menu-reactive">
    <div class="gal-nav">
        <div class="go-back">
            <a href="index.php">Categories</a> &lt; <a href="reports.php">Reports</a> < Modlog
        </div>
        <h1>Modlog</h1>
        <p style="margin: 0 0 10px 0">View logs</p>
        <div style="display: flex">
            <button style="margin: 0 10px" onclick="nextPage()">Next page</button>
            <button style="margin: 0 10px" onclick="goBackAPage()">Previous page</button>
        </div>
        <pre>Page: <?= $request->variable('page', 0); ?></pre>
    </div>
    <div>
        <pre><?= $log_string; ?></pre>
    </div>
</div>
<?= footerHTML() ?>
<script src="js/lightgallery-all.min.js"></script>
<script src="category.js"></script>
</body>

</html>
