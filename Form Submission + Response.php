Web Route

// routes/web.php
use App\Http\Controllers\ContactController;

Route::get('/contact', [ContactController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/confirmation', [ContactController::class, 'confirmation'])->name('contact.confirmation');

Controller
php artisan make:controller ContactController
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        // Validation
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Save to database
        $contact = Contact::create($data);

        // Log the user input
        Log::info('Contact Form Submission:', $data);

        // Flash success message
        session()->flash('success', 'Your message has been sent successfully!');

        // Redirect to confirmation page with data
        return redirect()->route('contact.confirmation')->with('contact', $data);
    }

    public function confirmation()
    {
        $contact = session('contact');
        return view('pages.contact_confirmation', compact('contact'));
    }
}

Contact Form Blade
<!-- resources/views/pages/contact.blade.php -->
@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container">
    <h2>Contact Us</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('contact.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control">{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
@endsection

Confirmation Page Blade
<!-- resources/views/pages/contact_confirmation.blade.php -->
@extends('layouts.app')

@section('title', 'Confirmation')

@section('content')
<div class="container">
    <h2>Thank you, {{ $contact['name'] }}</h2>
    <p>We have received your message:</p>
    <p>{{ $contact['message'] }}</p>
    <p>We will contact you at: {{ $contact['email'] }}</p>
</div>
@endsection






