<?php

namespace Tests\Feature;

use App\Models\CompensationRecord;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AlphalistTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Alphalist');
    }

    public function test_recent_employees_show_created_employees_without_compensation_records(): void
    {
        $this->actingAs(User::factory()->create());

        Employee::create([
            'tin' => '123-456-789-001',
            'first_name' => 'Ana',
            'last_name' => 'Santos',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        Employee::create([
            'tin' => '123-456-789-002',
            'first_name' => 'Ben',
            'last_name' => 'Reyes',
            'employment_status' => 'Regular',
            'monthly_salary' => 35000,
        ]);

        $response = $this->get('/dashboard');

        $response->assertSee('Ana Santos');
        $response->assertSee('Ben Reyes');
    }

    public function test_guest_cannot_open_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_payroll_records_forms_reports_or_exports(): void
    {
        foreach ([
            '/employees',
            '/compensation',
            '/forms',
            '/forms/choose',
            '/reports',
            '/reports/export-csv',
            '/reports/export-excel',
            '/reports/export-pdf',
        ] as $path) {
            $this->get($path)->assertRedirect('/login');
        }
    }

    public function test_bir_alphalist_page_lists_second_level_modules(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/forms');

        $response->assertStatus(200);
        $response->assertSee('1604-CF');
        $response->assertSee('MAP');
        $response->assertSee('1604-E');
        $response->assertSee('SAWT');
        $response->assertDontSee('1604-F');
        $response->assertSee('Minimum Wage Earners');
        $response->assertSee('Monthly Payees');
        $response->assertSee('Expanded Withholding');
        $response->assertSee('href="'.route('forms.annual').'"', false);
        $response->assertSee('href="'.route('forms.map').'"', false);
        $response->assertSee('href="'.route('forms.1604e').'"', false);

        $annualResponse = $this->actingAs(User::factory()->create())->get('/forms/annual');

        $annualResponse->assertOk();
        $annualResponse->assertSee('1604-CF Annual Alphalist');
        $annualResponse->assertSessionHas('selected_tax_form', 'Annual');

        $expandedWithholdingResponse = $this->get('/forms/1604-e');

        $expandedWithholdingResponse->assertOk();
        $expandedWithholdingResponse->assertSee('<title>1604-E</title>', false);
        $expandedWithholdingResponse->assertSee('>1604-E</h2>', false);
        $expandedWithholdingResponse->assertDontSee('QAP Forms');
        $expandedWithholdingResponse->assertSessionHas('selected_tax_form', 'QAP');
        $expandedWithholdingResponse->assertSee('compensation/create?tax_form=QAP');
    }

    public function test_tax_form_chooser_lists_only_supported_bir_forms(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/forms/choose');

        $response->assertSeeInOrder(['1604-CF', 'MAP', '1604-E', 'SAWT']);
        $response->assertDontSee('1604-F');
        $response->assertDontSee('Minimum Wage Earners');
        $response->assertDontSee('Monthly Payees');
        $response->assertDontSee('Expanded Withholding');
        $response->assertSee('action="'.route('logout').'"', false);
        $response->assertSee('>Logout</button>', false);
        $response->assertSee('href="'.route('compensation.index').'?tax_form="', false);
    }

    public function test_sawt_add_record_returns_to_sawt_form(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/forms/sawt');

        $response = $this->get('/compensation/create');

        $response->assertSee('Back to SAWT Form');
        $response->assertSee('name="tax_form" value="SAWT"', false);
    }

    public function test_add_record_accepts_display_name_and_uses_canonical_tax_form(): void
    {
        $this->actingAs(User::factory()->create());

        $formResponse = $this->get('/forms/map');
        $formResponse->assertSee('compensation/create?tax_form=MAP');

        $createResponse = $this->get('/compensation/create?tax_form=MAP%20Form');

        $createResponse->assertOk();
        $createResponse->assertSee('Tax form: <span class="font-semibold">MAP</span>', false);
        $createResponse->assertSee('name="tax_form" value="MAP"', false);
        $createResponse->assertSee('Back to MAP Form');
    }

    public function test_compensation_records_page_does_not_show_add_record_button(): void
    {
        $this->actingAs(User::factory()->create());
        $this->withSession(['selected_tax_form' => 'MAP']);

        $response = $this->get('/compensation?tax_form=');

        $response->assertOk();
        $response->assertDontSee('Add Record');
    }

    public function test_sawt_records_can_be_filtered_by_tax_year_pay_period_and_employee(): void
    {
        $this->actingAs(User::factory()->create());

        $firstEmployee = Employee::create([
            'tin' => '111-111-111-111',
            'first_name' => 'Ana',
            'last_name' => 'Santos',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);
        $secondEmployee = Employee::create([
            'tin' => '222-222-222-222',
            'first_name' => 'Ben',
            'last_name' => 'Reyes',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        CompensationRecord::create([
            'employee_id' => $firstEmployee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-09',
            'tax_form' => 'SAWT',
            'gross_compensation' => 1000,
            'taxable_compensation' => 1000,
            'tax_withheld' => 100,
        ]);
        CompensationRecord::create([
            'employee_id' => $secondEmployee->id,
            'tax_year' => 2025,
            'pay_period' => '2025-09',
            'tax_form' => 'SAWT',
            'gross_compensation' => 2000,
            'taxable_compensation' => 2000,
            'tax_withheld' => 200,
        ]);

        $response = $this->get('/forms/sawt?tax_year=2026&pay_period=2026-09&search=Ana');

        $response->assertSee('Ana Santos');
        $response->assertDontSee('Ben Reyes');
        $response->assertSee('₱1,000.00');
        $response->assertDontSee('₱2,000.00');
    }

    public function test_all_tax_form_filter_shows_records_from_every_form(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '333-333-333-333',
            'first_name' => 'Cara',
            'last_name' => 'Cruz',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        foreach (['MAP', 'SAWT'] as $taxForm) {
            CompensationRecord::create([
                'employee_id' => $employee->id,
                'tax_year' => 2026,
                'pay_period' => $taxForm === 'MAP' ? '2026-07' : '2026-08',
                'tax_form' => $taxForm,
                'gross_compensation' => 1000,
                'taxable_compensation' => 1000,
                'tax_withheld' => 100,
            ]);
        }

        $response = $this->get('/compensation?tax_form=');

        $response->assertSee('Tax form:');
        $response->assertSee('All');
        $response->assertSee('2026-08');
        $response->assertSee('SAWT');
        $response->assertSee('2026-07');
        $response->assertSee('MAP');
    }

    public function test_compensation_record_view_route_is_defined_and_loads_record(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '333-333-333-334',
            'first_name' => 'Cara',
            'last_name' => 'Cruz',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        $record = CompensationRecord::create([
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-09',
            'tax_form' => 'MAP',
            'gross_compensation' => 25000,
            'taxable_compensation' => 25000,
            'tax_withheld' => 1250,
        ]);

        $response = $this->get(route('compensation.show', $record));

        $response->assertOk();
        $response->assertSee('Cara Cruz');
        $response->assertSee('₱1,250.00');
    }

    public function test_duplicate_employee_tin_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        Employee::create([
            'tin' => '123-456-789-012',
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        $response = $this->post('/employees', [
            'tin' => '123-456-789-012',
            'first_name' => 'Alice',
            'last_name' => 'Duplicate',
            'employment_status' => 'Regular',
            'monthly_salary' => '35000',
        ]);

        $response->assertSessionHasErrors('tin');
        $this->assertDatabaseCount('employees', 1);
    }

    public function test_compensation_record_ignores_submitted_tax_withheld_and_calculates_from_rate(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '444-444-444-444',
            'first_name' => 'Diana',
            'last_name' => 'Jones',
            'employment_status' => 'Regular',
            'monthly_salary' => 50000,
        ]);

        $response = $this->post('/compensation', [
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-10',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'tax_rate' => '5',
            'gross_compensation' => '1000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
            'tax_withheld' => '1500',
        ]);

        $response->assertRedirect('/forms/map');
        $this->assertDatabaseHas('compensation_records', [
            'employee_id' => $employee->id,
            'tax_withheld' => '50.00',
        ]);
    }

    public function test_compensation_record_calculates_tax_withheld_from_entered_rate_and_compensation(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '444-444-444-445',
            'first_name' => 'Diana',
            'last_name' => 'Jones',
            'employment_status' => 'Regular',
            'monthly_salary' => 50000,
        ]);

        $response = $this->post('/compensation', [
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-10',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'tax_rate' => '5',
            'gross_compensation' => '30000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
        ]);

        $response->assertRedirect('/forms/map');
        $this->assertDatabaseHas('compensation_records', [
            'employee_id' => $employee->id,
            'tax_rate' => '5.0000',
            'gross_compensation' => '30000.00',
            'taxable_compensation' => '30000.00',
            'tax_withheld' => '1500.00',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => auth()->id(),
            'model_type' => CompensationRecord::class,
            'model_id' => CompensationRecord::query()->value('id'),
            'action' => 'created',
        ]);
    }

    public function test_employee_and_compensation_create_update_delete_actions_are_audited(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/employees', [
            'tin' => '444-444-444-446',
            'first_name' => 'Diana',
            'last_name' => 'Jones',
            'employment_status' => 'Regular',
            'monthly_salary' => '50000',
        ])->assertRedirect('/');

        $employee = Employee::query()->where('tin', '444-444-444-446')->firstOrFail();

        $this->put(route('employees.update', $employee), [
            'tin' => $employee->tin,
            'first_name' => 'Diana',
            'last_name' => 'Jones',
            'employment_status' => 'Regular',
            'monthly_salary' => '52000',
        ])->assertRedirect(route('employees.index'));

        $this->post('/compensation', [
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-06',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'tax_rate' => '5',
            'gross_compensation' => '30000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
        ])->assertRedirect('/forms/map');

        $record = CompensationRecord::query()->firstOrFail();

        $this->put(route('compensation.update', $record), [
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-06',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'tax_rate' => '4',
            'gross_compensation' => '30000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
        ])->assertRedirect('/forms/map');

        $this->delete(route('compensation.destroy', $record))->assertRedirect('/compensation');

        foreach (['created', 'updated'] as $action) {
            $this->assertDatabaseHas('audit_logs', [
                'user_id' => $user->id,
                'model_type' => Employee::class,
                'action' => $action,
            ]);
        }
        foreach (['created', 'updated', 'deleted'] as $action) {
            $this->assertDatabaseHas('audit_logs', [
                'user_id' => $user->id,
                'model_type' => CompensationRecord::class,
                'action' => $action,
            ]);
        }
    }

    public function test_reports_can_filter_and_display_canonical_tax_form_labels(): void
    {
        $this->actingAs(User::factory()->create());

        $annualEmployee = Employee::create([
            'tin' => '888-888-888-001',
            'first_name' => 'Annual',
            'last_name' => 'Employee',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);
        $qapEmployee = Employee::create([
            'tin' => '888-888-888-002',
            'first_name' => 'Expanded',
            'last_name' => 'Employee',
            'employment_status' => 'Regular',
            'monthly_salary' => 30000,
        ]);

        foreach ([[$annualEmployee, 'Annual', '2026-01'], [$qapEmployee, 'QAP', '2026-02']] as [$employee, $taxForm, $payPeriod]) {
            CompensationRecord::create([
                'employee_id' => $employee->id,
                'tax_year' => 2026,
                'pay_period' => $payPeriod,
                'tax_form' => $taxForm,
                'gross_compensation' => 1000,
                'taxable_compensation' => 1000,
                'tax_withheld' => 50,
            ]);
        }

        $response = $this->get('/reports?tax_form=Annual');

        $response->assertOk();
        $response->assertSee('1604-CF');
        $response->assertSee('Annual Employee');
        $response->assertDontSee('Expanded Employee');
    }

    public function test_duplicate_compensation_record_per_employee_period_and_form_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '555-555-555-555',
            'first_name' => 'Evelyn',
            'last_name' => 'Stone',
            'employment_status' => 'Regular',
            'monthly_salary' => 60000,
        ]);

        CompensationRecord::create([
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-11',
            'tax_form' => 'MAP',
            'gross_compensation' => 1000,
            'taxable_compensation' => 1000,
            'tax_withheld' => 100,
        ]);

        $response = $this->post('/compensation', [
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-11',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'tax_rate' => '5',
            'gross_compensation' => '2000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
            'tax_withheld' => '200',
        ]);

        $response->assertSessionHasErrors('pay_period');
        $this->assertDatabaseCount('compensation_records', 1);
    }

    public function test_employee_name_is_title_cased_and_middle_name_is_saved(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->actingAs(User::factory()->create());

        $response = $this->post('/employees', [
            'tin' => '111-222-333-444',
            'first_name' => 'ralph',
            'middle_name' => 'maria',
            'last_name' => 'escosio',
            'employment_status' => 'Regular',
            'monthly_salary' => '35000',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('employees', [
            'first_name' => 'Ralph',
            'middle_name' => 'Maria',
            'last_name' => 'Escosio',
        ]);

        $employee = Employee::first();
        $this->assertSame('Ralph Maria Escosio', $employee->full_name);
    }

    public function test_admin_routes_are_not_available(): void
    {
        foreach ([
            '/admin',
            '/admin/users',
            '/admin/settings',
            '/admin/audit-logs',
            '/admin/data-management',
        ] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    public function test_user_is_redirected_to_tax_form_selection_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'payroll@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/forms/choose');
    }

    public function test_compensation_record_redirects_back_to_selected_form(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/employees', [
            'tin' => '123-456-789-002',
            'first_name' => 'Pedro',
            'last_name' => 'Ramos',
            'employment_status' => 'Regular',
            'monthly_salary' => '35000',
        ]);

        $response = $this->post('/compensation', [
            'employee_id' => 1,
            'tax_year' => '2026',
            'pay_period' => '2026-09',
            'tax_form' => 'QAP',
            'atc_code' => 'A010',
            'tax_rate' => '0',
            'gross_compensation' => '25000',
            'non_taxable_compensation' => '0',
            'government_contributions' => '0',
            'tax_withheld' => '0',
            'notes' => 'QAP test',
        ]);

        $response->assertRedirect('/forms/1604-e');
    }

    public function test_pdf_export_returns_valid_pdf_document(): void
    {
        $this->actingAs(User::factory()->create());

        $employee = Employee::create([
            'tin' => '777-888-999-000',
            'first_name' => 'Rosa',
            'last_name' => 'Paz',
            'employment_status' => 'Regular',
            'monthly_salary' => 40000,
        ]);

        CompensationRecord::create([
            'employee_id' => $employee->id,
            'tax_year' => 2026,
            'pay_period' => '2026-09',
            'tax_form' => 'MAP',
            'atc_code' => 'A010',
            'gross_compensation' => 25000,
            'taxable_compensation' => 25000,
            'tax_withheld' => 1200,
            'status' => 'approved',
        ]);

        $response = $this->get('/reports/export-pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_create_routes_are_not_shadowed_by_detail_routes(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/employees/create')->assertStatus(200);
        $this->get('/compensation/create')->assertStatus(200);
    }

    public function test_registration_requires_sign_in_before_accessing_modules(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'selfadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Your account has been created. Please sign in.');
        $this->assertDatabaseHas('users', [
            'email' => 'selfadmin@example.com',
        ]);
        $this->assertGuest();
        $this->get('/forms/choose')->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'selfadmin@example.com',
            'password' => 'password123',
        ])->assertRedirect('/forms/choose');

        $this->get('/admin')->assertNotFound();
    }

    public function test_user_can_request_and_complete_password_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'resetme@example.com',
            'password' => bcrypt('old-password-123'),
        ]);

        $requestResponse = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $requestResponse->assertRedirect();

        $token = Password::createToken($user);

        $resetResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $resetResponse->assertRedirect('/login');
        $this->assertTrue(
            auth()->attempt(['email' => $user->email, 'password' => 'new-password-456'])
        );
    }

    public function test_employee_can_be_created(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/employees', [
            'tin' => '123-456-789-000',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'employment_status' => 'Regular',
            'monthly_salary' => '45000',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('employees', [
            'tin' => '123-456-789-000',
            'first_name' => 'Juan',
        ]);
    }

    public function test_compensation_record_requires_valid_amounts(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/employees', [
            'tin' => '123-456-789-001',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'employment_status' => 'Regular',
            'monthly_salary' => '35000',
        ]);

        $response = $this->post('/compensation-records', [
            'employee_tin' => '123-456-789-001',
            'pay_period' => '2026-09',
            'tax_rate' => '5',
            'gross_compensation' => '25000',
            'tax_withheld' => '-100',
            'non_taxable_compensation' => '1000',
            'government_contributions' => '3000',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('compensation_records', [
            'taxable_compensation' => '21000.00',
            'tax_withheld' => '1050.00',
        ]);
    }
}
