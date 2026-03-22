<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BylawController;
use App\Http\Controllers\CapitalStructureController;
use App\Http\Controllers\CompanyActivityController;
use App\Http\Controllers\CompanyConsultationNoteController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyCorporateFormationController;
use App\Http\Controllers\CompanyDealController;
use App\Http\Controllers\CompanyAccountingController;
use App\Http\Controllers\CompanyBankingController;
use App\Http\Controllers\CompanyBirTaxController;
use App\Http\Controllers\CompanyBifController;
use App\Http\Controllers\CompanyCorrespondenceController;
use App\Http\Controllers\CompanyKycController;
use App\Http\Controllers\CompanyLguController;
use App\Http\Controllers\CompanyOperationsController;
use App\Http\Controllers\CompanyProductController;
use App\Http\Controllers\CompanyServiceController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CorporateFormationController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DirectorOfficerController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SecAoiController;
use App\Http\Controllers\StockholderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/bif/respond/{token}', [CompanyBifController::class, 'clientForm'])->name('company.bif.client.show');
Route::post('/bif/respond/{token}', [CompanyBifController::class, 'submitClientForm'])->name('company.bif.client.submit');

Route::get('/activities', function () {
    return view('activities.index');
})->name('activities');

Route::prefix('api')->group(function () {
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::post('/tasks', [ActivityController::class, 'storeTask']);
    Route::post('/events', [ActivityController::class, 'storeEvent']);
    Route::post('/calls', [ActivityController::class, 'storeCall']);
    Route::post('/meetings', [ActivityController::class, 'storeMeeting']);

    Route::put('/tasks/{id}', [ActivityController::class, 'updateTask']);
    Route::put('/events/{id}', [ActivityController::class, 'updateEvent']);
    Route::put('/calls/{id}', [ActivityController::class, 'updateCall']);
    Route::put('/meetings/{id}', [ActivityController::class, 'updateMeeting']);

    Route::delete('/tasks/{id}', [ActivityController::class, 'destroyTask']);
    Route::delete('/events/{id}', [ActivityController::class, 'destroyEvent']);
    Route::delete('/calls/{id}', [ActivityController::class, 'destroyCall']);
    Route::delete('/meetings/{id}', [ActivityController::class, 'destroyMeeting']);

    Route::post('/notes', [ActivityController::class, 'storeNote']);
    Route::put('/notes/{id}', [ActivityController::class, 'updateNote']);
    Route::delete('/notes/{id}', [ActivityController::class, 'destroyNote']);
    Route::post('/meetings/{id}/analyze', [ActivityController::class, 'analyzeMeeting']);
    Route::post('/meetings/{id}/upload-video', [ActivityController::class, 'uploadVideo']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'submit'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactsController::class, 'store'])->name('contacts.store');
Route::post('/contacts/assign-owner', [ContactsController::class, 'assignOwner'])->name('contacts.assign-owner');
Route::post('/contacts/custom-fields', [ContactsController::class, 'storeCustomField'])->name('contacts.custom-fields.store');
Route::post('/contacts/{contact}/cif', [ContactsController::class, 'saveCif'])->name('contacts.cif.save');
Route::post('/contacts/{contact}/cif/documents', [ContactsController::class, 'uploadCifDocument'])->name('contacts.cif.documents.upload');
Route::post('/contacts/{contact}/kyc/requirements/upload', [ContactsController::class, 'uploadKycRequirementDocument'])->name('contacts.kyc.requirements.upload');
Route::delete('/contacts/{contact}/kyc/requirements/{requirement}', [ContactsController::class, 'removeKycRequirementDocument'])->name('contacts.kyc.requirements.remove');
Route::post('/contacts/{contact}/kyc/submit', [ContactsController::class, 'submitKycForVerification'])->name('contacts.kyc.submit');
Route::get('/contacts/{contact}/cif/preview', [ContactsController::class, 'previewCif'])->name('contacts.cif.preview');
Route::get('/contacts/{contact}/cif/download', [ContactsController::class, 'downloadCif'])->name('contacts.cif.download');
Route::get('/contacts/{id}/kyc/specimen-signature', [ContactsController::class, 'specimenSignature'])->name('contacts.specimen-signature');
Route::post('/contacts/{id}/kyc/specimen-signature', [ContactsController::class, 'saveSpecimenSignature'])->name('contacts.specimen-signature.save');
Route::get('/contacts/{id}/kyc/specimen-signature/download', [ContactsController::class, 'downloadSpecimenSignature'])->name('contacts.specimen-signature.download');
Route::get('/contacts/{contact}', [ContactsController::class, 'show'])->name('contacts.show');

Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
Route::post('/deals/preview', [DealController::class, 'preview'])->name('deals.preview');
Route::get('/deals/preview', [DealController::class, 'previewPage'])->name('deals.preview.show');
Route::post('/deals/draft', [DealController::class, 'saveDraft'])->name('deals.draft');
Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
Route::put('/deals/{id}', [DealController::class, 'update'])->name('deals.update');
Route::get('/deals/{id}/download', [DealController::class, 'download'])->name('deals.download');
Route::get('/deals/{id}', [DealController::class, 'show'])->name('deals.show');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::post('/products/change-owner', [ProductController::class, 'changeOwner'])->name('products.change-owner');
    Route::post('/products/custom-fields', [ProductController::class, 'storeCustomField'])->name('products.custom-fields.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/services', [CompanyServiceController::class, 'globalIndex'])->name('services.index');
    Route::post('/services', [CompanyServiceController::class, 'storeGlobal'])->name('services.store');
    Route::post('/services/custom-fields', [CompanyServiceController::class, 'storeCustomField'])->name('services.custom-fields.store');
    Route::get('/services/{service}', [CompanyServiceController::class, 'showGlobal'])->name('services.show');
    Route::match(['put', 'patch'], '/services/{service}', [CompanyServiceController::class, 'updateGlobal'])->name('services.update');
    Route::delete('/services/{service}', [CompanyServiceController::class, 'destroyGlobal'])->name('services.destroy');

    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/company/custom-fields', [CompanyController::class, 'storeCustomField'])->name('company.custom-fields.store');
    Route::match(['put', 'patch'], '/company/{company}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');
    Route::get('/company/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/company/{company}/kyc', [CompanyKycController::class, 'index'])->name('company.kyc');
    Route::get('/company/{company}/history', [CompanyController::class, 'history'])->name('company.history');
    Route::get('/company/{company}/consultation-notes', [CompanyController::class, 'consultationNotes'])->name('company.consultation-notes');
    Route::post('/company/{company}/consultation-notes', [CompanyConsultationNoteController::class, 'store'])->name('company.consultation-notes.store');
    Route::match(['put', 'patch'], '/company/{company}/consultation-notes/{note}', [CompanyConsultationNoteController::class, 'update'])->name('company.consultation-notes.update');
    Route::delete('/company/{company}/consultation-notes/{note}', [CompanyConsultationNoteController::class, 'destroy'])->name('company.consultation-notes.destroy');
    Route::get('/company/{company}/activities', [CompanyController::class, 'activities'])->name('company.activities');
    Route::post('/company/{company}/activities', [CompanyActivityController::class, 'store'])->name('company.activities.store');
    Route::match(['put', 'patch'], '/company/{company}/activities/{activity}', [CompanyActivityController::class, 'update'])->name('company.activities.update');
    Route::patch('/company/{company}/activities/{activity}/complete', [CompanyActivityController::class, 'complete'])->name('company.activities.complete');
    Route::delete('/company/{company}/activities/{activity}', [CompanyActivityController::class, 'destroy'])->name('company.activities.destroy');
    Route::get('/company/{company}/deals', [CompanyDealController::class, 'index'])->name('company.deals');
    Route::post('/company/{company}/deals', [CompanyDealController::class, 'store'])->name('company.deals.store');
    Route::get('/company/{company}/deals/{deal}', [CompanyDealController::class, 'show'])->name('company.deals.show');
    Route::match(['put', 'patch'], '/company/{company}/deals/{deal}', [CompanyDealController::class, 'update'])->name('company.deals.update');
    Route::delete('/company/{company}/deals/{deal}', [CompanyDealController::class, 'destroy'])->name('company.deals.destroy');
    Route::get('/company/{company}/contacts', [CompanyController::class, 'contacts'])->name('company.contacts');
    Route::post('/company/{company}/contacts', [CompanyController::class, 'storeContact'])->name('company.contacts.store');
    Route::post('/company/{company}/contacts/custom-fields', [CompanyController::class, 'storeContactCustomField'])->name('company.contacts.custom-fields.store');
    Route::match(['put', 'patch'], '/company/{company}/contacts/{contact}', [CompanyController::class, 'updateContact'])->name('company.contacts.update');
    Route::delete('/company/{company}/contacts/{contact}', [CompanyController::class, 'destroyContact'])->name('company.contacts.destroy');
    Route::get('/company/{company}/projects', [CompanyController::class, 'projects'])->name('company.projects');
    Route::get('/company/{company}/regular', [CompanyController::class, 'regular'])->name('company.regular');
    Route::get('/company/{company}/products', [CompanyProductController::class, 'index'])->name('company.products');
    Route::post('/company/{company}/products/link', [CompanyProductController::class, 'link'])->name('company.products.link');
    Route::post('/company/{company}/products', [CompanyProductController::class, 'store'])->name('company.products.store');
    Route::get('/company/{company}/products/{product}', [CompanyProductController::class, 'show'])->name('company.products.show');
    Route::match(['put', 'patch'], '/company/{company}/products/{product}', [CompanyProductController::class, 'update'])->name('company.products.update');
    Route::delete('/company/{company}/products/{product}', [CompanyProductController::class, 'unlink'])->name('company.products.unlink');
    Route::get('/company/{company}/lgu', [CompanyLguController::class, 'index'])->name('company.lgu');
    Route::post('/company/{company}/lgu', [CompanyLguController::class, 'store'])->name('company.lgu.store');
    Route::match(['put', 'patch'], '/company/{company}/lgu/{record}', [CompanyLguController::class, 'update'])->name('company.lgu.update');
    Route::delete('/company/{company}/lgu/{record}', [CompanyLguController::class, 'destroy'])->name('company.lgu.destroy');
    Route::get('/company/{company}/accounting', [CompanyAccountingController::class, 'index'])->name('company.accounting');
    Route::post('/company/{company}/accounting', [CompanyAccountingController::class, 'store'])->name('company.accounting.store');
    Route::match(['put', 'patch'], '/company/{company}/accounting/{record}', [CompanyAccountingController::class, 'update'])->name('company.accounting.update');
    Route::delete('/company/{company}/accounting/{record}', [CompanyAccountingController::class, 'destroy'])->name('company.accounting.destroy');
    Route::get('/company/{company}/banking', [CompanyBankingController::class, 'index'])->name('company.banking');
    Route::post('/company/{company}/banking', [CompanyBankingController::class, 'store'])->name('company.banking.store');
    Route::match(['put', 'patch'], '/company/{company}/banking/{record}', [CompanyBankingController::class, 'update'])->name('company.banking.update');
    Route::delete('/company/{company}/banking/{record}', [CompanyBankingController::class, 'destroy'])->name('company.banking.destroy');
    Route::get('/company/{company}/operations', [CompanyOperationsController::class, 'index'])->name('company.operations');
    Route::post('/company/{company}/operations', [CompanyOperationsController::class, 'store'])->name('company.operations.store');
    Route::match(['put', 'patch'], '/company/{company}/operations/{record}', [CompanyOperationsController::class, 'update'])->name('company.operations.update');
    Route::delete('/company/{company}/operations/{record}', [CompanyOperationsController::class, 'destroy'])->name('company.operations.destroy');
    Route::get('/company/{company}/correspondence', [CompanyCorrespondenceController::class, 'index'])->name('company.correspondence');
    Route::post('/company/{company}/correspondence', [CompanyCorrespondenceController::class, 'store'])->name('company.correspondence.store');
    Route::match(['put', 'patch'], '/company/{company}/correspondence/{record}', [CompanyCorrespondenceController::class, 'update'])->name('company.correspondence.update');
    Route::delete('/company/{company}/correspondence/{record}', [CompanyCorrespondenceController::class, 'destroy'])->name('company.correspondence.destroy');
    Route::get('/company/{company}/bir-tax', [CompanyBirTaxController::class, 'index'])->name('company.bir-tax');
    Route::post('/company/{company}/bir-tax', [CompanyBirTaxController::class, 'store'])->name('company.bir-tax.store');
    Route::match(['put', 'patch'], '/company/{company}/bir-tax/{record}', [CompanyBirTaxController::class, 'update'])->name('company.bir-tax.update');
    Route::delete('/company/{company}/bir-tax/{record}', [CompanyBirTaxController::class, 'destroy'])->name('company.bir-tax.destroy');
    Route::get('/company/{company}/corporate-formation', [CompanyCorporateFormationController::class, 'index'])->name('company.corporate-formation');
    Route::get('/company/{company}/corporate-formation/sec-coi', [CompanyCorporateFormationController::class, 'secCoi'])->name('company.corporate-formation.sec-coi');
    Route::post('/company/{company}/corporate-formation/sec-coi', [CompanyCorporateFormationController::class, 'storeSecCoi'])->name('company.corporate-formation.sec-coi.store');
    Route::match(['put', 'patch'], '/company/{company}/corporate-formation/sec-coi/{record}', [CompanyCorporateFormationController::class, 'updateSecCoi'])->name('company.corporate-formation.sec-coi.update');
    Route::get('/company/{company}/corporate-formation/sec-aoi', [CompanyCorporateFormationController::class, 'secAoi'])->name('company.corporate-formation.sec-aoi');
    Route::post('/company/{company}/corporate-formation/sec-aoi', [CompanyCorporateFormationController::class, 'storeSecAoi'])->name('company.corporate-formation.sec-aoi.store');
    Route::match(['put', 'patch'], '/company/{company}/corporate-formation/sec-aoi/{record}', [CompanyCorporateFormationController::class, 'updateSecAoi'])->name('company.corporate-formation.sec-aoi.update');
    Route::get('/company/{company}/corporate-formation/bylaws', [CompanyCorporateFormationController::class, 'bylaws'])->name('company.corporate-formation.bylaws');
    Route::post('/company/{company}/corporate-formation/bylaws', [CompanyCorporateFormationController::class, 'storeBylaw'])->name('company.corporate-formation.bylaws.store');
    Route::match(['put', 'patch'], '/company/{company}/corporate-formation/bylaws/{record}', [CompanyCorporateFormationController::class, 'updateBylaw'])->name('company.corporate-formation.bylaws.update');
    Route::get('/company/{company}/corporate-formation/gis', [CompanyCorporateFormationController::class, 'gis'])->name('company.corporate-formation.gis');
    Route::post('/company/{company}/corporate-formation/gis', [CompanyCorporateFormationController::class, 'storeGis'])->name('company.corporate-formation.gis.store');
    Route::match(['put', 'patch'], '/company/{company}/corporate-formation/gis/{record}', [CompanyCorporateFormationController::class, 'updateGis'])->name('company.corporate-formation.gis.update');
    Route::get('/company/{company}/kyc/bif/create', [CompanyBifController::class, 'create'])->name('company.bif.create');
    Route::post('/company/{company}/kyc/bif', [CompanyBifController::class, 'store'])->name('company.bif.store');
    Route::post('/company/{company}/kyc/bif/send', [CompanyBifController::class, 'sendClientForm'])->name('company.bif.send');
    Route::get('/company/{company}/kyc/bif/{bif}', [CompanyBifController::class, 'show'])->name('company.bif.show');
    Route::get('/company/{company}/kyc/bif/{bif}/edit', [CompanyBifController::class, 'edit'])->name('company.bif.edit');
    Route::match(['put', 'patch'], '/company/{company}/kyc/bif/{bif}', [CompanyBifController::class, 'update'])->name('company.bif.update');
    Route::get('/company/{company}/kyc/bif/{bif}/print', [CompanyBifController::class, 'print'])->name('company.bif.print');
    Route::get('/company/{company}/services', [CompanyServiceController::class, 'companyIndex'])->name('company.services.index');
    Route::post('/company/{company}/services', [CompanyServiceController::class, 'storeForCompany'])->name('company.services.store');
    Route::get('/company/{company}/services/{service}', [CompanyServiceController::class, 'showForCompany'])->name('company.services.show');
    Route::match(['put', 'patch'], '/company/{company}/services/{service}', [CompanyServiceController::class, 'updateForCompany'])->name('company.services.update');
    Route::delete('/company/{company}/services/{service}', [CompanyServiceController::class, 'destroyForCompany'])->name('company.services.destroy');

    Route::get('/corporate', [GisController::class, 'companyInfo'])->name('corporate');
    Route::get('/corporate/company-general-information', [GisController::class, 'companyInfo'])->name('corporate.companyinfo');
    Route::get('/corporate/gis', [GisController::class, 'index'])->name('corporate.gis');
    Route::post('/corporate/gis/store', [GisController::class, 'store'])->name('gis.store');
    Route::get('/corporate/gis/capital-structure', [GisController::class, 'capitalStructure'])->name('gis.capital');
    Route::get('/corporate/gis/directors-officers', [GisController::class, 'directorsOfficers'])->name('gis.directors');
    Route::get('/corporate/gis/stockholders', [GisController::class, 'stockholders'])->name('gis.stockholders');

    Route::get('/corporate/formation', [CorporateFormationController::class, 'index'])->name('corporate.formation');
    Route::post('/corporate/formation/store', [CorporateFormationController::class, 'store'])->name('corporate.formation.store');
    Route::get('/corporate/formation/{id}', [CorporateFormationController::class, 'show'])->name('corporate.formation.show');

    Route::get('/corporate/sec-aoi', [SecAoiController::class, 'index'])->name('corporate.sec_aoi');
    Route::post('/corporate/sec-aoi/store', [SecAoiController::class, 'store'])->name('corporate.sec_aoi.store');
    Route::get('/corporate/sec-aoi/{id}', [SecAoiController::class, 'show'])->name('corporate.sec_aoi.show');

    Route::get('/corporate/bylaws', [BylawController::class, 'index'])->name('corporate.bylaws');
    Route::post('/corporate/bylaws/store', [BylawController::class, 'store'])->name('corporate.bylaws.store');
    Route::get('/corporate/bylaws/{id}', [BylawController::class, 'show'])->name('corporate.bylaws.show');
    Route::view('/corporate/lgu', 'corporate.lgu')->name('corporate.lgu');
    Route::view('/corporate/accounting', 'corporate.accounting')->name('corporate.accounting');
    Route::view('/corporate/banking', 'corporate.banking')->name('corporate.banking');
    Route::view('/corporate/operations', 'corporate.operations')->name('corporate.operations');
    Route::view('/corporate/correspondence', 'corporate.correspondence')->name('corporate.correspondence');
    Route::view('/corporate/ubo', 'corporate.ubo-form')->name('corporate.ubo');
    Route::view('/stock-transfer-book', 'corporate.stock-transfer-book.index')->name('stock-transfer-book');
    Route::view('/stock-transfer-book/index', 'corporate.stock-transfer-book.stb-index')->name('stock-transfer-book.index');
    Route::view('/stock-transfer-book/journal', 'corporate.stock-transfer-book.journal')->name('stock-transfer-book.journal');
    Route::view('/stock-transfer-book/ledger', 'corporate.stock-transfer-book.ledger')->name('stock-transfer-book.ledger');
    Route::view('/stock-transfer-book/installment', 'corporate.stock-transfer-book.installment')->name('stock-transfer-book.installment');
    Route::view('/stock-transfer-book/certificates', 'corporate.stock-transfer-book.certificates')->name('stock-transfer-book.certificates');
    Route::view('/notices', 'corporate.notices.index')->name('notices');
    Route::view('/notices/preview', 'corporate.notices.preview')->name('notices.preview');
    Route::view('/minutes', 'corporate.minutes.index')->name('minutes');
    Route::view('/minutes/preview', 'corporate.minutes.preview')->name('minutes.preview');
    Route::view('/resolutions', 'corporate.resolutions.index')->name('resolutions');
    Route::view('/resolutions/preview', 'corporate.resolutions.preview')->name('resolutions.preview');
    Route::view('/secretary-certificates', 'corporate.secretary-certificates.index')->name('secretary-certificates');
    Route::view('/secretary-certificates/preview', 'corporate.secretary-certificates.preview')->name('secretary-certificates.preview');
    Route::view('/bir-tax', 'corporate.bir-tax.index')->name('bir-tax');
    Route::view('/bir-tax/preview', 'corporate.bir-tax.preview')->name('bir-tax.preview');
    Route::view('/natgov', 'corporate.natgov.index')->name('natgov');
    Route::view('/natgov/preview', 'corporate.natgov.preview')->name('natgov.preview');

    Route::get('/corporate/gis/{id}/show', [GisController::class, 'show'])->name('gis.show');
    Route::post('/gis/authorized/store', [CapitalStructureController::class, 'storeAuthorized'])->name('authorized.store');
    Route::post('/gis/subscribed/store', [CapitalStructureController::class, 'storeSubscribed'])->name('subscribed.store');
    Route::post('/gis/paidup/store', [CapitalStructureController::class, 'storePaidup'])->name('paidup.store');
    Route::post('/gis/director/store', [DirectorOfficerController::class, 'store'])->name('director.store');
    Route::post('/gis/stockholder/store', [StockholderController::class, 'store'])->name('stockholder.store');
});
