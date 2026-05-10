<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\Admin\AdminLoanController;
use App\Http\Middleware\ProfileGate;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Scopes\DomainScope;
use App\Support\Settings;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Breeze Auth Routes
require __DIR__.'/auth.php';


Route::get('/test-public', function () {
    // Example domain folder
    $domainFolder = 'gotymelendingcom';
    $dir = $domainFolder . '/slides';

    // Full path using public_path()
    $fullDir = public_path("storage/{$dir}");
    echo "Full directory path: {$fullDir}<br>";

    // Ensure directory exists
    if (!file_exists($fullDir)) {
        echo "Directory does not exist. Creating...<br>";
        if (!mkdir($fullDir, 0755, true) && !is_dir($fullDir)) {
            die("Failed to create directory: {$fullDir}<br>");
        }
    } else {
        echo "Directory exists.<br>";
    }

    // Create a test file
    $filename = 'test_publicpath_' . uniqid() . '.txt';
    $filepath = $fullDir . '/' . $filename;

    try {
        file_put_contents($filepath, "This is a test file for public_path.\n");
        echo "File created successfully: {$filepath}<br>";
        echo "Public URL (should work in browser): " . url("storage/{$dir}/{$filename}") . "<br>";
    } catch (\Throwable $e) {
        echo "Failed to create file: " . $e->getMessage() . "<br>";
    }

    return 'Check output above for debug info.';
});

// Authenticated User Routes (Mobile App Logic)
Route::middleware(['auth'])->group(function () {

    // get theme from settings (domain-aware)
    $theme = Settings::get('theme', 'default');

    // --- AJAX endpoints ---
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('/notifications', [\App\Http\Controllers\AjaxController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\AjaxController::class, 'unreadCount'])->name('notifications.unread_count');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\AjaxController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\AjaxController::class, 'markAllRead'])->name('notifications.read_all');
    });

    // --- Core Mobile Navigation Placeholder Routes ---
    Route::view('/', 'home')->name('home');
    // Convenience alias to home
    Route::get('/home', function () {
        return redirect()->route('home');
    });
    Route::get('/wallets', function () use ($theme) {
        return view('wallets');
    })->name('wallets');
    Route::view('/loans', 'loans.index')->name('loans.index');
    // Themed default pages
    Route::get('/loan', function () use ($theme) {
        return view('loan');
    })->name('loan');
    Route::get('/notifications', function () use ($theme) {
        return view('notifications');
    })->name('notifications');
    Route::get('/withdrawals', function () use ($theme) {
        return view('withdrawal');
    })->name('withdrawals.index');
    Route::get('/about-us', function () use ($theme) {
        return view('about-us');
    })->name('about');
    Route::get('/contact-us', function () use ($theme) {
        return view('contact-us');
    })->name('contact-us');
    // Backward-compatible alias
    Route::get('/contact', function () {
        return redirect()->route('contact-us');
    })->name('contact');
    Route::get('/profile', function () use ($theme) {
        return view('profile');
    })->name('profile');

    // --- Loan Application Flow ---
    // Route protected by ProfileGate middleware
    Route::post('/loan/apply', [LoanController::class, 'store'])->name('loans.store');

    // --- Withdrawal Flow ---
    Route::post('/withdrawal/submit', [WithdrawalController::class, 'store'])->name('withdrawals.store');

    // --- Profile steps (update user data) ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/personal', [\App\Http\Controllers\ProfileController::class, 'editPersonal'])->name('personal');
        Route::post('/personal', [\App\Http\Controllers\ProfileController::class, 'updatePersonal'])->name('personal.update');
        Route::get('/id', [\App\Http\Controllers\ProfileController::class, 'editId'])->name('id');
        Route::post('/id', [\App\Http\Controllers\ProfileController::class, 'updateId'])->name('id.update');
        Route::get('/bank', [\App\Http\Controllers\ProfileController::class, 'editBank'])->name('bank');
        Route::post('/bank', [\App\Http\Controllers\ProfileController::class, 'updateBank'])->name('bank.update');
        Route::get('/signature', [\App\Http\Controllers\ProfileController::class, 'editSignature'])->name('signature');
        Route::post('/signature', [\App\Http\Controllers\ProfileController::class, 'updateSignature'])->name('signature.update');
    });

});


// --- Admin Auth Routes ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin login (guest:admin)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController::class, 'store']);
    });

    // Admin logout
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController::class, 'destroy'])->middleware('auth:admin')->name('logout');
});

// --- Admin Dashboard Routes ---
Route::prefix('admin')
    ->middleware(['auth:admin', AdminMiddleware::class]) // Requires admin guard AND admin role
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Admin change password
        Route::get('/password', [\App\Http\Controllers\Admin\PasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password', [\App\Http\Controllers\Admin\PasswordController::class, 'update'])->name('password.update');

        // CRUD Resources
        Route::resource('users', \App\Http\Controllers\Admin\UsersController::class)->except(['show']);
        Route::resource('loans', \App\Http\Controllers\Admin\LoansController::class)->except(['show']);
        Route::post('/loans/{loan}/review', [\App\Http\Controllers\Admin\LoansController::class, 'review'])->name('loans.review');
        Route::resource('withdrawals', \App\Http\Controllers\Admin\WithdrawalsController::class)->except(['show']);
        Route::post('/withdrawals/{withdrawal}/review', [\App\Http\Controllers\Admin\WithdrawalsController::class, 'review'])->name('withdrawals.review');
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationsController::class)->except(['show']);
        Route::resource('settings', \App\Http\Controllers\Admin\SettingsController::class)->except(['show']);
        Route::resource('domains', \App\Http\Controllers\Admin\DomainsController::class)
            ->middleware('superadmin')
            ->except(['show']);
        // Logs (read-only listing)
        Route::get('/logs', [\App\Http\Controllers\Admin\LogsController::class, 'index'])->name('logs.index');
        Route::delete('/logs/{id}', [\App\Http\Controllers\Admin\LogsController::class, 'destroy'])
            ->middleware('superadmin')
            ->name('logs.destroy');

        // Settings Panel endpoints (sectional saves)
        Route::post('/settings/panel/layout', [\App\Http\Controllers\Admin\SettingsController::class, 'saveLayout'])->name('settings.save.layout');
        Route::post('/settings/panel/loan', [\App\Http\Controllers\Admin\SettingsController::class, 'saveLoan'])->name('settings.save.loan');
        Route::post('/settings/panel/requirements', [\App\Http\Controllers\Admin\SettingsController::class, 'saveRequirements'])->name('settings.save.requirements');
        Route::post('/settings/panel/pages', [\App\Http\Controllers\Admin\SettingsController::class, 'savePages'])->name('settings.save.pages');
        Route::post('/settings/panel/faqs', [\App\Http\Controllers\Admin\SettingsController::class, 'saveFaqs'])->name('settings.save.faqs');
        Route::post('/settings/panel/welcome', [\App\Http\Controllers\Admin\SettingsController::class, 'saveWelcome'])->name('settings.save.welcome');
        // Sliders
        Route::post('/settings/panel/slider', [\App\Http\Controllers\Admin\SettingsController::class, 'uploadSlider'])->name('settings.save.slider');
        Route::delete('/settings/panel/slider', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteSlider'])->name('settings.delete.slider');

        // Users quick edit endpoints
        Route::post('/users/{user}/quick/wallet', [\App\Http\Controllers\Admin\UsersController::class, 'quickWallet'])->name('users.quick.wallet');
        Route::post('/users/{user}/quick/withdrawal-code', [\App\Http\Controllers\Admin\UsersController::class, 'quickWithdrawalCode'])->name('users.quick.withdrawal_code');
        Route::post('/users/{user}/quick/score', [\App\Http\Controllers\Admin\UsersController::class, 'quickScore'])->name('users.quick.score');
        Route::post('/users/{user}/quick/id', [\App\Http\Controllers\Admin\UsersController::class, 'quickId'])->name('users.quick.id');
        Route::post('/users/{user}/quick/password', [\App\Http\Controllers\Admin\UsersController::class, 'quickPassword'])->name('users.quick.password');
        Route::post('/users/{user}/quick/bank', [\App\Http\Controllers\Admin\UsersController::class, 'quickBank'])->name('users.quick.bank');
        Route::post('/users/{user}/quick/disable', [\App\Http\Controllers\Admin\UsersController::class, 'quickDisable'])->name('users.quick.disable');

});