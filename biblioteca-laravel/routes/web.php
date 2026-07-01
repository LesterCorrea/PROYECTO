<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\BookController;
use App\Http\Controllers\Public\MagazineController;
use App\Http\Controllers\Public\CollectionController;
use App\Http\Controllers\Public\AuthorController;
use App\Http\Controllers\Public\PdfController;
use App\Http\Controllers\Public\CommentController;
use App\Http\Controllers\Student\ReservationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Librarian\DashboardController as LibrarianDashboard;
use App\Http\Controllers\Librarian\BookManagerController;
use App\Http\Controllers\Librarian\MagazineManagerController;
use App\Http\Controllers\Librarian\AuthorManagerController;
use App\Http\Controllers\Librarian\CategoryManagerController;
use App\Http\Controllers\Librarian\PublisherManagerController;
use App\Http\Controllers\Librarian\CollectionManagerController;
use App\Http\Controllers\Librarian\LoanController;
use App\Http\Controllers\Librarian\FineController;
use App\Http\Controllers\Librarian\FeaturedListController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Librarian\QuickCreateController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// RUTAS PÚBLICAS — accesibles sin login
// ══════════════════════════════════════════════════════════════
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('libros')->name('books.')->group(function () {
     Route::get('/', [BookController::class, 'index'])->name('index');
     Route::get('/{book:isbn}', [BookController::class, 'show'])->name('show');
     Route::get('/buscar', [BookController::class, 'search'])->name('search');
});

Route::prefix('revistas')->name('magazines.')->group(function () {
     Route::get('/', [MagazineController::class, 'index'])->name('index');
     Route::get('/{magazine}', [MagazineController::class, 'show'])->name('show');
     Route::get('/buscar', [MagazineController::class, 'search'])->name('search');
});

Route::prefix('colecciones')->name('collections.')->group(function () {
     Route::get('/', [CollectionController::class, 'index'])->name('index');
     Route::get('/{collection:slug}', [CollectionController::class, 'show'])->name('show');
});

Route::prefix('autores')->name('authors.')->group(function () {
     Route::get('/', [AuthorController::class, 'index'])->name('index');
     Route::get('/{author}', [AuthorController::class, 'show'])->name('show');
});

// ══════════════════════════════════════════════════════════════
// RUTAS PROTEGIDAS — requieren login
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->group(function () {

     // PDF protegido — nunca URL directa
     Route::get('/leer/{type}/{id}', [PdfController::class, 'serve'])
          ->name('pdf.serve')
          ->middleware('permission:read pdf');

     // Lector de PDF embebido
     Route::get('/leer/{type}/{id}/visor', [PdfController::class, 'reader'])
          ->name('pdf.reader')
          ->middleware('permission:read pdf');

     // Progreso de lectura (AJAX)
     Route::post('/leer/progreso', [PdfController::class, 'updateProgress'])
          ->name('pdf.progress');

     // Comentarios
     Route::post('/comentarios/{type}/{id}', [CommentController::class, 'store'])
          ->name('comments.store');
     Route::delete('/comentarios/{comment}', [CommentController::class, 'destroy'])
          ->name('comments.destroy');

     // ── Panel del Estudiante ────────────────────────────────
     Route::prefix('mi-cuenta')->name('student.')->middleware('role:student')->group(function () {
          Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
          Route::get('/reservas', [ReservationController::class, 'index'])->name('reservations.index');
          Route::post('/reservas/{book}', [ReservationController::class, 'store'])->name('reservations.store');

          Route::post('/reservas/revista/{magazine}', [ReservationController::class, 'storeMagazine'])
               ->name('reservations.store.magazine');

          Route::delete('/reservas/{reservation}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
          Route::get('/multas', [StudentDashboard::class, 'fines'])->name('fines');
          Route::get('/historial', [StudentDashboard::class, 'history'])->name('history');
     });

     // ── Panel del Bibliotecario ─────────────────────────────
     Route::prefix('bibliotecario')->name('librarian.')->middleware('role:librarian|admin')->group(function () {
          Route::get('/dashboard', [LibrarianDashboard::class, 'index'])->name('dashboard');

          // Libros
          Route::resource('libros', BookManagerController::class);

          // Revistas
          Route::resource('revistas', MagazineManagerController::class);

          // Autores
          Route::resource('autores', AuthorManagerController::class);

          // Categorías
          Route::resource('categorias', CategoryManagerController::class);

          // Editoriales
          // Route::resource('editoriales', PublisherManagerController::class);
          Route::resource('editoriales', PublisherManagerController::class)
               ->parameters([
                    'editoriales' => 'editorial'
               ]);

          // Carruseles
          Route::get('/carruseles/{list}/buscar', [FeaturedListController::class, 'searchItems'])
               ->name('carruseles.search');

          // Colecciones / Sagas
          Route::resource('colecciones', CollectionManagerController::class);
          Route::post('/colecciones/{collection}/libros', [CollectionManagerController::class, 'addBook'])
               ->name('colecciones.addBook');
          Route::delete('/colecciones/{collection}/libros/{book}', [CollectionManagerController::class, 'removeBook'])
               ->name('colecciones.removeBook');

          Route::patch('/colecciones/{collection}/libros/reordenar', [CollectionManagerController::class, 'reorderBooks'])
               ->name('colecciones.reorderBooks');
          Route::get('/colecciones/{collection}/libros/buscar', [CollectionManagerController::class, 'searchBooks'])
               ->name('colecciones.searchBooks');

          // Reservas
          Route::get('/reservas', [LoanController::class, 'reservations'])->name('reservations');
          Route::patch('/reservas/{reservation}/confirmar', [LoanController::class, 'confirmReservation'])
               ->name('reservations.confirm');
          Route::patch('/reservas/{reservation}/rechazar', [LoanController::class, 'rejectReservation'])
               ->name('reservations.reject');

          // Préstamos
          Route::get('/prestamos', [LoanController::class, 'index'])->name('loans.index');
          Route::post('/prestamos', [LoanController::class, 'store'])->name('loans.store');
          Route::patch('/prestamos/{loan}/devolver', [LoanController::class, 'returnBook'])
               ->name('loans.return');

          // Multas
          Route::get('/multas', [FineController::class, 'index'])->name('fines.index');
          Route::patch('/multas/{fine}/pagar', [FineController::class, 'markAsPaid'])
               ->name('fines.pay');

          // Carruseles (listas circulares)
          Route::resource('carruseles', FeaturedListController::class);
          Route::post('/carruseles/{list}/items', [FeaturedListController::class, 'addItem'])
               ->name('carruseles.addItem');
          Route::delete('/carruseles/{list}/items/{item}', [FeaturedListController::class, 'removeItem'])
               ->name('carruseles.removeItem');

          Route::patch('/carruseles/{list}/items/reordenar', [FeaturedListController::class, 'reorder'])
               ->name('carruseles.reorder');

          // Quick create (respuestas JSON para modales inline)
          Route::prefix('quick-create')->name('quick.')->group(function () {
               Route::post('/author',     [QuickCreateController::class, 'author'])->name('author');
               Route::post('/category',   [QuickCreateController::class, 'category'])->name('category');
               Route::post('/publisher',  [QuickCreateController::class, 'publisher'])->name('publisher');
               Route::post('/collection', [QuickCreateController::class, 'collection'])->name('collection');
          });
     });

     // ── Panel del Administrador ─────────────────────────────
     Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
          Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

          // Usuarios
          Route::resource('usuarios', UserController::class);
          Route::patch('/usuarios/{user}/toggle', [UserController::class, 'toggleActive'])
               ->name('usuarios.toggle');
          Route::patch('/usuarios/{user}/rol', [UserController::class, 'changeRole'])
               ->name('usuarios.role');

          // Reportes
          Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
          Route::get('/reportes/prestamos', [ReportController::class, 'loans'])->name('reports.loans');
          Route::get('/reportes/multas', [ReportController::class, 'fines'])->name('reports.fines');
          Route::get('/reportes/usuarios', [ReportController::class, 'users'])->name('reports.users');
          Route::get('/reportes/exportar/{type}', [ReportController::class, 'export'])
               ->name('reports.export');

          // Logs de actividad
          Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
     });
});

// ══════════════════════════════════════════════════════════════
// FIX 4 — Búsquedas AJAX para el modal de préstamos
// ══════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:librarian|admin'])->prefix('api')->group(function () {
     Route::get('/buscar-usuarios', [LoanController::class, 'searchUsers']);
     Route::get('/buscar-items',    [LoanController::class, 'searchLoanItems']);
});

// Auth routes (login, register, etc.) — generadas por Breeze
require __DIR__ . '/auth.php';
