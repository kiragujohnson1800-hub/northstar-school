<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_role('student');
$studentName = e((string) $_SESSION['user']['full_name']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student dashboard | Northstar School</title><script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-900/80"><div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4"><a class="flex items-center gap-3 font-semibold tracking-wide" href="dashboard.php"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-500 font-black">N</span> NORTHSTAR SCHOOL</a><form action="../logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="secondary-button" type="submit">Sign out</button></form></div></header>
  <main class="mx-auto max-w-7xl px-5 py-12"><p class="text-sm font-semibold uppercase tracking-[.2em] text-indigo-300">Student dashboard</p><h1 class="mt-3 text-3xl font-bold">Welcome, <?= $studentName ?></h1><p class="mt-3 max-w-2xl text-slate-400">You’re signed in to your Northstar School student account.</p><section class="mt-9 grid gap-5 md:grid-cols-2"><article class="dashboard-card"><p class="text-sm font-semibold text-indigo-300">Your account</p><h2 class="mt-3 text-xl font-semibold">Student access</h2><p class="mt-2 text-sm leading-6 text-slate-400">Your account is ready. Contact your school for class and enrollment information.</p></article><article class="dashboard-card"><p class="text-sm font-semibold text-indigo-300">Need help?</p><h2 class="mt-3 text-xl font-semibold">We’re here to help</h2><p class="mt-2 text-sm leading-6 text-slate-400">Contact your school administrator if your account details need to be updated.</p></article></section></main>
</body>
</html>
