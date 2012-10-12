<?php
$pages[] = array('type' => 'regex',
                 'expr' => '/^\/users\/([0-9]+)\/edit\/$/',
                 'exec' => 'edit(\\1)',
                 'page' => 'lib/http/post/user.php');

$pages['/users/create/'] = array('page' => 'lib/http/post/user.php',
                                 'exec' => 'create');
?>