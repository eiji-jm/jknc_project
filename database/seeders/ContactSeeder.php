<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $contacts = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'company_name' => 'ABC Corp',
                'email' => 'juan@gmail.com',
                'phone' => '0923445352',
                'kyc_status' => 'Verified',
                'owner_name' => 'JohnAdmin',
                'last_activity_at' => $now->copy()->subHour(),
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'company_name' => 'Tech Solutions Inc',
                'email' => 'maria.santos@techsolutions.com',
                'phone' => '09171234567',
                'kyc_status' => 'Pending Verification',
                'owner_name' => 'AdminUser',
                'last_activity_at' => $now->copy()->subHour(),
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'company_name' => 'Global Enterprises',
                'email' => 'robert.j@global.com',
                'phone' => '09281234567',
                'kyc_status' => 'Verified',
                'owner_name' => 'JohnAdmin',
                'last_activity_at' => $now->copy()->subDays(5),
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'company_name' => 'Innovate Co',
                'email' => 's.williams@innovate.co',
                'phone' => '09191234567',
                'kyc_status' => 'Not Submitted',
                'owner_name' => 'JohnAdmin',
                'last_activity_at' => $now->copy()->subHours(3),
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'company_name' => 'Startup Hub',
                'email' => 'mbrown@startuphub.com',
                'phone' => '09351234567',
                'kyc_status' => 'Rejected',
                'owner_name' => 'AdminUser',
                'last_activity_at' => $now->copy()->subWeek(),
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Garcia',
                'company_name' => 'Marketing Plus',
                'email' => 'lisa@marketingplus.com',
                'phone' => '09221234567',
                'kyc_status' => 'Verified',
                'owner_name' => 'JohnAdmin',
                'last_activity_at' => $now->copy()->subDay(),
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Lee',
                'company_name' => 'Consulting Group',
                'email' => 'david.lee@consulting.com',
                'phone' => '09331234567',
                'kyc_status' => 'Pending Verification',
                'owner_name' => 'AdminUser',
                'last_activity_at' => $now->copy()->subDays(4),
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Davis',
                'company_name' => 'E-commerce Solutions',
                'email' => 'emma@ecommerce.com',
                'phone' => '09441234567',
                'kyc_status' => 'Not Submitted',
                'owner_name' => 'JohnAdmin',
                'last_activity_at' => $now->copy()->subWeeks(2),
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::updateOrCreate(
                ['email' => $contact['email']],
                array_merge($contact, [
                    'lead_source' => null,
                    'description' => null,
                ])
            );
        }
    }
}
