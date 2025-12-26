<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Facility;
use App\Models\Hospitalization;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Facilities
        $fac1 = Facility::firstOrCreate([
            'name' => 'NeuroCare Clinic',
        ], [
            'type' => 'clinic',
            'city' => 'Casablanca',
            'address' => '123 Brain St',
            'description' => 'Outpatient neurology services including EEG and seizure monitoring.'
        ]);
        $fac2 = Facility::firstOrCreate([
            'name' => 'General Hospital',
        ], [
            'type' => 'hospital',
            'city' => 'Rabat',
            'address' => '45 Health Ave',
            'description' => 'Full-service hospital with neurology department.'
        ]);
        $fac3 = Facility::firstOrCreate([
            'name' => 'Seizure Center',
        ], [
            'type' => 'cabinet',
            'city' => 'Marrakesh',
            'address' => '78 Calm Blvd',
            'description' => 'Specialized epilepsy and seizure care.'
        ]);

        // Doctors
        $dr1 = User::firstOrCreate([
            'email' => 'dr.house@example.com',
        ], [
            'name' => 'Dr. House',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'specialty' => 'Neurology',
            'bio' => 'Experienced neurologist focusing on seizure disorders and complex diagnostics.',
            'case_categories' => 'epilepsy,EEG,neurodiagnostics',
            'phone' => '+212600000001',
            'address' => '221B Baker Street',
        ]);
        $dr2 = User::firstOrCreate([
            'email' => 'dr.brain@example.com',
        ], [
            'name' => 'Dr. Brain',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'specialty' => 'Clinical Neurophysiology',
            'bio' => 'Clinical neurophysiologist with interest in long-term EEG monitoring.',
            'case_categories' => 'LTM,epilepsy,pediatrics',
            'phone' => '+212600000002',
            'address' => '742 Evergreen Terrace',
        ]);

        $dr1->facilities()->syncWithoutDetaching([$fac1->id, $fac2->id]);
        $dr2->facilities()->syncWithoutDetaching([$fac2->id, $fac3->id]);

        // Admin
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Site Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Patients
        $p1 = User::firstOrCreate([
            'email' => 'alice.patient@example.com',
        ], [
            'name' => 'Alice Patient',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'doctor_id' => $dr1->id,
            'phone' => '+212700000001',
            'address' => '12 Ocean Road',
        ]);
        $p2 = User::firstOrCreate([
            'email' => 'bob.patient@example.com',
        ], [
            'name' => 'Bob Patient',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'doctor_id' => $dr1->id,
            'phone' => '+212700000002',
            'address' => '34 Desert Lane',
        ]);
        $p3 = User::firstOrCreate([
            'email' => 'charlie.patient@example.com',
        ], [
            'name' => 'Charlie Patient',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'doctor_id' => $dr2->id,
            'phone' => '+212700000003',
            'address' => '56 Mountain Path',
        ]);

        // Hospitalizations
        $this->seedHospitalization($p1->id, $fac1->id, 'Initial evaluation', 'Admitted for seizure characterization and EEG.', '-10 days', '-7 days');
        $this->seedHospitalization($p1->id, $fac2->id, 'Medication adjustment', 'Optimizing antiepileptic drugs, monitoring side effects.', '-3 days', null);
        $this->seedHospitalization($p2->id, $fac2->id, 'Post-ictal workup', 'CT/MRI and labs following breakthrough seizure.', '-20 days', '-19 days');
        $this->seedHospitalization($p3->id, $fac3->id, 'Follow-up visit', 'Outpatient follow-up for seizure diary review.', '-5 days', null);
    }

    private function seedHospitalization(int $patientId, int $facilityId, string $title, string $desc, ?string $startRel, ?string $endRel): void
    {
        $start = $startRel ? Carbon::parse($startRel) : Carbon::now();
        $end = $endRel ? Carbon::parse($endRel) : null;
        Hospitalization::create([
            'patient_id' => $patientId,
            'facility_id' => $facilityId,
            'title' => $title,
            'description' => $desc,
            'start_date' => $start->toDateString(),
            'end_date' => $end?->toDateString(),
        ]);
    }
}
