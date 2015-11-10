<?php

var_dump($_POST);

if (!isset($_POST['page_id']) || !is_numeric($_POST['page_id'])/* || !isset($_SESSION['user_id'])*/) return;

$db->query('UPDATE temp_pages SET last_access=NOW() WHERE id='  . $_POST['page_id']/* . ' AND user_id=' . $_SESSION['user_id']*/);
?>