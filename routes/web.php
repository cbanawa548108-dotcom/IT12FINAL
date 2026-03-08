<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierTransactionController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\BlockedIpController;
use App\Http\Controllers\PasswordChangeLogController;

// Root → login
Route::get('/', fn() => redirect()->route('login'));

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('password.email');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');
});

// ── 2FA + CAPTCHA ─────────────────────────────────────────────────────────────
Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor.show');
Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');
Route::post('/two-factor/resend', [AuthController::class, 'resendTwoFactor'])->name('two-factor.resend');
Route::get('/captcha', [AuthController::class, 'showCaptcha'])->name('captcha.show');
Route::post('/captcha', [AuthController::class, 'verifyCaptcha'])->name('captcha.verify');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/force-logout', [AuthController::class, 'forceLogout'])->name('force.logout')->middleware('auth');

// ── Dashboard ─────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ── Inventory & Products (Admin + Manager) ────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/inventory/reports', [InventoryReportController::class, 'index'])->name('inventory.reports');
    Route::get('/inventory/reports/data', [InventoryReportController::class, 'getData'])->name('inventory.reports.data');
    Route::get('/inventory/reports/export', [InventoryReportController::class, 'export'])->name('inventory.reports.export');

    Route::get('/inventory/audit', [InventoryController::class, 'audit'])->name('inventory.audit');
    Route::get('/inventory/{product}/edit-history', [InventoryController::class, 'editHistory'])->name('inventory.edit-history');

    Route::resource('inventory', InventoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('stockins', StockInController::class);
    Route::resource('suppliers', SupplierController::class);

    Route::get('/suppliers/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
    Route::post('/suppliers/restore/{id}', [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::delete('/suppliers/force-delete/{id}', [SupplierController::class, 'forceDelete'])->name('suppliers.forceDelete');
});

// ── Customers & Sales (Admin + Cashier) ───────────────────────────────────────
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::get('/sales/report', [SalesTransactionController::class, 'report'])->name('sales.report');
    Route::get('/sales/report/export', [SalesTransactionController::class, 'export'])->name('sales.report.export');

    Route::get('/sales/walk-in/create', [SalesTransactionController::class, 'createWalkIn'])->name('sales.walkIn.create');
    Route::post('/sales/walk-in', [SalesTransactionController::class, 'storeWalkIn'])->name('sales.walkIn.store');

    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SalesTransactionController::class)->except(['show']);

    Route::put('sales/{sale}/paid', [SalesTransactionController::class, 'markPaid'])->name('sales.markPaid');
    Route::get('/sales/{sale}/print', [SalesTransactionController::class, 'printReceipt'])->name('sales.printReceipt');
    Route::get('/sales/{sale}/details', [SalesTransactionController::class, 'details'])->name('sales.details');

    Route::get('/customers/archive', [CustomerController::class, 'archive'])->name('customers.archive');
    Route::post('/customers/restore/{id}', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('/customers/force-delete/{id}', [CustomerController::class, 'forceDelete'])->name('customers.forceDelete');
});

// ── Users, Employees, Audit, IP Blocking, Password Logs (Admin only) ──────────
Route::middleware(['auth', 'role:admin'])->group(function () {

    // ── User CRUD ─────────────────────────────────────────────
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/archived', [UserController::class, 'archived'])->name('users.archived');
    Route::get('/users/create-cashier', [UserController::class, 'createCashier'])->name('users.create-cashier');
    Route::post('/users/create-cashier', [UserController::class, 'storeCashier'])->name('users.store-cashier');
    Route::get('/users/create-manager', [UserController::class, 'createManager'])->name('users.create-manager');
    Route::post('/users/create-manager', [UserController::class, 'storeManager'])->name('users.store-manager');
    Route::get('/users/cashiers', [UserController::class, 'listCashiers'])->name('users.list-cashiers');
    Route::get('/users/managers', [UserController::class, 'listManagers'])->name('users.list-managers');

    // ── Edit / Update ─────────────────────────────────────────
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // ── Archive / Restore / Force Delete ─────────────────────
    Route::post('/users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ── Employees ─────────────────────────────────────────────
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');

    // ── Login Audit ───────────────────────────────────────────
    Route::get('/login-audit-logs', [AuthController::class, 'loginAuditLogs'])->name('login.audit');

    // ── IP Blocking ───────────────────────────────────────────
    Route::get('/blocked-ips', [BlockedIpController::class, 'index'])->name('blocked-ips.index');
    Route::post('/blocked-ips', [BlockedIpController::class, 'store'])->name('blocked-ips.store');
    Route::delete('/blocked-ips/{id}', [BlockedIpController::class, 'destroy'])->name('blocked-ips.destroy');

    // ── Password Change Logs ──────────────────────────────────
    Route::get('/password-change-logs', [PasswordChangeLogController::class, 'index'])->name('password-change-logs.index');
});

// ── Supplier Transactions (Admin + Manager) ───────────────────────────────────
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/supplier-transactions', [SupplierTransactionController::class, 'index'])->name('supplier.transactions');
    Route::get('/supplier-transactions/create', [SupplierTransactionController::class, 'create'])->name('supplier-transactions.create');
    Route::post('/supplier-transactions', [SupplierTransactionController::class, 'store'])->name('supplier-transactions.store');
    Route::get('/supplier-transactions/{supplier_transaction}/edit', [SupplierTransactionController::class, 'edit'])->name('supplier-transactions.edit');
    Route::put('/supplier-transactions/{supplier_transaction}', [SupplierTransactionController::class, 'update'])->name('supplier-transactions.update');
    Route::delete('/supplier-transactions/{supplier_transaction}', [SupplierTransactionController::class, 'destroy'])->name('supplier-transactions.destroy');
    Route::put('/supplier-transactions/{supplier_transaction}/pay', [SupplierTransactionController::class, 'pay'])->name('supplier-transactions.pay');
    Route::get('/supplier-transactions/{supplier_transaction}/receipt', [SupplierTransactionController::class, 'printReceipt'])->name('supplier-transactions.receipt');

    Route::get('/supplier-transactions/products/{supplier}', [SupplierTransactionController::class, 'getProductsBySupplier'])->name('supplier-transactions.products');
    Route::get('/supplier-transactions/varieties/{product}', [SupplierTransactionController::class, 'getVarietiesByProduct'])->name('supplier-transactions.varieties');
});

// ── Archive Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/archive/suppliers', [SupplierController::class, 'archive'])->name('archive.suppliers');
});

Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::get('/archive/customers', [CustomerController::class, 'archive'])->name('archive.customers');
});