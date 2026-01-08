<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerMessage;
use Carbon\Carbon;

class CustomerMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            [
                'message_id' => 'MSG-001',
                'customer_id' => null,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'subject' => 'Billing Issue - Invoice #12345',
                'message' => 'I have a question about my recent invoice. The amount seems incorrect and I would like clarification on the charges.',
                'category' => 'billing',
                'status' => 'open',
                'priority' => 'normal',
                'department' => 'billing',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'message_id' => 'MSG-002',
                'customer_id' => null,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'subject' => 'Product Support Request',
                'message' => 'I need help setting up my new product. The installation guide is not clear and I am having trouble with the configuration.',
                'category' => 'technical',
                'status' => 'in_progress',
                'priority' => 'high',
                'department' => 'technical',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'message_id' => 'MSG-003',
                'customer_id' => null,
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'subject' => 'Sales Inquiry - Enterprise Plan',
                'message' => 'I am interested in upgrading to your enterprise plan. Can you provide more details about the features and pricing?',
                'category' => 'sales',
                'status' => 'resolved',
                'priority' => 'normal',
                'department' => 'sales',
                'admin_reply' => 'Thank you for your interest! I have sent you detailed information about our enterprise plan to your email.',
                'replied_at' => Carbon::now()->subHours(2),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'message_id' => 'MSG-004',
                'customer_id' => null,
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'subject' => 'Account Access Problem',
                'message' => 'I cannot log into my account. I have tried resetting my password but I am not receiving the reset email.',
                'category' => 'technical',
                'status' => 'open',
                'priority' => 'urgent',
                'department' => 'technical',
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(4),
            ],
            [
                'message_id' => 'MSG-005',
                'customer_id' => null,
                'name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'subject' => 'Feature Request',
                'message' => 'It would be great if you could add a dark mode option to the dashboard. Many users would appreciate this feature.',
                'category' => 'feedback',
                'status' => 'open',
                'priority' => 'low',
                'department' => 'product',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ]
        ];

        foreach ($messages as $message) {
            CustomerMessage::create($message);
        }
    }
}