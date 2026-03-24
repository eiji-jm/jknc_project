<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminUserPermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BirTaxController;
use App\Http\Controllers\BylawController;
use App\Http\Controllers\CapitalStructureController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CorporateDocumentDefaultsController;
use App\Http\Controllers\CorporateApprovalController;
use App\Http\Controllers\CorporateFormationController;
use App\Http\Controllers\DirectorOfficerController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\MinuteController;
use App\Http\Controllers\NatGovController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SecAoiController;
use App\Http\Controllers\SecretaryCertificateController;
use App\Http\Controllers\StockTransferCertificateController;
use App\Http\Controllers\StockTransferInstallmentController;
use App\Http\Controllers\StockTransferJournalController;
use App\Http\Controllers\StockTransferLedgerController;
use App\Http\Controllers\StockTransferLookupController;
use App\Http\Controllers\StockholderController;
use App\Http\Controllers\TownHallController;
use App\Http\Controllers\UltimateBeneficialOwnerController;
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
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions');
    Route::post('/admin/role-permissions/{id}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
    Route::get('/admin/user-permissions', [AdminUserPermissionController::class, 'index'])->name('admin.user-permissions');
    Route::get('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'edit'])->name('admin.user-permissions.edit');
    Route::post('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'update'])->name('admin.user-permissions.update');
    Route::get('/admin/corporate-dashboard', [CorporateApprovalController::class, 'dashboard'])->name('admin.corporate.dashboard');
    Route::post('/admin/corporate-approvals/{module}/{id}/approve', [CorporateApprovalController::class, 'approve'])->name('corporate.approvals.approve');
    Route::post('/admin/corporate-approvals/{module}/{id}/reject', [CorporateApprovalController::class, 'reject'])->name('corporate.approvals.reject');
    Route::post('/admin/corporate-approvals/{module}/{id}/revise', [CorporateApprovalController::class, 'revise'])->name('corporate.approvals.revise');
    Route::post('/admin/corporate-approvals/{module}/{id}/archive', [CorporateApprovalController::class, 'archive'])->name('corporate.approvals.archive');

    /*
    |----------------------------------------------------------------------
    | Town Hall
    |----------------------------------------------------------------------
    */
    Route::get('/townhall', [TownHallController::class, 'index'])->name('townhall');
    Route::post('/townhall', [TownHallController::class, 'store'])->name('townhall.store');
    Route::get('/townhall/{id}', [TownHallController::class, 'show'])->name('townhall.show');
    Route::post('/townhall/{id}/approve', [TownHallController::class, 'approve'])->name('townhall.approve');
    Route::post('/townhall/{id}/reject', [TownHallController::class, 'reject'])->name('townhall.reject');
    Route::post('/townhall/{id}/revise', [TownHallController::class, 'revise'])->name('townhall.revise');

    /*
    |----------------------------------------------------------------------
    | Corporate
    |----------------------------------------------------------------------
    */
    Route::get('/corporate', [GisController::class, 'companyInfo'])->name('corporate');
    Route::get('/corporate/company-general-information', [GisController::class, 'companyInfo'])->name('corporate.companyinfo');

    Route::get('/corporate/gis', [GisController::class, 'index'])->name('corporate.gis');
    Route::post('/corporate/gis/store', [GisController::class, 'store'])->name('gis.store');
    Route::get('/corporate/gis/{id}/show', [GisController::class, 'show'])->name('gis.show');
    Route::get('/corporate/gis/{id}/company-info', [GisController::class, 'companyInfoById'])->name('gis.company.info');
    Route::put('/gis/company-info/{id}', [GisController::class, 'updateCompanyInfo'])->name('gis.company.update');
    Route::get('/corporate/gis/capital-structure', [GisController::class, 'capitalStructure'])->name('gis.capital');
    Route::get('/corporate/gis/directors-officers', [GisController::class, 'directorsOfficers'])->name('gis.directors');
    Route::get('/corporate/gis/stockholders', [GisController::class, 'stockholders'])->name('gis.stockholders');
    Route::post('/gis/authorized/store', [CapitalStructureController::class, 'storeAuthorized'])->name('authorized.store');
    Route::post('/gis/subscribed/store', [CapitalStructureController::class, 'storeSubscribed'])->name('subscribed.store');
    Route::post('/gis/paidup/store', [CapitalStructureController::class, 'storePaidup'])->name('paidup.store');
    Route::post('/gis/director/store', [DirectorOfficerController::class, 'store'])->name('director.store');
    Route::post('/gis/stockholder/store', [StockholderController::class, 'store'])->name('stockholder.store');
    Route::post('/corporate/gis/{id}/upload-draft-file', [GisController::class, 'uploadDraftFile'])->name('corporate.gis.upload.draft');
    Route::post('/corporate/gis/{id}/upload-notary-file', [GisController::class, 'uploadNotaryFile'])->name('corporate.gis.upload.notary');

    Route::get('/corporate/formation', [CorporateFormationController::class, 'index'])->name('corporate.formation');
    Route::post('/corporate/formation/store', [CorporateFormationController::class, 'store'])->name('corporate.formation.store');
    Route::get('/corporate/formation/{id}', [CorporateFormationController::class, 'show'])->name('corporate.formation.show');
    Route::post('/corporate/formation/{id}/upload-draft-file', [CorporateFormationController::class, 'uploadDraftFile'])->name('corporate.formation.upload.draft');
    Route::post('/corporate/formation/{id}/upload-notary-file', [CorporateFormationController::class, 'uploadNotaryFile'])->name('corporate.formation.upload.notary');
    Route::post('/corporate/formation/{id}/submit', [CorporateFormationController::class, 'submit'])->name('corporate.formation.submit');
    Route::put('/corporate/formation/{id}/update', [CorporateFormationController::class, 'update'])->name('corporate.formation.update');

    Route::get('/corporate/sec-aoi', [SecAoiController::class, 'index'])->name('corporate.sec_aoi');
    Route::post('/corporate/sec-aoi/store', [SecAoiController::class, 'store'])->name('corporate.sec_aoi.store');
    Route::get('/corporate/sec-aoi/{id}', [SecAoiController::class, 'show'])->name('corporate.sec_aoi.show');
    Route::post('/corporate/sec-aoi/{id}/upload-draft-file', [SecAoiController::class, 'uploadDraftFile'])->name('corporate.sec_aoi.upload.draft');
    Route::post('/corporate/sec-aoi/{id}/upload-notary-file', [SecAoiController::class, 'uploadNotaryFile'])->name('corporate.sec_aoi.upload.notary');
    Route::post('/corporate/sec-aoi/{id}/submit', [SecAoiController::class, 'submit'])->name('corporate.sec_aoi.submit');

    Route::get('/corporate/bylaws', [BylawController::class, 'index'])->name('corporate.bylaws');
    Route::post('/corporate/bylaws/store', [BylawController::class, 'store'])->name('corporate.bylaws.store');
    Route::get('/corporate/bylaws/{id}', [BylawController::class, 'show'])->name('corporate.bylaws.show');
    Route::post('/corporate/bylaws/{id}/upload-draft-file', [BylawController::class, 'uploadDraftFile'])->name('corporate.bylaws.upload.draft');
    Route::post('/corporate/bylaws/{id}/upload-notary-file', [BylawController::class, 'uploadNotaryFile'])->name('corporate.bylaws.upload.notary');
    Route::post('/corporate/bylaws/{id}/submit', [BylawController::class, 'submit'])->name('corporate.bylaws.submit');

    /*
    |----------------------------------------------------------------------
    | UBO / Contacts / STB
    |----------------------------------------------------------------------
    */
    Route::get('/corporate/ubo', [UltimateBeneficialOwnerController::class, 'index'])->name('corporate.ubo');
    Route::get('/corporate/ubo/create', [UltimateBeneficialOwnerController::class, 'create'])->name('corporate.ubo.create');
    Route::post('/corporate/ubo', [UltimateBeneficialOwnerController::class, 'store'])->name('corporate.ubo.store');
    Route::get('/corporate/ubo/{ultimateBeneficialOwner}', [UltimateBeneficialOwnerController::class, 'show'])->name('corporate.ubo.show');
    Route::get('/corporate/ubo/{ultimateBeneficialOwner}/edit', [UltimateBeneficialOwnerController::class, 'edit'])->name('corporate.ubo.edit');
    Route::put('/corporate/ubo/{ultimateBeneficialOwner}', [UltimateBeneficialOwnerController::class, 'update'])->name('corporate.ubo.update');
    Route::delete('/corporate/ubo/{ultimateBeneficialOwner}', [UltimateBeneficialOwnerController::class, 'destroy'])->name('corporate.ubo.destroy');

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
