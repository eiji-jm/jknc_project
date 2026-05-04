<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        $catalog = $this->defaultProductCatalog();
        $defaultOwnerId = DB::table('users')->orderBy('id')->value('id');
        $defaultOwnerName = DB::table('users')->orderBy('id')->value('name') ?: 'System Seed';
        $timestamp = Carbon::now();

        foreach ($catalog as $serviceArea => $productNames) {
            foreach ($productNames as $productName) {
                $existingProduct = DB::table('products')
                    ->where('product_name', $productName)
                    ->first();

                if ($existingProduct) {
                    $areas = collect(json_decode((string) ($existingProduct->product_area ?? '[]'), true))
                        ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                        ->map(fn ($value) => trim((string) $value))
                        ->push($serviceArea)
                        ->unique()
                        ->values()
                        ->all();

                    DB::table('products')
                        ->where('id', $existingProduct->id)
                        ->update([
                            'product_area' => json_encode($areas),
                            'updated_at' => $timestamp,
                        ]);

                    continue;
                }

                $insert = [
                    'product_id' => $this->nextProductId(),
                    'product_name' => $productName,
                    'product_type' => 'Service',
                    'linked_service_id' => null,
                    'product_area' => json_encode([$serviceArea]),
                    'product_area_other' => null,
                    'product_description' => $productName,
                    'product_inclusions' => null,
                    'category' => $this->defaultCategoryForServiceArea($serviceArea),
                    'pricing_type' => 'Fixed',
                    'price' => 350,
                    'cost' => null,
                    'is_discountable' => false,
                    'tax_type' => 'VAT',
                    'sku' => $this->nextSku(),
                    'inventory_type' => 'Service',
                    'stock_qty' => null,
                    'unit' => 'Project',
                    'status' => 'Active',
                    'owner_id' => $defaultOwnerId,
                    'created_by' => $defaultOwnerName,
                    'reviewed_by' => $defaultOwnerName,
                    'reviewed_at' => $timestamp,
                    'approved_by' => $defaultOwnerName,
                    'approved_at' => $timestamp,
                    'custom_field_values' => json_encode([]),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                if (Schema::hasColumn('products', 'linked_service_ids')) {
                    $insert['linked_service_ids'] = json_encode([]);
                }

                if (Schema::hasColumn('products', 'tax_treatment')) {
                    $insert['tax_treatment'] = 'Tax Exclusive';
                }

                DB::table('products')->insert($insert);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::table('products')
            ->whereIn('product_name', collect($this->defaultProductCatalog())->flatten()->unique()->values()->all())
            ->where('created_by', 'System Seed')
            ->delete();
    }

    private function defaultProductCatalog(): array
    {
        return [
            'Corporate & Regulatory Advisory' => [
                'Printing',
                'Photocopy',
                'Drafting of Letters',
                'Drafting of Notices',
                'Drafting of Demand Letters',
                'Drafting of Emails (Formal / Business)',
            ],
            'Accounting & Compliance Advisory' => [
                'Archive Retrieval',
                'Digital Archive Copy',
                'Drafting of Responses to Letters / Notices',
                'Drafting of Memorandum (Internal / External)',
                'Drafting of Certifications',
                'Drafting of Compliance Documents',
            ],
            'Governance & Policy Advisory' => [
                'Document Delivery (Metro Cebu)',
                'Document Delivery (Outside Metro Cebu/LBC)',
                'Drafting of Affidavits (Non-Legal Advice)',
                'Drafting of Agreements / Simple Contracts',
                'Drafting of Board Resolutions',
                'Drafting of Endorsement / Request Letters',
            ],
            'Business Strategy & Process Advisory' => [
                'Notarization - Simple Documents',
                'Notarization - Complex Documents',
                "Drafting of Secretary's Certificates",
                'Drafting of Policies & Procedures',
                'Drafting of Reports / Formal Documents',
            ],
            'Strategic Situations Advisory' => [
                'Printing',
                'Photocopy',
                'Drafting of Letters',
                'Drafting of Notices',
                'Drafting of Demand Letters',
                'Drafting of Emails (Formal / Business)',
            ],
            'People & Talent Solutions' => [
                'Archive Retrieval',
                'Digital Archive Copy',
                'Drafting of Responses to Letters / Notices',
                'Drafting of Memorandum (Internal / External)',
                'Drafting of Certifications',
                'Drafting of Compliance Documents',
            ],
            'Learning & Capability Development' => [
                'Document Delivery (Metro Cebu)',
                'Document Delivery (Outside Metro Cebu/LBC)',
                'Drafting of Affidavits (Non-Legal Advice)',
                'Drafting of Agreements / Simple Contracts',
                'Drafting of Board Resolutions',
                'Drafting of Endorsement / Request Letters',
            ],
        ];
    }

    private function defaultCategoryForServiceArea(string $serviceArea): string
    {
        return match ($serviceArea) {
            'Corporate & Regulatory Advisory' => 'Corporate Services',
            'Accounting & Compliance Advisory' => 'Accounting Services',
            'Governance & Policy Advisory' => 'Professional Fees',
            'Business Strategy & Process Advisory' => 'Consulting Revenue',
            'Strategic Situations Advisory' => 'Professional Fees',
            'People & Talent Solutions' => 'HR Services',
            'Learning & Capability Development' => 'Training & Development',
            default => 'Other Income',
        };
    }

    private function nextProductId(): string
    {
        do {
            $productId = (string) random_int(10000, 99999);
        } while (DB::table('products')->where('product_id', $productId)->exists());

        return $productId;
    }

    private function nextSku(): string
    {
        $prefix = 'PRD-REG-';
        $latestSku = DB::table('products')
            ->where('sku', 'like', $prefix.'%')
            ->whereNotNull('sku')
            ->orderByDesc('sku')
            ->value('sku');

        if (! is_string($latestSku) || preg_match('/^PRD-REG-(\d{3})$/', $latestSku, $matches) !== 1) {
            return $prefix.'001';
        }

        return $prefix.str_pad((string) (((int) $matches[1]) + 1), 3, '0', STR_PAD_LEFT);
    }
};
