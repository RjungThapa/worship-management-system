<?php
// Optional variables for flexibility
$page_title = $page_title ?? 'Worship Management System';
$css_path = $css_path ?? '../css/style.css';
$body_class = $body_class ?? 'bg-light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    <style>
        /* Ensure breathing gap below navbar */
        body { padding-top: 5rem; }
    </style>
</head>
<body class="<?php echo $body_class; ?>">
