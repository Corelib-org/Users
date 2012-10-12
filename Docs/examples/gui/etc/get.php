<?php
$pages['/users/'] = array('page' => 'lib/http/get/user.php',
                          'exec' => 'UserList');

$pages[] = array('type' => 'regex',
                 'expr' => '/^\/users\/([0-9]+)\/edit\/$/',
                 'exec' => 'edit(\\1)',
                 'page' => 'lib/http/get/user.php');

$pages['/users/create/'] = array('page' => 'lib/http/get/user.php',
                                 'exec' => 'create');

$pages[] = array('type' => 'regex',
                 'expr' => '/^\/users\/([0-9]+)\/delete\/$/',
                 'exec' => 'delete(\\1)',
                 'page' => 'lib/http/get/user.php');
?>
