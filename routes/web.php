<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminUserPermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BirTaxController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CorporateDocumentDefaultsController;
use App\Http\Controllers\MinuteController;
use App\Http\Controllers\NatGovController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SecretaryCertificateController;
use App\Http\Controllers\StockTransferCertificateController;
use App\Http\Controllers\StockTransferInstallmentController;
use App\Http\Controllers\StockTransferJournalController;
use App\Http\Controllers\StockTransferLedgerController;
use App\Http\Controllers\StockTransferLookupController;
use App\Http\Controllers\UploadedFileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/uploads/{path}', [UploadedFileController::class, 'show'])
        ->where('path', '.*')
        ->name('uploads.show');

    /*
    |----------------------------------------------------------------------
    | Admin
    |----------------------------------------------------------------------
    */
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions');
    Route::post('/admin/role-permissions/{id}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
    Route::get('/admin/user-permissions', [AdminUserPermissionController::class, 'index'])->name('admin.user-permissions');
    Route::get('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'edit'])->name('admin.user-permissions.edit');
    Route::post('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'update'])->name('admin.user-permissions.update');

    /*
    |----------------------------------------------------------------------
    | Retained Corporate Modules
    |----------------------------------------------------------------------
    */
    Route::get('/corporate', function () {
        return redirect()->route('stock-transfer-book');
    })->name('corporate');

    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/stock-transfer-book', function () {
        return redirect()->route('stock-transfer-book.journal');
    })->name('stock-transfer-book');

    Route::get('/stock-transfer-book/ledger', [StockTransferLedgerController::class, 'index'])->name('stock-transfer-book.ledger');
    Route::get('/stock-transfer-book/ledger/create', [StockTransferLedgerController::class, 'create'])->name('stock-transfer-book.ledger.create');
    Route::post('/stock-transfer-book/ledger', [StockTransferLedgerController::class, 'store'])->name('stock-transfer-book.ledger.store');
    Route::get('/stock-transfer-book/ledger/{stockTransferLedger}', [StockTransferLedgerController::class, 'show'])->name('stock-transfer-book.ledger.show');
    Route::get('/stock-transfer-book/ledger/{stockTransferLedger}/edit', [StockTransferLedgerController::class, 'edit'])->name('stock-transfer-book.ledger.edit');
    Route::put('/stock-transfer-book/ledger/{stockTransferLedger}', [StockTransferLedgerController::class, 'update'])->name('stock-transfer-book.ledger.update');
    Route::delete('/stock-transfer-book/ledger/{stockTransferLedger}', [StockTransferLedgerController::class, 'destroy'])->name('stock-transfer-book.ledger.destroy');
    Route::get('/stock-transfer-book/index', [StockTransferLedgerController::class, 'indexPage'])->name('stock-transfer-book.index');

    Route::get('/stock-transfer-book/journal', [StockTransferJournalController::class, 'index'])->name('stock-transfer-book.journal');
    Route::get('/stock-transfer-book/journal/create', [StockTransferJournalController::class, 'create'])->name('stock-transfer-book.journal.create');
    Route::post('/stock-transfer-book/journal', [StockTransferJournalController::class, 'store'])->name('stock-transfer-book.journal.store');
    Route::get('/stock-transfer-book/journal/{stockTransferJournal}', [StockTransferJournalController::class, 'show'])->name('stock-transfer-book.journal.show');
    Route::get('/stock-transfer-book/journal/{stockTransferJournal}/edit', [StockTransferJournalController::class, 'edit'])->name('stock-transfer-book.journal.edit');
    Route::put('/stock-transfer-book/journal/{stockTransferJournal}', [StockTransferJournalController::class, 'update'])->name('stock-transfer-book.journal.update');
    Route::delete('/stock-transfer-book/journal/{stockTransferJournal}', [StockTransferJournalController::class, 'destroy'])->name('stock-transfer-book.journal.destroy');

    Route::get('/stock-transfer-book/installment', [StockTransferInstallmentController::class, 'index'])->name('stock-transfer-book.installment');
    Route::get('/stock-transfer-book/installment/create', [StockTransferInstallmentController::class, 'create'])->name('stock-transfer-book.installment.create');
    Route::post('/stock-transfer-book/installment', [StockTransferInstallmentController::class, 'store'])->name('stock-transfer-book.installment.store');
    Route::get('/stock-transfer-book/installment/{stockTransferInstallment}', [StockTransferInstallmentController::class, 'show'])->name('stock-transfer-book.installment.show');
    Route::post('/stock-transfer-book/installment/{stockTransferInstallment}/payments', [StockTransferInstallmentController::class, 'recordPreviewPayment'])->name('stock-transfer-book.installment.payments.store');
    Route::get('/stock-transfer-book/installment/{stockTransferInstallment}/edit', [StockTransferInstallmentController::class, 'edit'])->name('stock-transfer-book.installment.edit');
    Route::put('/stock-transfer-book/installment/{stockTransferInstallment}', [StockTransferInstallmentController::class, 'update'])->name('stock-transfer-book.installment.update');
    Route::post('/stock-transfer-book/installment/{stockTransferInstallment}/cancel', [StockTransferInstallmentController::class, 'cancelInstallment'])->name('stock-transfer-book.installment.cancel');
    Route::delete('/stock-transfer-book/installment/{stockTransferInstallment}', [StockTransferInstallmentController::class, 'destroy'])->name('stock-transfer-book.installment.destroy');

    Route::get('/stock-transfer-book/certificates', [StockTransferCertificateController::class, 'index'])->name('stock-transfer-book.certificates');
    Route::get('/stock-transfer-book/certificates/create', [StockTransferCertificateController::class, 'create'])->name('stock-transfer-book.certificates.create');
    Route::post('/stock-transfer-book/certificates', [StockTransferCertificateController::class, 'store'])->name('stock-transfer-book.certificates.store');
    Route::get('/stock-transfer-book/certificates/{stockTransferCertificate}/template', [StockTransferCertificateController::class, 'templatePreview'])->name('stock-transfer-book.certificates.template');
    Route::get('/stock-transfer-book/certificates/{stockTransferCertificate}', [StockTransferCertificateController::class, 'show'])->name('stock-transfer-book.certificates.show');
    Route::post('/stock-transfer-book/certificates/{stockTransferCertificate}/issue', [StockTransferCertificateController::class, 'issue'])->name('stock-transfer-book.certificates.issue');
    Route::get('/stock-transfer-book/certificates/{stockTransferCertificate}/edit', [StockTransferCertificateController::class, 'edit'])->name('stock-transfer-book.certificates.edit');
    Route::put('/stock-transfer-book/certificates/{stockTransferCertificate}', [StockTransferCertificateController::class, 'update'])->name('stock-transfer-book.certificates.update');
    Route::delete('/stock-transfer-book/certificates/{stockTransferCertificate}', [StockTransferCertificateController::class, 'destroy'])->name('stock-transfer-book.certificates.destroy');
    Route::post('/stock-transfer-book/certificates/requests', [StockTransferCertificateController::class, 'storeRequest'])->name('stock-transfer-book.certificates.requests.store');
    Route::get('/stock-transfer-book/certificates/requests/{stockTransferIssuanceRequest}', [StockTransferCertificateController::class, 'showRequest'])->name('stock-transfer-book.certificates.requests.show');
    Route::post('/stock-transfer-book/certificates/requests/{stockTransferIssuanceRequest}/approve', [StockTransferCertificateController::class, 'approveRequest'])->name('stock-transfer-book.certificates.requests.approve');
    Route::get('/stock-transfer-book/lookup', [StockTransferLookupController::class, 'lookup'])->name('stock-transfer-book.lookup');
    Route::get('/stock-transfer-book/defaults', [StockTransferLookupController::class, 'defaults'])->name('stock-transfer-book.defaults');
    Route::get('/corporate-document-defaults', CorporateDocumentDefaultsController::class)->name('corporate-document-defaults');

    /*
    |----------------------------------------------------------------------
    | Corporate Registers
    |----------------------------------------------------------------------
    */
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices');
    Route::get('/notices/create', [NoticeController::class, 'create'])->name('notices.create');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::get('/notices/{notice}', [NoticeController::class, 'show'])->name('notices.preview');
    Route::get('/notices/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
    Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');

    Route::get('/minutes', [MinuteController::class, 'index'])->name('minutes');
    Route::get('/minutes/create', [MinuteController::class, 'create'])->name('minutes.create');
    Route::post('/minutes', [MinuteController::class, 'store'])->name('minutes.store');
    Route::get('/minutes/{minute}', [MinuteController::class, 'show'])->name('minutes.preview');
    Route::get('/minutes/{minute}/edit', [MinuteController::class, 'edit'])->name('minutes.edit');
    Route::put('/minutes/{minute}', [MinuteController::class, 'update'])->name('minutes.update');
    Route::post('/minutes/{minute}/approve', [MinuteController::class, 'approve'])->name('minutes.approve');
    Route::post('/minutes/{minute}/workspace-save', [MinuteController::class, 'saveWorkspace'])->name('minutes.workspace-save');
    Route::post('/minutes/{minute}/final-audio', [MinuteController::class, 'saveFinalRecording'])->name('minutes.final-audio');
    Route::post('/minutes/{minute}/final-save', [MinuteController::class, 'saveFinalPreview'])->name('minutes.final-save');
    Route::delete('/minutes/{minute}', [MinuteController::class, 'destroy'])->name('minutes.destroy');

    Route::get('/resolutions', [ResolutionController::class, 'index'])->name('resolutions');
    Route::get('/resolutions/create', [ResolutionController::class, 'create'])->name('resolutions.create');
    Route::post('/resolutions', [ResolutionController::class, 'store'])->name('resolutions.store');
    Route::get('/resolutions/{resolution}', [ResolutionController::class, 'show'])->name('resolutions.preview');
    Route::get('/resolutions/{resolution}/edit', [ResolutionController::class, 'edit'])->name('resolutions.edit');
    Route::put('/resolutions/{resolution}', [ResolutionController::class, 'update'])->name('resolutions.update');
    Route::delete('/resolutions/{resolution}', [ResolutionController::class, 'destroy'])->name('resolutions.destroy');

    Route::get('/secretary-certificates', [SecretaryCertificateController::class, 'index'])->name('secretary-certificates');
    Route::get('/secretary-certificates/create', [SecretaryCertificateController::class, 'create'])->name('secretary-certificates.create');
    Route::post('/secretary-certificates', [SecretaryCertificateController::class, 'store'])->name('secretary-certificates.store');
    Route::get('/secretary-certificates/{secretaryCertificate}', [SecretaryCertificateController::class, 'show'])->name('secretary-certificates.preview');
    Route::get('/secretary-certificates/{secretaryCertificate}/edit', [SecretaryCertificateController::class, 'edit'])->name('secretary-certificates.edit');
    Route::match(['post', 'put'], '/secretary-certificates/{secretaryCertificate}/edit', [SecretaryCertificateController::class, 'update']);
    Route::put('/secretary-certificates/{secretaryCertificate}', [SecretaryCertificateController::class, 'update'])->name('secretary-certificates.update');
    Route::delete('/secretary-certificates/{secretaryCertificate}', [SecretaryCertificateController::class, 'destroy'])->name('secretary-certificates.destroy');

    Route::get('/bir-tax', [BirTaxController::class, 'index'])->name('bir-tax');
    Route::get('/bir-tax/create', [BirTaxController::class, 'create'])->name('bir-tax.create');
    Route::post('/bir-tax', [BirTaxController::class, 'store'])->name('bir-tax.store');
    Route::get('/bir-tax/{birTax}', [BirTaxController::class, 'show'])->name('bir-tax.preview');
    Route::get('/bir-tax/{birTax}/edit', [BirTaxController::class, 'edit'])->name('bir-tax.edit');
    Route::put('/bir-tax/{birTax}', [BirTaxController::class, 'update'])->name('bir-tax.update');
    Route::post('/bir-tax/{birTax}/authority-notes', [BirTaxController::class, 'storeAuthorityNote'])->name('bir-tax.notes.store');
    Route::delete('/bir-tax/{birTax}', [BirTaxController::class, 'destroy'])->name('bir-tax.destroy');

    Route::get('/natgov', [NatGovController::class, 'index'])->name('natgov');
    Route::get('/natgov/create', [NatGovController::class, 'create'])->name('natgov.create');
    Route::post('/natgov', [NatGovController::class, 'store'])->name('natgov.store');
    Route::get('/natgov/{natgov}', [NatGovController::class, 'show'])->name('natgov.preview');
    Route::get('/natgov/{natgov}/edit', [NatGovController::class, 'edit'])->name('natgov.edit');
    Route::put('/natgov/{natgov}', [NatGovController::class, 'update'])->name('natgov.update');
    Route::post('/natgov/{natgov}/authority-notes', [NatGovController::class, 'storeAuthorityNote'])->name('natgov.notes.store');
    Route::delete('/natgov/{natgov}', [NatGovController::class, 'destroy'])->name('natgov.destroy');
});
