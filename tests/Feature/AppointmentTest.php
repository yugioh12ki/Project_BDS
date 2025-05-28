<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Property;
use Carbon\Carbon;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_view_appointments()
    {
        $agent = User::factory()->create(['Role' => 'Agent']);
        
        $response = $this->actingAs($agent)
                         ->get('/agent/appointments');

        $response->assertStatus(200);
    }

    public function test_agent_can_update_appointment_status()
    {
        $agent = User::factory()->create(['Role' => 'Agent']);
        $owner = User::factory()->create(['Role' => 'Owner']);
        $customer = User::factory()->create(['Role' => 'Customer']);
        
        $property = Property::factory()->create(['OwnerID' => $owner->UserID, 'AgentID' => $agent->UserID]);
        
        $appointment = Appointment::create([
            'PropertyID' => $property->PropertyID,
            'AgentID' => $agent->UserID,
            'CusID' => $customer->UserID,
            'OwnerID' => $owner->UserID,
            'TitleAppoint' => 'Test Appointment',
            'DescAppoint' => 'Test Description',
            'AppointmentDateStart' => Carbon::now()->addDay(),
            'AppointmentDateEnd' => Carbon::now()->addDay()->addHour(),
            'Status' => Appointment::STATUS_PENDING
        ]);

        $response = $this->actingAs($agent)
                         ->put("/agent/appointments/{$appointment->AppointmentID}/status", [
                             'status' => Appointment::STATUS_CONFIRMED
                         ]);

        $response->assertRedirect();
        $this->assertEquals(Appointment::STATUS_CONFIRMED, $appointment->fresh()->Status);
    }

    public function test_appointment_status_constants_are_correct()
    {
        $this->assertEquals('Chờ xử lý', Appointment::STATUS_PENDING);
        $this->assertEquals('Thành công', Appointment::STATUS_CONFIRMED);
        $this->assertEquals('Đã hủy', Appointment::STATUS_CANCELLED);
        $this->assertEquals('Hoàn thành', Appointment::STATUS_COMPLETED);
    }

    public function test_appointment_scopes_work_correctly()
    {
        $agent = User::factory()->create(['Role' => 'Agent']);
        $owner = User::factory()->create(['Role' => 'Owner']);
        $customer = User::factory()->create(['Role' => 'Customer']);
        $property = Property::factory()->create(['OwnerID' => $owner->UserID, 'AgentID' => $agent->UserID]);

        // Create appointments with different statuses
        $pendingAppointment = Appointment::create([
            'PropertyID' => $property->PropertyID,
            'AgentID' => $agent->UserID,
            'CusID' => $customer->UserID,
            'OwnerID' => $owner->UserID,
            'TitleAppoint' => 'Pending Appointment',
            'DescAppoint' => 'Test Description',
            'AppointmentDateStart' => Carbon::now()->addDay(),
            'AppointmentDateEnd' => Carbon::now()->addDay()->addHour(),
            'Status' => Appointment::STATUS_PENDING
        ]);

        $completedAppointment = Appointment::create([
            'PropertyID' => $property->PropertyID,
            'AgentID' => $agent->UserID,
            'CusID' => $customer->UserID,
            'OwnerID' => $owner->UserID,
            'TitleAppoint' => 'Completed Appointment',
            'DescAppoint' => 'Test Description',
            'AppointmentDateStart' => Carbon::now()->addDay(),
            'AppointmentDateEnd' => Carbon::now()->addDay()->addHour(),
            'Status' => Appointment::STATUS_COMPLETED
        ]);

        // Test scopes
        $this->assertEquals(1, Appointment::pending()->count());
        $this->assertEquals(1, Appointment::completed()->count());
        $this->assertEquals(0, Appointment::cancelled()->count());
    }
}
