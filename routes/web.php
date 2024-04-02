<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ShopController::class, 'index'])->name('shops.index');
Route::get('/search', [ShopController::class, 'search'])->name('shops.search');
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('shops.category');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('shops.product');

Route::get('/cart', [CartController::class, 'index'])->name('carts.index')->middleware(['auth', 'role:User']);
Route::post('/cart/{slug}', [CartController::class, 'store'])->name('carts.store')->middleware(['auth', 'role:User']);
Route::put('/cart/{id}/add', [CartController::class, 'add'])->name('carts.add')->middleware(['auth', 'role:User']);
Route::put('/cart/{id}/remove', [CartController::class, 'remove'])->name('carts.remove')->middleware(['auth', 'role:User']);
Route::delete('/cart/{id}/delete', [CartController::class, 'destroy'])->name('carts.destroy')->middleware(['auth', 'role:User']);

Route::post('/transaction', [TransactionController::class, 'store'])->name('transactions.store')->middleware(['auth', 'role:User']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', [ProfileController::class, 'index'])->name('profiles.index')->middleware('auth');
Route::put('/profile', [ProfileController::class, 'update'])->name('profiles.update')->middleware('auth');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', 'role:Admin']);

Route::get('/admin/category', [CategoryController::class, 'index'])->name('categories.index')->middleware(['auth', 'role:Admin']);
Route::get('/admin/category/create', [CategoryController::class, 'create'])->name('categories.create')->middleware(['auth', 'role:Admin']);
Route::post('/admin/category', [CategoryController::class, 'store'])->name('categories.store')->middleware(['auth', 'role:Admin']);
Route::get('/admin/category/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware(['auth', 'role:Admin']);
Route::put('/admin/category/{id}', [CategoryController::class, 'update'])->name('categories.update')->middleware(['auth', 'role:Admin']);
Route::delete('/admin/category/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware(['auth', 'role:Admin']);

Route::get('/admin/product', [ProductController::class, 'index'])->name('products.index')->middleware(['auth', 'role:Admin']);
Route::get('/admin/product/create', [ProductController::class, 'create'])->name('products.create')->middleware(['auth', 'role:Admin']);
Route::post('/admin/product', [ProductController::class, 'store'])->name('products.store')->middleware(['auth', 'role:Admin']);
Route::get('/admin/product/{id}/show', [ProductController::class, 'show'])->name('products.show')->middleware(['auth', 'role:Admin']);
Route::get('/admin/product/{id}/edit', [ProductController::class, 'edit'])->name('products.edit')->middleware(['auth', 'role:Admin']);
Route::put('/admin/product/{id}', [ProductController::class, 'update'])->name('products.update')->middleware(['auth', 'role:Admin']);
Route::delete('/admin/product/{id}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware(['auth', 'role:Admin']);

Route::get('/admin/membership', [MembershipController::class, 'index'])->name('memberships.index')->middleware(['auth', 'role:Admin']);
Route::get('/admin/membership/create', [MembershipController::class, 'create'])->name('memberships.create')->middleware(['auth', 'role:Admin']);
Route::post('/admin/membership', [MembershipController::class, 'store'])->name('memberships.store')->middleware(['auth', 'role:Admin']);
Route::get('/admin/membership/{id}/edit', [MembershipController::class, 'edit'])->name('memberships.edit')->middleware(['auth', 'role:Admin']);
Route::put('/admin/membership/{id}', [MembershipController::class, 'update'])->name('memberships.update')->middleware(['auth', 'role:Admin']);
Route::delete('/admin/membership/{id}', [MembershipController::class, 'destroy'])->name('memberships.destroy')->middleware(['auth', 'role:Admin']);

Route::get('/user/histori-membership', [MembershipController::class, 'my'])->name('memberships.my')->middleware(['auth', 'role:User']);
Route::get('/user/membership', [MembershipController::class, 'transaction'])->name('memberships.transaction')->middleware(['auth', 'role:User']);
Route::post('/user/membership', [MembershipController::class, 'purchase'])->name('memberships.purchase')->middleware(['auth', 'role:User']);

Route::get('/admin/membership/list', [MembershipController::class, 'list'])->name('memberships.list')->middleware(['auth', 'role:Admin']);
Route::get('/admin/membership/{id}/activation', [MembershipController::class, 'activation'])->name('memberships.activation')->middleware(['auth', 'role:Admin']);
Route::put('/admin/membership/{id}/activate', [MembershipController::class, 'activate'])->name('memberships.activate')->middleware(['auth', 'role:Admin']);