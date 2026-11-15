<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes — Big Live Marketplace
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if ($user && Hash::check($credentials['password'], $user->password)) {
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->with('error', 'The provided credentials do not match our records.');
});

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.register');
});

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
        'phone_e164' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    $user = User::create([
        'uuid' => (string) Str::uuid(),
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone_e164' => $validated['phone_e164'] ?? null,
        'password' => Hash::make($validated['password']),
        'status' => 'active',
        'locale' => 'en',
        'preferred_currency' => 'PKR',
    ]);

    try {
        $user->assignRole('buyer');
    } catch (\Throwable $e) {
        // Continue if role table not populated in custom env
    }

    Auth::login($user);
    $request->session()->regenerate();

    return redirect('/dashboard')->with('success', 'Account created successfully!');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

// Marketplace Post / Sell Ad Routes
Route::get('/sell', function () {
    return view('listings.create');
})->middleware('auth');

Route::post('/sell', function (Request $request) {
    $validated = $request->validate([
        'title' => ['required', 'string', 'min:5', 'max:180'],
        'category_id' => ['required', 'integer'],
        'city_id' => ['required', 'integer'],
        'price' => ['required', 'numeric', 'min:1'],
        'condition_code' => ['required', 'string', 'in:new,used,refurbished'],
        'location_label' => ['nullable', 'string', 'max:255'],
        'description' => ['required', 'string', 'min:20', 'max:5000'],
    ]);

    $listing = Listing::create([
        'seller_id' => Auth::id(),
        'category_id' => $validated['category_id'],
        'city_id' => $validated['city_id'],
        'title' => $validated['title'],
        'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(6)),
        'description' => $validated['description'],
        'price_minor' => (int) round($validated['price'] * 100),
        'currency' => 'PKR',
        'condition_code' => $validated['condition_code'],
        'location_label' => $validated['location_label'] ?? 'Pakistan',
        'status' => 'active',
        'published_at' => now(),
    ]);

    return redirect('/listings/' . $listing->id)->with('success', 'Your ad has been published successfully!');
})->middleware('auth');

// Listing Detail
Route::get('/listings/{listing}', function ($id) {
    $listing = Listing::with(['seller', 'category', 'city'])->findOrFail($id);
    return view('listings.show', compact('listing'));
});

// Search & Catalog
Route::get('/search', function (Request $request) {
    $query = Listing::where('status', 'active')->with(['category', 'city']);

    if ($request->filled('q')) {
        $q = $request->input('q');
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        });
    }

    if ($request->filled('city')) {
        $cityName = $request->input('city');
        $city = City::where('slug', $cityName)->orWhere('name', 'like', "%{$cityName}%")->first();
        if ($city) {
            $query->where('city_id', $city->id);
        }
    }

    $listings = $query->latest('id')->get();
    $title = $request->filled('q') ? "Search: {$request->input('q')}" : "All Classifieds";

    return view('search', compact('listings', 'title'));
});

// Category Route
Route::get('/category/{slug}', function ($slug) {
    $category = Category::where('slug', $slug)->first();
    $title = $category ? $category->name : ucfirst(str_replace('-', ' ', $slug));

    $query = Listing::where('status', 'active')->with(['category', 'city']);
    if ($category) {
        $query->where('category_id', $category->id);
    } else {
        $query->where('title', 'like', "%{$slug}%");
    }

    $listings = $query->latest('id')->get();

    return view('search', compact('listings', 'title'));
});

// Information / CMS Pages
Route::get('/terms', function () {
    return view('pages.content', [
        'title' => 'Terms of Service',
        'content' => '<p>Welcome to Big Live Marketplace. By accessing or using our platform, mobile applications, and services, you agree to comply with and be bound by these Terms of Service.</p><h3 class="text-lg font-bold text-navy mt-4">1. Use of Services</h3><p>You must provide accurate, complete information when creating an account or posting classified listings. You agree not to post fraudulent, deceptive, or prohibited items.</p><h3 class="text-lg font-bold text-navy mt-4">2. Trading & Safety</h3><p>Big Live Marketplace facilitates communication between buyers and sellers. We encourage users to inspect items in person in safe public locations before completing transactions.</p>',
    ]);
});

Route::get('/privacy', function () {
    return view('pages.content', [
        'title' => 'Privacy Policy',
        'content' => '<p>Your privacy is important to us. This Privacy Policy explains how Big Live Marketplace collects, stores, protects, and handles your personal data.</p><h3 class="text-lg font-bold text-navy mt-4">1. Information We Collect</h3><p>We collect information you provide directly, including account details, phone number, listings, messages, and transactional data.</p><h3 class="text-lg font-bold text-navy mt-4">2. Data Security</h3><p>We implement industry-standard encryption, tokenization, and strict access controls to protect your information against unauthorized access.</p>',
    ]);
});

Route::get('/about', function () {
    return view('pages.content', [
        'title' => 'About Big Live Marketplace',
        'content' => '<p>Big Live Marketplace is Pakistan’s leading digital marketplace connecting millions of buyers and sellers across the country.</p><p class="mt-4">Our mission is to provide an accessible, transparent, high-performance platform for buying and selling vehicles, real estate, electronics, mobile phones, services, and everyday goods.</p>',
    ]);
});

Route::get('/help', function () {
    return view('pages.content', [
        'title' => 'Help & Customer Support',
        'content' => '<p>Need help with your Big Live Marketplace account, ad posting, or order? Our support team is available 24/7 to assist you.</p><div class="mt-4 bg-slate-50 p-4 rounded-xl border border-slate-200"><strong>Contact Support:</strong><br>Email: support@biglive.pk<br>Helpline: +92 (51) 111-BIG-LIVE<br>Working Hours: 24/7 Live Support</div>',
    ]);
});

Route::get('/safety', function () {
    return view('pages.content', [
        'title' => 'Safety & Fraud Prevention Tips',
        'content' => '<p>Stay secure while buying and selling on Big Live Marketplace by following these essential guidelines:</p><ul class="list-disc pl-5 mt-2 space-y-2"><li><strong>Meet in Public:</strong> Always meet buyers or sellers in well-lit, public places such as shopping malls or public markets.</li><li><strong>Inspect Before Paying:</strong> Never transfer funds in advance without physically verifying the product and its documents.</li><li><strong>Use Built-in Chat:</strong> Communicate through the official Big Live in-app chat to maintain an audit trail.</li></ul>',
    ]);
});
