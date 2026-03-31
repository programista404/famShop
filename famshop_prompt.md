# FamShop Assistant — Master Project Prompt



## Project Identity

- **App name:** FamShop Assistant
-  ** website language ** : English
- **Purpose:** Smart family shopping app — combines allergen detection, halal verification, budget tracking, and personalized family member profiles in one accessible platform.
- **Delivery:** Mobile app built as a **WebView** — the backend is Laravel MVC + MySQL, the frontend is Blade/HTML/CSS/JS loaded inside a mobile WebView shell.
- **Design:** Already completed in Figma — assets are exported into `/design/` folder inside the Laravel project root. Do not modify files in `/design/`.
- **Database name:** `famshop_db`
- **Language:** Arabic (RTL) is the primary UI language. All user-facing text should support Arabic. Variable names and code stay in English.

---

## Technology Stack

| Layer | Technology                                      |
|---|-------------------------------------------------|
| Backend | Laravel  (PHP )                                 |
| Database | MySQL                                           |
| Frontend | Blade templates + HTML BOOSTRAP5,icons, JS + CSS |
| Mobile Shell | WebView (Android / iOS)                         |
| Authentication | Laravel (Blade)                           |
| Barcode Scanning | QuaggaJS (CDN)                                  |
| Product Data | Open Food Facts API (free, no key needed)       |
| File Storage | Laravel storage (public disk)                   |
| Voice Readout | Web Speech API (browser native)                 |

---

## Laravel Folder Structure

Follow this structure exactly. Do not create files outside these locations:

```
famshop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── FamilyMemberController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ScanController.php
│   │   │   ├── BudgetController.php
│   │   │   ├── CartController.php
│   │   │   ├── ShoppingListController.php
│   │   │   ├── FeedbackController.php
│   │   │   └── SupportController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── FamilyMember.php
│   │   ├── AllergyProfile.php
│   │   ├── Budget.php
│   │   ├── Product.php
│   │   ├── Ingredient.php
│   │   ├── ScanHistory.php
│   │   ├── ShoppingCart.php
│   │   ├── CartItem.php
│   │   ├── ShoppingList.php
│   │   ├── Feedback.php
│   │   └── SupportTicket.php
│   └── Services/
│       └── AllergyChecker.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── auth/
│       ├── dashboard/
│       ├── family/
│       ├── scan/
│       ├── budget/
│       ├── cart/
│       ├── list/
│       └── support/
├── routes/
│   └── web.php
├── public/
│   └── assets/
│       ├── css/
│       ├── js/
│       └── icons/
└── design/          ← Figma exports — READ ONLY, never modify
```

---

## Database Schema

Database name: `famshop
All tables use `ENGINE=InnoDB`, `utf8mb4_unicode_ci`, and Laravel timestamps (`created_at`, `updated_at`).

### users
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| name | VARCHAR(100) NOT NULL | |
| email | VARCHAR(100) UNIQUE NOT NULL | |
| password | VARCHAR(255) NOT NULL | Bcrypt hashed |
| gender | VARCHAR(20) NULL | |
| age | TINYINT UNSIGNED NULL | |
| profile_photo | VARCHAR(255) NULL | Path in storage/app/public |
| remember_token | VARCHAR(100) NULL | |
| created_at / updated_at | TIMESTAMP | Laravel auto |

### family_members
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| user_id | BIGINT UNSIGNED FK→users | CASCADE DELETE |
| name_member | VARCHAR(100) NOT NULL | |
| age | TINYINT UNSIGNED NULL | |
| gender | VARCHAR(20) NULL | |
| avatar | VARCHAR(255) NULL | |
| created_at / updated_at | TIMESTAMP | |

### allergy_profiles
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| member_id | BIGINT UNSIGNED FK→family_members | CASCADE DELETE |
| allergy_type | VARCHAR(100) NOT NULL | gluten / lactose / nuts / pork / egg / soy / sesame / shellfish / halal |
| severity_level | ENUM('mild','moderate','severe') | Default: moderate |
| created_at / updated_at | TIMESTAMP | |

### budgets
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| member_id | BIGINT UNSIGNED FK→family_members UNIQUE | One budget per member |
| daily_budget | DECIMAL(10,2) | |
| weekly_budget | DECIMAL(10,2) | |
| monthly_budget | DECIMAL(10,2) | |
| daily_spent | DECIMAL(10,2) | |
| weekly_spent | DECIMAL(10,2) | |
| monthly_spent | DECIMAL(10,2) | |
| created_at / updated_at | TIMESTAMP | |

> ⚠️ `remaining_amount` is NEVER stored in the DB. Always calculate in PHP: `$budget->daily_budget - $budget->daily_spent`

### products
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| barcode | VARCHAR(50) UNIQUE NOT NULL | |
| pr_name | VARCHAR(255) NOT NULL | |
| brand | VARCHAR(100) NULL | |
| price | DECIMAL(10,2) NULL | |
| image_url | VARCHAR(500) NULL | |
| halal_status | ENUM('halal','haram','unknown') | Default: unknown |
| raw_ingredients | TEXT NULL | Raw string from Open Food Facts API |
| created_at / updated_at | TIMESTAMP | |

### ingredients
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| name | VARCHAR(100) NOT NULL | Arabic or English ingredient name |
| aller_name | VARCHAR(100) NULL | Allergen keyword: gluten / lactose / nuts / pork / halal / egg / soy / sesame / shellfish |
| description | TEXT NULL | |
| created_at / updated_at | TIMESTAMP | |

### products_ingredients
| Column | Type | Notes |
|---|---|---|
| product_id | BIGINT UNSIGNED FK→products | Composite PK |
| ingredient_id | BIGINT UNSIGNED FK→ingredients | Composite PK |
| note | VARCHAR(255) NULL | |

### alternative_products
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| product_id | BIGINT UNSIGNED FK→products | The UNSAFE product |
| alternative_product_id | BIGINT UNSIGNED FK→products | The SAFE replacement |
| created_at | TIMESTAMP | |

### scan_history
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| user_id | BIGINT UNSIGNED FK→users | |
| member_id | BIGINT UNSIGNED FK→family_members | |
| product_id | BIGINT UNSIGNED FK→products | |
| match_status | ENUM('safe','unsafe','over_budget','unsafe_over_budget') | |
| reason | VARCHAR(255) NULL | Human-readable explanation, e.g. "يحتوي على لاكتوز" |
| scan_date | TIMESTAMP NOT NULL | |
| created_at / updated_at | TIMESTAMP | |

### shopping_carts
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| user_id | BIGINT UNSIGNED FK→users | |
| member_id | BIGINT UNSIGNED NULL | Active member this cart is for |
| total_cost | DECIMAL(10,2) | |
| match_status | VARCHAR(50) NULL | |
| purchase_date | TIMESTAMP NULL | Set on checkout |
| created_at / updated_at | TIMESTAMP | |

### cart_items
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| cart_id | BIGINT UNSIGNED FK→shopping_carts | CASCADE DELETE |
| product_id | BIGINT UNSIGNED FK→products | |
| quantity | TINYINT UNSIGNED NOT NULL | Default: 1 |
| total_price | DECIMAL(10,2) NOT NULL | quantity × product.price |
| purchase_date | TIMESTAMP NULL | |
| created_at / updated_at | TIMESTAMP | |

### shopping_lists
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| member_id | BIGINT UNSIGNED FK→family_members | CASCADE DELETE |
| item_name | VARCHAR(255) NOT NULL | |
| is_checked | BOOLEAN NOT NULL | Default: false |
| created_at / updated_at | TIMESTAMP | |

### feedback
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| user_id | BIGINT UNSIGNED FK→users | |
| type | ENUM('rating','suggestion','bug') | Default: rating |
| rating | TINYINT UNSIGNED NULL | 1–5 stars |
| comment | TEXT NULL | |
| created_at / updated_at | TIMESTAMP | |

### support_tickets
| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK AI | |
| user_id | BIGINT UNSIGNED FK→users | |
| message | TEXT NOT NULL | |
| status | ENUM('open','in_progress','closed') | Default: open |
| ticket_date | TIMESTAMP NOT NULL | |
| created_at / updated_at | TIMESTAMP | |

---

## Eloquent Model Relationships

Define these relationships in every Model:

```php
// User.php
public function familyMembers() { return $this->hasMany(FamilyMember::class); }
public function shoppingCarts() { return $this->hasMany(ShoppingCart::class); }
public function scanHistory()   { return $this->hasMany(ScanHistory::class); }
public function feedback()      { return $this->hasMany(Feedback::class); }
public function supportTickets(){ return $this->hasMany(SupportTicket::class); }

// FamilyMember.php
public function user()          { return $this->belongsTo(User::class); }
public function allergyProfiles(){ return $this->hasMany(AllergyProfile::class, 'member_id'); }
public function budget()        { return $this->hasOne(Budget::class, 'member_id'); }
public function scanHistory()   { return $this->hasMany(ScanHistory::class, 'member_id'); }
public function shoppingList()  { return $this->hasMany(ShoppingList::class, 'member_id'); }

// Product.php
public function ingredients()   { return $this->belongsToMany(Ingredient::class, 'products_ingredients'); }
public function alternatives()  { return $this->hasMany(AlternativeProduct::class); }

// ShoppingCart.php
public function items()         { return $this->hasMany(CartItem::class, 'cart_id'); }
public function member()        { return $this->belongsTo(FamilyMember::class, 'member_id'); }
```

---

## Routes Map (routes/web.php)

All routes except auth are wrapped in `middleware('auth')`:

```php
// Public
Route::get('/',               [AuthController::class, 'landing']);
Route::get('/register',       [AuthController::class, 'showRegister']);
Route::post('/register',      [AuthController::class, 'register']);
Route::get('/login',          [AuthController::class, 'showLogin']);
Route::post('/login',         [AuthController::class, 'login']);
Route::post('/logout',        [AuthController::class, 'logout']);

// Protected
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard',              [ScanController::class, 'dashboard']);

    // Profile
    Route::get('/profile',                [UserController::class, 'edit']);
    Route::post('/profile',               [UserController::class, 'update']);

    // Family members
    Route::get('/family',                 [FamilyMemberController::class, 'index']);
    Route::post('/family',                [FamilyMemberController::class, 'store']);
    Route::get('/family/{id}/edit',       [FamilyMemberController::class, 'edit']);
    Route::put('/family/{id}',            [FamilyMemberController::class, 'update']);
    Route::delete('/family/{id}',         [FamilyMemberController::class, 'destroy']);

    // Active member selector
    Route::post('/scan/member',           [ScanController::class, 'selectMember']);

    // Scanning
    Route::get('/scan',                   [ScanController::class, 'index']);
    Route::post('/scan/barcode',          [ScanController::class, 'scan']);
    Route::get('/scan/history',           [ScanController::class, 'history']);
    Route::delete('/scan/history/{id}',   [ScanController::class, 'deleteRecord']);
    Route::post('/scan/rescan/{id}',      [ScanController::class, 'rescan']);

    // Budget
    Route::get('/budget/{memberId}',      [BudgetController::class, 'edit']);
    Route::put('/budget/{memberId}',      [BudgetController::class, 'update']);

    // Cart
    Route::get('/cart',                   [CartController::class, 'index']);
    Route::post('/cart',                  [CartController::class, 'addItem']);
    Route::put('/cart/{id}',              [CartController::class, 'updateQty']);
    Route::delete('/cart/{id}',           [CartController::class, 'removeItem']);
    Route::post('/cart/checkout',         [CartController::class, 'checkout']);

    // Shopping list
    Route::get('/list',                   [ShoppingListController::class, 'index']);
    Route::post('/list',                  [ShoppingListController::class, 'store']);
    Route::patch('/list/{id}/toggle',     [ShoppingListController::class, 'toggle']);
    Route::delete('/list/{id}',           [ShoppingListController::class, 'destroy']);

    // Support & Feedback
    Route::get('/support',                [SupportController::class, 'index']);
    Route::post('/feedback',              [FeedbackController::class, 'store']);
    Route::post('/support',               [SupportController::class, 'store']);
});
```

---

## Core Business Logic

### AllergyChecker Service (`app/Services/AllergyChecker.php`)

```php
<?php
namespace App\Services;

use App\Models\FamilyMember;
use App\Models\Product;
use App\Models\Ingredient;

class AllergyChecker
{
    public function check(Product $product, FamilyMember $member): array
    {
        $triggered = [];
        $rawIngredients = strtolower($product->raw_ingredients ?? '');
        $allergyTypes = $member->allergyProfiles->pluck('allergy_type')->toArray();

        foreach ($allergyTypes as $allergyType) {
            // Special case: halal check
            if ($allergyType === 'halal') {
                if ($product->halal_status === 'haram') {
                    $triggered[] = 'halal';
                    continue;
                }
            }

            // Keyword matching against raw_ingredients string
            $keywords = Ingredient::where('aller_name', $allergyType)
                ->pluck('name')
                ->toArray();

            foreach ($keywords as $keyword) {
                if (str_contains($rawIngredients, strtolower($keyword))) {
                    $triggered[] = $allergyType;
                    break; // one match per allergy type is enough
                }
            }
        }

        return [
            'safe'               => empty($triggered),
            'triggered_allergens'=> array_unique($triggered),
        ];
    }
}
```

### ScanController@scan — Full Flow

```php
public function scan(Request $request)
{
    $barcode     = $request->input('barcode');
    $memberId    = session('active_member_id');
    $member      = FamilyMember::with(['allergyProfiles', 'budget'])->findOrFail($memberId);

    // 1. Fetch or retrieve product from local DB
    $product = Product::where('barcode', $barcode)->first();
    if (!$product) {
        $product = $this->fetchFromOpenFoodFacts($barcode);
        if (!$product) {
            return back()->with('error', 'المنتج غير موجود في قاعدة البيانات');
        }
    }

    // 2. Run allergy check
    $checker      = new \App\Services\AllergyChecker();
    $allergyResult = $checker->check($product, $member);

    // 3. Run budget check
    $budgetResult  = $this->checkBudget($product->price, $member->budget);

    // 4. Determine match_status
    $safe          = $allergyResult['safe'];
    $withinBudget  = $budgetResult['within_budget'];

    $matchStatus = match(true) {
        $safe && $withinBudget    => 'safe',
        !$safe && $withinBudget   => 'unsafe',
        $safe && !$withinBudget   => 'over_budget',
        default                   => 'unsafe_over_budget',
    };

    // 5. Build reason string (Arabic)
    $reasons = [];
    foreach ($allergyResult['triggered_allergens'] as $allergen) {
        $reasons[] = "يحتوي على {$allergen}";
    }
    if (!$withinBudget) {
        $reasons[] = "تجاوز الميزانية {$budgetResult['exceeded_period']}";
    }

    // 6. Save to scan_history
    ScanHistory::create([
        'user_id'      => auth()->id(),
        'member_id'    => $member->id,
        'product_id'   => $product->id,
        'match_status' => $matchStatus,
        'reason'       => implode(' | ', $reasons) ?: null,
        'scan_date'    => now(),
    ]);

    // 7. Get alternatives if needed
    $alternatives = [];
    if ($matchStatus !== 'safe') {
        $alternatives = Product::whereIn('id',
            \App\Models\AlternativeProduct::where('product_id', $product->id)
                ->pluck('alternative_product_id')
        )->get();
    }

    return view('scan.result', compact('product', 'member', 'allergyResult', 'budgetResult', 'matchStatus', 'alternatives'));
}
```

### BudgetController — checkBudget Helper

```php
private function checkBudget(?float $price, ?Budget $budget): array
{
    if (!$budget || !$price) {
        return ['within_budget' => true, 'exceeded_period' => null];
    }

    $dailyRemaining   = $budget->daily_budget   - $budget->daily_spent;
    $weeklyRemaining  = $budget->weekly_budget  - $budget->weekly_spent;
    $monthlyRemaining = $budget->monthly_budget - $budget->monthly_spent;

    if ($price > $dailyRemaining)   return ['within_budget' => false, 'exceeded_period' => 'اليومية'];
    if ($price > $weeklyRemaining)  return ['within_budget' => false, 'exceeded_period' => 'الأسبوعية'];
    if ($price > $monthlyRemaining) return ['within_budget' => false, 'exceeded_period' => 'الشهرية'];

    return ['within_budget' => true, 'exceeded_period' => null];
}
```

### CartController@checkout

```php
public function checkout(Request $request)
{
    $cart   = ShoppingCart::with('items.product')
                ->where('user_id', auth()->id())
                ->whereNull('purchase_date')
                ->firstOrFail();

    $total  = $cart->items->sum('total_price');
    $member = FamilyMember::with('budget')->findOrFail($cart->member_id);
    $budget = $member->budget;

    // Deduct from all three budget periods
    $budget->increment('daily_spent',   $total);
    $budget->increment('weekly_spent',  $total);
    $budget->increment('monthly_spent', $total);

    // Finalize cart
    $cart->update([
        'total_cost'    => $total,
        'purchase_date' => now(),
    ]);
    $cart->items()->update(['purchase_date' => now()]);

    // Create a fresh empty cart for next session
    ShoppingCart::create(['user_id' => auth()->id(), 'member_id' => $cart->member_id, 'total_cost' => 0]);

    return redirect('/dashboard')->with('success', 'تمت عملية الشراء بنجاح');
}
```

### Open Food Facts API Integration

```php
private function fetchFromOpenFoodFacts(string $barcode): ?Product
{
    $response = \Illuminate\Support\Facades\Http::timeout(5)
        ->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

    if (!$response->ok() || $response->json('status') !== 1) {
        return null;
    }

    $data = $response->json('product');

    return Product::updateOrCreate(
        ['barcode' => $barcode],
        [
            'pr_name'         => $data['product_name']     ?? 'منتج غير معروف',
            'brand'           => $data['brands']           ?? null,
            'image_url'       => $data['image_url']        ?? null,
            'raw_ingredients' => $data['ingredients_text'] ?? null,
            'halal_status'    => 'unknown',
        ]
    );
}
```

---

## Session Pattern — Active Member

Always use this pattern to track which family member is being shopped for:

```php
// Set active member (ScanController@selectMember)
session(['active_member_id' => $request->validated()['member_id']]);

// Read active member in any controller
$member = FamilyMember::with(['allergyProfiles', 'budget'])
    ->where('user_id', auth()->id())   // security: must belong to logged-in user
    ->findOrFail(session('active_member_id'));
```

---

## UI / WebView Rules

### Blade Layout Head (layouts/app.blade.php)
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta charset="UTF-8">
<html lang="ar" dir="rtl">
```

### CSS Color Variables (exist in desgin/css file)

### Visual Indicator Rules
| Status | Icon | Color | Message |
|---|---|---|---|
| Safe product | ✅ green checkmark | `--color-safe` | آمن لـ [اسم العضو] |
| Unsafe product | ❌ red X + allergen icon | `--color-unsafe` | غير مناسب — يحتوي على [allergen] |
| Over budget | 💰 red money icon | `--color-unsafe` | تجاوز الميزانية [اليومية/الأسبوعية/الشهرية] |
| Both unsafe + over budget | ❌ + 💰 | `--color-unsafe` | Show both warnings |

### Budget Progress Bar
```css
.budget-bar { height: 12px; border-radius: 6px; background: #E0E0E0; }
.budget-bar-fill {
    height: 100%;
    border-radius: 6px;
    transition: width 0.4s ease;
}
/* Dynamic class applied by Blade based on % remaining */
.budget-ok   { background: var(--color-budget-ok);  } /* > 50% remaining */
.budget-low  { background: var(--color-budget-low); } /* 20–50% remaining */
.budget-out  { background: var(--color-budget-out); } /* < 20% remaining */
```

### Allergen Icon Map
```
lactose   → 🥛  icon-milk.svg
gluten    → 🌾  icon-wheat.svg
nuts      → 🥜  icon-nut.svg
pork      → 🐷  icon-pig.svg
halal     → ☪️  icon-halal.svg
egg       → 🥚  icon-egg.svg
soy       → 🫘  icon-soy.svg
shellfish → 🦐  icon-shrimp.svg
```

### QuaggaJS Barcode Scanner (public/js/scanner.js)
```javascript
Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#scanner-container'),
        constraints: { facingMode: "environment" }  // use back camera
    },
    decoder: {
        readers: ["ean_reader", "ean_8_reader", "code_128_reader"]
    }
}, function(err) {
    if (err) { console.error(err); return; }
    Quagga.start();
});

Quagga.onDetected(function(result) {
    const barcode = result.codeResult.code;
    Quagga.stop();
    document.getElementById('barcode-input').value = barcode;
    document.getElementById('scan-form').submit();
});
```

### Voice Readout (Web Speech API)
```javascript
function speakResult(message) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(message);
        utterance.lang = 'ar-SA';
        utterance.rate = 0.9;
        window.speechSynthesis.speak(utterance);
    }
}
// Call after scan result loads:
// speakResult('المنتج آمن لنورة') or speakResult('تحذير: يحتوي على لاكتوز')
```

---

## Coding Rules — Always Follow

1. **Always use Eloquent ORM** — never raw SQL queries inside controllers.
2. **Always validate requests** — use `$request->validate([...])` before touching the DB.
3. **Always use `@csrf`** in every Blade form.
4. **Always check ownership** — when loading a FamilyMember, always add `.where('user_id', auth()->id())` to prevent users from accessing other families' data.
5. **Never store `remaining_amount`** in the database — always compute it: `$budget->daily_budget - $budget->daily_spent`.
6. **Always use `number_format($value, 2)`** when displaying money in Blade.
7. **File uploads** must use: `$request->file('photo')->store('avatars', 'public')`.
8. **Flash messages** use: `session()->flash('success', '...')` and `session()->flash('error', '...')`.
9. **Generate with Artisan** using: `php artisan make:model ModelName -mcr`.
10. **Barcode scanning requires HTTPS or localhost** — camera will be blocked on plain HTTP on real devices.

---

## Seed Data Reference

The database is pre-seeded with:

| Table | Rows | Key Data |
|---|---|---|
| users | 5 | نورة / محمد / سارة / فيصل / هند |
| family_members | 12 | Children, spouses, elderly parents |
| allergy_profiles | 21 | lactose / gluten / nuts / halal combinations |
| budgets | 12 | Some near limit (سارة at 98% daily) |
| products | 20 | Real KSA barcodes — نادك / المراعي / لولو / ليلى |
| ingredients | 48 | Arabic + English allergen keywords |
| scan_history | 23 | safe / unsafe / over_budget / unsafe_over_budget examples |
| shopping_carts | 5 | One active cart per user |
| cart_items | 12 | Safe items only per member's allergy profile |
| shopping_lists | 18 | Per-member lists with checked/unchecked items |
| feedback | 5 | Ratings + suggestions + bug reports |
| support_tickets | 5 | open / in_progress / closed examples |

**All users share the same test password hash:**
```
$2y$10$aKhNNNXlP3R96DfwJdPTn.ew6scWdg2tSCSUUNwGzaP/SRpqOEtCG
```

**Test login:**
- Email: `noura@famshop.sa`
- Password: *(the plain text that matches the hash above)*

---

## Task Phases — Build Order

Follow this order strictly. Each phase depends on the previous.

| Phase | Name | Key Deliverable |
|---|---|---|
| 1 | Environment & Foundation | .env config, DB, layout, assets |
| 2 | Authentication | Landing, Register, Login, Logout |
| 3 | User Profile | Edit profile, photo upload |
| 4 | Family Members | CRUD members, allergy selection, budget setup, session selector |
| 5 | Product & Barcode | Open Food Facts API, local cache, QuaggaJS scanner |
| 6 | Allergen Validation | AllergyChecker service, visual indicators, alternatives |
| 7 | Budget Tracking | Dashboard card, price check, progress bar, deduct on checkout |
| 8 | Shopping Cart | Add/remove/qty/checkout with budget deduction |
| 9 | Shopping List | Per-member list, check off, delete |
| 10 | Scan History | Auto-save, list screen, re-check, delete |
| 11 | Support & Feedback | Star rating, comment, support ticket |
| 12 | Accessibility & Polish | RTL Arabic, voice readout, large icons, color system |
| 13 | Testing & Launch | Unit tests, WebView shell build, deployment |

