<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_role('parent');
$parentName = e((string) $_SESSION['user']['full_name']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parent dashboard | Northstar School</title><script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-900/80"><div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4"><a class="flex items-center gap-3 font-semibold tracking-wide" href="dashboard.php"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-500 font-black">N</span> NORTHSTAR SCHOOL</a><form action="../logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="secondary-button" type="submit">Sign out</button></form></div></header>
  <main class="mx-auto max-w-7xl px-5 py-12"><p class="text-sm font-semibold uppercase tracking-[.2em] text-indigo-300">Parent dashboard</p><h1 class="mt-3 text-3xl font-bold">Welcome, <?= $parentName ?></h1><p class="mt-3 max-w-2xl text-slate-400">You’re signed in to your Northstar School parent account.</p><section class="mt-9 grid gap-5 md:grid-cols-2"><article class="dashboard-card"><p class="text-sm font-semibold text-indigo-300">Your account</p><h2 class="mt-3 text-xl font-semibold">Parent access</h2><p class="mt-2 text-sm leading-6 text-slate-400">Your school account is active. Contact the school office for family and student information.</p></article><article class="dashboard-card"><p class="text-sm font-semibold text-indigo-300">Need help?</p><h2 class="mt-3 text-xl font-semibold">Contact your school</h2><p class="mt-2 text-sm leading-6 text-slate-400">The school administrator can help with account access and profile details.</p></article></section></main>
</body>
</html>
