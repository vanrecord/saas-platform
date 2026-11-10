# Patients Module Refactoring Report

## 1. Executive Summary

The **Patients module** (`http://sovann.tenancy-core.test/patients`) in this Laravel Tenancy application has been refactored to align with the design patterns, architecture, and UI/UX conventions established in the **User module** (`http://saas-platform.test/user`).

The refactoring achieves:
- Replaced the custom manual table implementation with the shared, server-side `ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable` package paired with `@/Components/Datatables` (`<Table :resource="patients" />`).
- Complete repository pattern implementation with `PatientRepositoryInterface` and `PatientRepository` extending `BaseRepository` (`Rinvex\Repository\Repositories\EloquentRepository`).
- Full decoupling of database queries from `PatientController`, delegating query building, sorting, global search, filtering, and CRUD operations to the repository.
- Consistent **FormBuilder-style UI** across `Create.vue` and `Edit.vue` using reusable form components (`TextInput`, `SelectInput`, `TextareaInput`, `SwitchInput`, `SaveButton`, `BackButton`).
- Seamless preservation of all patient-specific domain capabilities: avatar upload/storage, calculated age from DOB, unique `id_number`, financial balance, card status switches, and e-Health integration.

---

## 2. Architecture Comparison: User Module vs. Patients Module

| Component | User Module (Reference) | Patients Module (Before) | Patients Module (Refactored) |
| :--- | :--- | :--- | :--- |
| **DataTable Backend** | `ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable` + `Spatie\QueryBuilder` | Manual `Patient::query()` with manual `LIKE` filters and `orderBy` | `ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable` + `Spatie\QueryBuilder` |
| **DataTable Frontend** | `<Table :resource="users">` from `@/Components/Datatables` | Custom manual `<table>` with manual SVG pagination and query state | Shared `<Table :resource="patients">` from `@/Components/Datatables` |
| **Search & Filtering** | Global search + column search rows + select filters (Status, Admin) | Single manual text input triggering Inertia visit | Global search + column filters (`card_activated`, `gender`) + column visibility toggle |
| **Pagination** | Shared `Pagination` with per-page dropdown (`[10, 15, 30, 50, 100]`) | Custom inline pagination bar | Shared `Pagination` with per-page dropdown and query preservation |
| **Actions Menu** | `JetDropdown` with View, Edit, Delete (`@/Libs/Action/Delete.vue` with SweetAlert2) | Inline action buttons | `JetDropdown` with View (`patients.show`), Edit (`patients.edit`), Delete (`patients.destroy`) with SweetAlert2 confirmation |
| **Database Queries** | `UserRepository` extending `BaseRepository` | Directly inside `PatientController` | `PatientRepository` implementing `PatientRepositoryInterface` extending `BaseRepository` |
| **Dependency Injection** | `App(UserRepository::class)` | N/A | Constructor DI: `PatientRepositoryInterface` bound in `AppServiceProvider` |
| **Form Layout** | FormBuilder-style card layout with section headings, grid spacing, reusable inputs | Custom horizontal flex layout with hardcoded labels and inputs | Standard FormBuilder card layout with section headers, 2-column responsive grid, and `@/Components/Form/` |

---

## 3. Implementation Details

### 3.1. Repository Pattern Implementation

#### `app/Repositories/PatientRepositoryInterface.php`
Defines the contract for all patient data operations:
- `getDataTable()`: Constructs server-side QueryBuilder instance with global search, allowed sorts, and filters.
- `columnsDataTable($table)`: Configures columns, per-page options, global search, and select filters on the `InertiaTable` instance.
- `getNextIdNumber()`: Retrieves next sequential identification number.
- `find($id, array $columns = ['*'])`: Finds patient record by ID.
- `findWithDetails($id)`: Eager-loads dental charts and visit records for the show view.
- `create(array $attributes = [])`: Persists new patient record.
- `update($id, array $attributes = [])`: Updates existing patient record.
- `delete($id)`: Safely removes patient record (handling soft deletes).

#### `app/Repositories/PatientRepository.php`
Implements `PatientRepositoryInterface` and extends `BaseRepository`:
```php
namespace App\Repositories;

use App\Models\Tenant\Patient;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Patient::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) $value = implode(',', $value);
            $query->where(function ($query) use ($value) {
                $term = strtolower(trim($value));
                $query->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(phone)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(email)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(card_code)'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id_number', (int) $value)
                          ->orWhere('id', (int) $value);
                }
            });
        });

        return QueryBuilder::for(Patient::class)
            ->defaultSort('-id')
            ->allowedFields(['id', 'id_number', 'firstname', 'lastname', 'gender', 'age', 'dob', 'phone', 'email', 'profession', 'address', 'balance', 'avatar', 'card_activated', 'card_code', 'is_trail_card', 'created_at', 'updated_at'])
            ->allowedSorts(['id', 'id_number', 'firstname', 'lastname', 'gender', 'age', 'dob', 'phone', 'email', 'profession', 'balance', 'card_activated', 'created_at'])
            ->allowedFilters(['firstname', 'lastname', 'phone', 'email', 'gender', AllowedFilter::exact('id_number'), AllowedFilter::exact('card_activated'), $globalSearch])
            ->paginate()
            ->withQueryString();
    }
...
```

#### `app/Providers/AppServiceProvider.php`
Bound in container:
```php
public function register()
{
    $this->app->bind(
        \App\Repositories\PatientRepositoryInterface::class,
        \App\Repositories\PatientRepository::class
    );
}
```

---

### 3.2. Refactored Controller Architecture

#### `app/Http/Controllers/Tenant/PatientController.php`
- Cleanly decoupled: all queries delegated to `PatientRepositoryInterface`.
- Injected through constructor: `public function __construct(PatientRepositoryInterface $patient)`.
- Handles HTTP requests, file uploads (`avatar` disk storage), boolean casts, error aborts, and flash notifications.

```php
class PatientController extends Controller
{
    protected PatientRepositoryInterface $patient;

    public function __construct(PatientRepositoryInterface $patient)
    {
        $this->patient = $patient;
    }

    public function index(): Response
    {
        $patients = $this->patient->getDataTable();
        return Inertia::render('Tenant/Patient/Index', [
            'patients' => $patients,
        ])->table(function (InertiaTable $table) {
            $this->patient->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Patient/Create', [
            'suggestedIdNumber' => $this->patient->getNextIdNumber(),
        ]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        // validates, calculates age if dob provided, stores avatar, calls repository create
        $patient = $this->patient->create($data);
        return redirect()->route('patients.index')->with('success', 'Patient record created successfully.');
    }

    public function show($id): Response
    {
        $patient = $this->patient->findWithDetails($id);
        if (!$patient) abort(404, 'Patient not found');
        return Inertia::render('Tenant/Patient/Show', ['patient' => $patient]);
    }

    public function edit($id): Response
    {
        $patient = $this->patient->find($id);
        if (!$patient) abort(404, 'Patient not found');
        return Inertia::render('Tenant/Patient/Edit', ['patient' => $patient]);
    }

    public function update(PatientRequest $request, $id): RedirectResponse
    {
        $patient = $this->patient->find($id);
        // processes avatar update/removal, calls repository update
        $this->patient->update($patient, $data);
        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $patient = $this->patient->find($id);
        if ($patient) {
            $this->patient->delete($patient);
        }
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}
```

---

### 3.3. Frontend DataTable: `resources/js/Pages/Tenant/Patient/Index.vue`
- Uses shared `<Table :resource="patients">` from `@/Components/Datatables`.
- Column slots:
  - `#cell(id)`: row index counter
  - `#cell(avatar)`: patient avatar thumbnail with initial avatar fallback
  - `#cell(id_number)`: formatted patient ID (`#571`)
  - `#cell(firstname)` & `#cell(lastname)`: clickable links to patient view
  - `#cell(gender)`, `#cell(age)`, `#cell(dob)`: patient demographics
  - `#cell(phone)`, `#cell(email)`: contact info
  - `#cell(created_at)`: formatted date
  - `#cell(actions)`: `JetDropdown` with View, Edit, and SweetAlert2 Delete
- Full compatibility with tenant navigation via `TenantLayout`.

---

### 3.4. FormBuilder-Style UI: `Create.vue` & `Edit.vue`
Both forms now match the layout and design of `resources/js/Pages/User/Create.vue`:
1. **Breadcrumb Bar**: `Home` / `Patients` / `Create` or `Edit`.
2. **Card Container**: `col-span-2 bg-white rounded-md dark:bg-darker shadow-sm`.
3. **Form Sections**:
   - **Patient Information**: `id_number`, `firstname`, `lastname`, `gender`, `dob`, `age`.
   - **Contact Information**: `phone`, `email`, `profession`, `address`.
   - **Avatar**: Circular preview, "Choose Avatar" trigger, and "Remove" action.
4. **Action Buttons**: Reusable `<save-button>` and `<back-button>`.
5. **Real-Time Dynamic Feedback**: Selecting a Date of Birth automatically computes and populates the Age field.
6. **Toast Feedback**: `vue-toastification` notifications triggered on successful create/update/delete.

---

### 3.5. Patient View: `resources/js/Pages/Tenant/Patient/Show.vue`
- Preserved existing patient profile display, avatar view, card details, dental examination charts list, visit register shortcut, and e-Health modal.
- Added top-level SweetAlert2 delete confirmation button for quick administrative removal.

---

### 3.6. Two-Way Automatic Age & Date of Birth Calculation
A bidirectional, reactive synchronization between **Date of Birth** (`dob`) and **Age** (`age`) was implemented in both `Create.vue` and `Edit.vue`:
1. **DOB → Age**:
   - Computes completed years based on today's reference date (`new Date()`).
   - Accounts for month and day comparisons.
   - Rejects future dates (sets age to `null`).
   - Automatically synchronizes immediately when user selects or modifies DOB.
2. **Age → DOB**:
   - Calculates birth date by subtracting integer age from today's date.
   - Leap year edge case protection (safely resolves Feb 29 to Feb 28 on non-leap years).
   - Rejects negative or non-integer values.
   - Formats to `YYYY-MM-DD` and sets the date-picker component immediately.
3. **Loop & State Protection**:
   - `isSyncing` flag prevents infinite reactive update loops between watchers.
   - Clearing either field safely clears the corresponding value without unexpected recalculations.
   - `isInitialLoad` in `Edit.vue` ensures existing patient data is not dirtied on mount (calculating age from DOB if DOB is present, while preserving custom age if DOB is empty).
4. **Backend Validation & Recalculation**:
   - `PatientRequest`: Added `'dob' => ['nullable', 'date', 'before_or_equal:today']` and `'age' => ['nullable', 'integer', 'min:0', 'max:150']`.
   - `PatientController`: Server treats `dob` as authoritative and recalculates `age` via Carbon; if only `age` is submitted, calculates `dob` using `Carbon::today()->subYearsNoOverflow((int) $data['age'])`.

---

## 4. Verification Checklist

| Item | Requirement | Status |
| :--- | :--- | :--- |
| **DataTable UI/UX** | Matches User DataTable design, styles, loading state, empty state, and column sorting | Verified |
| **Server-Side AJAX** | Spatie QueryBuilder + InertiaTable query handling with pagination and filters | Verified |
| **Repository Architecture** | `PatientRepositoryInterface` and `PatientRepository` extending `BaseRepository` | Verified |
| **Controller Decoupling** | Zero raw DB queries inside `PatientController` | Verified |
| **FormBuilder UI** | Reusable `TextInput`, `SelectInput`, `TextareaInput`, `SwitchInput`, `SaveButton`, `BackButton` | Verified |
| **CRUD Operations** | Create, View, Edit, Delete with SweetAlert2 confirmation and toast notifications | Verified |
| **Data Integrity** | Matches actual `patients` tenant migration fields without inventing columns | Verified |
| **Vite Bundle Build** | `npm run build` completed with 0 errors | Verified |
