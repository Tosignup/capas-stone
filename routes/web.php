<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\adminPanel\AdminController;
use App\Http\Controllers\clientPanel\ClientController;
use App\Http\Controllers\patientPanel\PatientController;
use App\Http\Controllers\patientPanel\PaymentController;
use App\Http\Controllers\receptionistPanel\ReceptionistController;


Route::get('/', function () {return view('welcome');})->name('welcome');

Route::get('/dashboard', [ClientController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/appointment/request', [AppointmentController::class, 'create'])->name('appointments.request');
Route::post('/appointment/store', [AppointmentController::class, 'store'])->name('appointments.store');
Route::post('/appointments/{id}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
Route::post('/appointments/{id}/decline', [AppointmentController::class, 'decline'])->name('appointments.decline');


// Admin Routes
Route::group(['middleware' => ['auth','verified', 'role:admin']], function () {
    //Navbar
    Route::get('/admin/dashboard', [AdminController::class, 'overview'])->name('admin.dashboard');
    Route::get('/admin/patient-list', [AdminController::class, 'patient_list'])->name('patient_list');
    Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/admin/appointment-submission', [AppointmentController::class, 'appointment_submission'])->name('appointment.submission');

    // Patients
    Route::get('/admin/add-patient', [PatientController::class, 'addPatient'])->name('add.patient');
    Route::post('/patients', [PatientController::class, 'storePatient'])->name('store.patient');
    Route::get('/admin/edit-patient/{patient}', [PatientController::class, 'editPatient'])->name('edit.patient');
    Route::put('/patients/{patient}', [PatientController::class, 'updatePatient'])->name('update.patient');
    Route::get('/admin/show-patient/{patient}', [PatientController::class, 'showPatient'])->name('show.patient');

    // Payment
    Route::get('/patient/payment-page/{patient}', [PaymentController::class, 'addPayment'])->name('add.payment');
    Route::post('/patient/payment/{patient}', [PaymentController::class, 'testPayment'])->name('store.payment');

    Route::get('/patient/edit-payment/{patient}/{payment}', [PaymentController::class, 'editPayment'])->name('edit.payment');
    Route::put('/patient/update-payment/{patient}/{payment}', [PaymentController::class, 'testUpPayment'])->name('update.payment');
    Route::get('/patient/{patient}/payment-history/{payment}', [PaymentController::class, 'showPaymentHistory'])->name('history.payment');

});

// Receptionist Routes
Route::group(['middleware' =>['auth', 'role:receptionist']], function () {
    Route::get('/receptionist/dashboard', [AdminController::class, 'overview'])->name('receptionist.dashboard');

});
//Client Routes
Route::group(['middleware' =>['auth','verified', 'role:client']], function () {
    // Route::get('/client/dashboard', [ClientController::class, 'profilePage'])->name('client.dashboard');
    Route::get('/client/dashboard/overview/{patient}', [ClientController::class, 'profileOverview'])->name('client.overview');
    Route::get('/client/dashboard/user-profile', [ClientController::class, 'profileUserProfile'])->name('client.user-profile');
});
