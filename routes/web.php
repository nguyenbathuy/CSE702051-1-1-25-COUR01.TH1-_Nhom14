<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PhieuMuonController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Require Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // --- 1. Core & Dashboard ---
    // Tuyến đường gốc (/) và Dashboard chính
    Route::get('/', [LibraryController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [LibraryController::class, 'searchCatalog'])->name('catalog.search');
    
    // --- 2. Public Catalog Routes (View only for all members) ---
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');

    // --- 3. Member-Specific Actions (Chức năng Thành viên) ---
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/profile', [MemberController::class, 'profile'])->name('profile');
        Route::get('/lending-history', [MemberController::class, 'lendingHistory'])->name('lending-history');
        
        // Tương tác với sách
        Route::post('/books/{bookItem}/reserve', [LibraryController::class, 'reserveBook'])->name('reserve');
        Route::post('/lendings/{lending}/renew', [LibraryController::class, 'renewBook'])->name('renew');
    });

    // --- 4. Posts/Social Features ---
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/share', [PostController::class, 'share'])->name('posts.share');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');


    /*
    |--------------------------------------------------------------------------
    | Librarian-Only Routes (Require 'librarian' middleware)
    |--------------------------------------------------------------------------
    */
    Route::middleware('librarian')->group(function () {

        // --- 5. Librarian Operations (Nghiệp vụ Thủ thư) ---
        Route::prefix('operations')->name('librarian.')->group(function () {
            Route::post('/issue', [LibraryController::class, 'issueBook'])->name('issue');
            Route::post('/return', [LibraryController::class, 'returnBook'])->name('return');
        });

        // --- 6. Data Management (CRUD Dữ liệu) ---
        Route::resource('books', BookController::class)->except(['index', 'show']);
        Route::resource('members', MemberController::class)->except(['index', 'show']);
        Route::resource('phieumuon', PhieuMuonController::class); // Quản lý phiếu mượn

        // Quick Admin Actions (Thao tác nhanh cho Admin)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::post('/books', [DataController::class, 'createBook'])->name('books.create');
            Route::delete('/books/{book}', [DataController::class, 'deleteBook'])->name('books.delete');
            Route::post('/members/register', [DataController::class, 'registerMember'])->name('members.register');
            Route::post('/members/{user}/cancel', [DataController::class, 'cancelMembership'])->name('members.cancel');
        });
    });
});
