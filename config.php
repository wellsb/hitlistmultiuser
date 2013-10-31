<?php
// - Notes - - - - - - - -
// for ramp -> 500000 microseconds = 0.5 seconds
// for ramp -> 1000000 = 1 seconds
// hit_delay_lower \ hit_delay_upper is in seconds (it has a .0-9 put on it too)

// - Config - - - - - - - -
$config = array (
    'base_url' => 'http://192.168.5.201/moodle241',
    'killfile' => 'kill',
    'users' => 20,
    'hit_concurrent' => 5,
    'hit_delay_lower' => 5,
    'hit_delay_upper' => 60,
    'ramp' => 500000,
    'firstpageneedle' => 'test01',
    'renderpage' => false,
    'sessionfiles' => 'sessions');
date_default_timezone_set('Europe/London');
// - Config - - - - - - - -

// - Pages to hit - - - - - -
$hitlist = array (
    'forum' => array ('http://192.168.5.201/moodle241/mod/forum/view.php?id=3' => 'XhitlistdiscussionX'),
    'chat' => array ('http://192.168.5.201/moodle241/mod/chat/view.php?id=4' => 'XhitlistchatX'),
    'page' => array ('http://192.168.5.201/moodle241/mod/page/view.php?id=5' => 'XhitlistpageX'),
    'wiki' => array ('http://192.168.5.201/moodle241/mod/wiki/view.php?pageid=1&group=0' => 'XhitlistwikiX')
);
// - Pages to hit - - - - - -


//$hitlist = array (
//    'forum' => array ('http://192.168.5.201/moodle241/mod/forum/view.php?id=3' => 'XhitlistdiscussionX'),
//    'chat' => array ('http://192.168.5.201/moodle241/mod/chat/view.php?id=4' => 'XhitlistchatX'),
//    'page' => array ('http://192.168.5.201/moodle241/mod/page/view.php?id=5' => 'XhitlistpageX'),
//    'wiki' => array ('http://192.168.5.201/moodle241/mod/wiki/view.php?pageid=1&group=0' => 'XhitlistwikiX'),
//    'My' => array ('http://192.168.5.201/moodle241/my/' => 'overview'),
//    'Courses' => array ('http://192.168.5.201/moodle241/course/index.php?categoryedit=on' => 'Miscellaneous'),
//    'Users' => array ('http://192.168.5.201/moodle241/admin/user.php' => 'brendan')
//);
?>
