# Plan: Create Loan Management Page & Search Preview Component

## Overview
Create a complete loan management page (`loans/index.html.twig`) and search preview component (`_loan_preview.html.twig`) following the same pattern as the students template.

---

## Entity Structure (Loan.php)

| Property | Getter | Twig Access |
|----------|--------|-------------|
| `student` | `getStudent()` | `loan.student.firstName`, `loan.student.lastName` |
| `book` | `getBook()` | `loan.book.title` |
| `tutor` | `getTutor()` | `loan.tutor.firstName` |
| `status` | `getStatus()` | `loan.status.value` |
| `loan_date` | `getLoanDate()` | `loan.loanDate` |
| `return_date` | `getReturnDate()` | `loan.returnDate` |

### Status Enum (LoanEnum)
- `active` - Verde (#10b981)
- `returned` - Azul (#3b82f6)
- `overdue` - Naranja (#f59e0b)
- `lost` - Rojo (#ef4444)

---

## Files to Create

### 1. `templates/loans/index.html.twig`
Main loan management page with sidebar, search, filters, and responsive table/cards.

### 2. `templates/components/_loan_preview.html.twig`
Search preview dropdown partial.

### 3. `assets/controllers/search-loan_controller.js`
Stimulus controller for loan search functionality.

---

## Implementation Details

### 1. `loans/index.html.twig` Structure

**Header:** "Préstamos"

**Search Bar:**
- Placeholder: "Buscar préstamo..."
- Connected to `search-loan` Stimulus controller

**Filter Buttons:**
- Filtrar por estado
- Filtrar por tutor
- Más recientes

**Desktop Table Layout (768px+):**
| Estudiante | Libro | Tutor | Estado | Fecha Préstamo | Fecha Devolución | Acciones |
|------------|-------|-------|--------|----------------|------------------|----------|

Grid columns: `1fr 1fr 100px 100px 110px 110px 60px`

**Mobile Card Layout (<768px):**
```
┌─────────────────────────────────────────┐
│ Juan García                             │
│ 📖 "El Principito"                      │
│ 👤 Tutor: María López                   │
│ 🏷️ [Active]  📅 15 Ene → 30 Ene        │
└─────────────────────────────────────────┘
```

### 2. `_loan_preview.html.twig` Structure

**Empty State:**
- Icon: Document/clipboard icon
- Message: "No se encontraron préstamos"
- Subtitle: "Intenta con otro término de búsqueda"

**Results Header:**
- Counter badge with purple gradient background
- Text: "X Préstamo(s) encontrado(s)"

**Desktop Table Layout (768px+):**
| Estudiante | Libro | Tutor | Estado | F. Préstamo | F. Devolución |
|------------|-------|-------|--------|-------------|---------------|

Grid columns: `1fr 1fr 100px 90px 100px 100px`

**Mobile Card Layout (<768px):**
Similar to main page cards

### 3. Stimulus Controller (`search-loan_controller.js`)

Based on `search-student_controller.js`:
- Targets: `result`, `filter`
- Values: `url` (String)
- Methods:
  - `handleSearch()` - debounced search (300ms)
  - `showLoading()` - purple spinner
  - `performSearch()` - fetch with `?q=query&preview=1`
  - `showResults()` / `hideResults()`
  - `hideOnClickOutside()`

---

## Status Badge Styling

```twig
{% if loan.status.value == 'active' %}
    {% set statusBg = '#dcfce7' %}
    {% set statusColor = '#166534' %}
{% elseif loan.status.value == 'returned' %}
    {% set statusBg = '#dbeafe' %}
    {% set statusColor = '#1e40af' %}
{% elseif loan.status.value == 'overdue' %}
    {% set statusBg = '#fef3c7' %}
    {% set statusColor = '#92400e' %}
{% else %} {# lost #}
    {% set statusBg = '#fee2e2' %}
    {% set statusColor = '#991b1b' %}
{% endif %}
```

---

## Sidebar Navigation
Copy from `students/index.html.twig` with:
- "Loans" item highlighted (active state)
- Update other items to non-active state

---

## Backend Requirements (Developer must implement)

1. Create `LoanController.php` with route `loan_index`:
```php
#[Route('/loan', name: 'loan_index')]
public function index(Request $request): Response
{
    $searchTerm = $request->query->get('q');

    if ($request->query->get('preview')) {
        $loans = $this->loanService->filterLoanByTerm($searchTerm);
        return $this->render('components/_loan_preview.html.twig', [
            'loans' => $loans
        ]);
    }

    $loans = $this->loanService->getLoans();
    return $this->render('loans/index.html.twig', [
        'loans' => $loans
    ]);
}
```

2. Create `LoanService.php` with methods:
   - `getLoans()` - returns all loans
   - `filterLoanByTerm($term)` - search by student name, book title, or tutor name

---

## Verification Steps

1. [ ] Create `templates/loans/` directory
2. [ ] Create `loans/index.html.twig` with full page layout
3. [ ] Create `_loan_preview.html.twig` with empty state and results
4. [ ] Create `search-loan_controller.js`
5. [ ] Test responsive layouts (desktop/mobile)
6. [ ] Verify status badge colors
7. [ ] Test search functionality (requires backend)

---

## Notes

- All property access uses camelCase to match entity getters
- Status is an enum, access value with `.value`
- Dates formatted with Twig `|date('d M, Y')`
- Return date may be null for active loans - handle with `loan.returnDate ? loan.returnDate|date('d M, Y') : '-'`
