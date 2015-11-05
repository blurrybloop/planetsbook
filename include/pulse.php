<?php
if (!isset($_POST['page_id']) || !is_numeric($_POST['page_id'])) return;
$db->query('UPDATE temp_pages SET last_access=NOW() WHERE id='  . $_POST['page_id']);
?>