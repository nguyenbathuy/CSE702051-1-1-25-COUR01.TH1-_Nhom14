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
    Route::get('/', [LibraryController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [LibraryController::class, 'searchCatalog'])->name('catalog.search');

    // --- 2. Book Management (Sử dụng Resource Route) ---
    // Khai báo resource cho books. Tất cả 7 route (index, create, store, show, edit, update, destroy)
    // đều được khai báo TẠI ĐÂY (dưới middleware 'auth').
    // Việc phân quyền (chỉ librarian mới được create, store, edit, update, destroy)
    // SẼ ĐƯỢC XỬ LÝ TRONG FILE BookController.php (thường dùng hàm `__construct` hoặc FormRequest).
    Route::resource('books', BookController::class); 

    // --- 3. Public Catalog Routes (Members) ---
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');

    // --- 4. Member-Specific Actions (Chức năng Thành viên) ---
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/profile', [MemberController::class, 'profile'])->name('profile');
        Route::get('/lending-history', [MemberController::class, 'lendingHistory'])->name('lending-history');
        
        // Tương tác với sách
        Route::post('/books/{bookItem}/reserve', [LibraryController::class, 'reserveBook'])->name('reserve');
        Route::post('/lendings/{lending}/renew', [LibraryController::class, 'renewBook'])->name('renew');
    });

    // --- 5. Posts/Social Features ---
    Route::resource('posts', PostController::class);
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/share', [PostController::class, 'share'])->name('posts.share');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');


    /*
    |--------------------------------------------------------------------------
    | Librarian-Only Routes (Require 'librarian' middleware)
    |--------------------------------------------------------------------------
    | Các route này vẫn cần middleware 'librarian' vì chúng KHÔNG thuộc resource mặc định.
    */
    Route::middleware('librarian')->group(function () {

        // --- 6. Librarian Operations (Nghiệp vụ Thủ thư) ---
        Route::prefix('operations')->name('librarian.')->group(function () {
            Route::post('/issue', [LibraryController::class, 'issueBook'])->name('issue');
            Route::post('/return', [LibraryController::class, 'returnBook'])->name('return');
        });

        // --- 7. Member and Lending Management ---
        Route::resource('members', MemberController::class)->except(['index', 'show']);
        Route::resource('phieumuon', PhieuMuonController::class); // Quản lý phiếu mượn

        // Quick Admin Actions (Thao tác nhanh cho Admin)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::post('/books', [DataController::class, 'createBook'])->name('books.create'); // Cần kiểm tra lại mục đích route này
            Route::delete('/books/{book}', [DataController::class, 'deleteBook'])->name('books.delete'); // Cần kiểm tra lại mục đích route này
            Route::post('/members/register', [DataController::class, 'registerMember'])->name('members.register');
            Route::post('/members/{user}/cancel', [DataController::class, 'cancelMembership'])->name('members.cancel');
        });
    });
});
