<?php
include_once 'configuration.php';
include_once 'lib/lib.php';
include_once 'lib/template.php';

$notes = new Notes( $configuration['db_host'], 
	$configuration['db_name'], 
	$configuration['db_user'], 
	$configuration['db_password'] 
	);

$notes->noteTitle = 'hahahhaha';
$notes->noteContent = 'huhuhuhuhu';

$notes->newNote();

$template = new Template;
$template->heading = "PHPRO Templates";
$template->setTemplateDir("template");
$template->display("index.html", $id);