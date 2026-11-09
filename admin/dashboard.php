<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_role('admin');
$adminName = e((string) $_SESSION['user']['full_name']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin dashboard | Northstar School</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-900/80">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4">
      <a class="flex items-center gap-3 font-semibold tracking-wide" href="dashboard.php"><span class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-500 font-black">N</span> NORTHSTAR SCHOOL</a>
      <form action="../logout.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="secondary-button" type="submit">Sign out</button>
      </form>
    </div>
  </header>
  <main class="mx-auto max-w-7xl px-5 py-10">
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
      <div><p class="text-sm font-semibold uppercase tracking-[.2em] text-indigo-300">Administrator</p><h1 class="mt-2 text-3xl font-bold">Welcome, <?= $adminName ?></h1><p class="mt-2 text-slate-400">Manage the accounts that keep your school connected.</p></div>
      <button class="primary-button" type="button" data-open-users>Manage users</button>
    </div>
    <section class="mt-9 grid gap-5 md:grid-cols-3">
      <article class="dashboard-card"><p class="text-sm text-slate-400">User access</p><h2 class="mt-3 text-xl font-semibold">One shared sign-in</h2><p class="mt-2 text-sm leading-6 text-slate-400">Students, instructors, parents, and administrators all use the same login page.</p></article>
      <article class="dashboard-card"><p class="text-sm text-slate-400">Account creation</p><h2 class="mt-3 text-xl font-semibold">Managed in one place</h2><p class="mt-2 text-sm leading-6 text-slate-400">Create student, instructor, and parent accounts from the user manager.</p></article>
      <article class="dashboard-card"><p class="text-sm text-slate-400">Security</p><h2 class="mt-3 text-xl font-semibold">Role-protected pages</h2><p class="mt-2 text-sm leading-6 text-slate-400">Passwords are hashed and each dashboard checks the authenticated account role.</p></article>
    </section>
  </main>

  <div class="modal-backdrop hidden" data-users-modal aria-hidden="true">
    <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="users-modal-title">
      <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-5 py-4 sm:px-7">
        <div><h2 id="users-modal-title" class="text-xl font-bold">Manage users</h2><p class="mt-1 text-sm text-slate-400">Create accounts, review access, or remove non-admin accounts.</p></div>
        <button class="icon-button" type="button" aria-label="Close user manager" data-close-users>&times;</button>
      </div>
      <div class="grid gap-7 p-5 sm:p-7 lg:grid-cols-[minmax(0,1.3fr)_minmax(260px,.7fr)]">
        <section>
          <div class="flex items-center justify-between gap-3"><h3 class="font-semibold">Accounts</h3><button class="text-sm font-semibold text-indigo-300 hover:text-indigo-200" type="button" data-refresh-users>Refresh</button></div>
          <p class="mt-2 text-sm text-slate-400" data-user-status role="status" aria-live="polite"></p>
          <div class="mt-3 overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full min-w-[560px] text-left text-sm">
              <thead class="bg-slate-900 text-xs uppercase tracking-wide text-slate-400"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Action</th></tr></thead>
              <tbody class="divide-y divide-slate-800" data-users-list><tr><td class="px-4 py-4 text-slate-400" colspan="4">Open the user manager to load accounts.</td></tr></tbody>
            </table>
          </div>
        </section>
        <section>
          <h3 class="font-semibold">Create account</h3>
          <form class="mt-4 space-y-4" data-add-user-form>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label class="block"><span class="mb-2 block text-sm text-slate-300">Full name</span><input class="form-input" type="text" name="full_name" minlength="2" maxlength="120" required></label>
            <label class="block"><span class="mb-2 block text-sm text-slate-300">Email address</span><input class="form-input" type="email" name="email" maxlength="190" required></label>
            <label class="block"><span class="mb-2 block text-sm text-slate-300">Role</span><select class="form-input" name="role" required><option value="student">Student</option><option value="instructor">Instructor</option><option value="parent">Parent</option></select></label>
            <label class="block"><span class="mb-2 block text-sm text-slate-300">Temporary password</span><input class="form-input" type="password" name="password" minlength="10" autocomplete="new-password" required><span class="mt-2 block text-xs text-slate-500">At least 10 characters. Share it securely with the account holder.</span></label>
            <button class="primary-button w-full" type="submit">Create account</button>
            <p class="text-sm text-slate-400" data-create-status role="status" aria-live="polite"></p>
          </form>
        </section>
      </div>
    </section>
  </div>
  <script src="../assets/js/script.js" defer></script>
</body>
</html>
