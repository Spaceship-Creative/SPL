<?php

namespace Database\Factories;

use App\Models\CaseDocument;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseDocument>
 */
class CaseDocumentFactory extends Factory
{
    protected $model = CaseDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['complaint', 'motion', 'order', 'evidence', 'pleading', 'discovery', 'exhibit', 'correspondence'];
        $processingStatuses = ['pending', 'processing', 'completed', 'failed'];
        $fileExtensions = ['pdf', 'docx', 'doc', 'txt'];
        
        $category = $this->faker->randomElement($categories);
        $extension = $this->faker->randomElement($fileExtensions);
        $fileName = $this->generateFileName($category, $extension);

        return [
            'case_id' => LegalCase::factory(),
            'name' => $this->generateDocumentName($category),
            'file_name' => $fileName,
            'file_path' => 'case-documents/' . $this->faker->uuid() . '/' . $fileName,
            'file_size' => $this->faker->numberBetween(50000, 5000000), // 50KB to 5MB
            'mime_type' => $this->getMimeType($extension),
            'category' => $category,
            'tags' => $this->generateTags($category),
            'version' => $this->faker->numberBetween(1, 3),
            'processing_status' => $this->faker->randomElement($processingStatuses),
            'ai_processed_at' => $this->faker->optional(0.6)->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Generate a realistic document name based on category.
     */
    private function generateDocumentName(string $category): string
    {
        $documentNames = [
            'complaint' => [
                'Initial Complaint',
                'Amended Complaint',
                'Third-Party Complaint',
                'Class Action Complaint'
            ],
            'motion' => [
                'Motion to Dismiss',
                'Motion for Summary Judgment',
                'Motion to Compel Discovery',
                'Motion for Preliminary Injunction',
                'Motion to Strike'
            ],
            'order' => [
                'Temporary Restraining Order',
                'Scheduling Order',
                'Discovery Order',
                'Final Judgment Order',
                'Order Granting Motion'
            ],
            'evidence' => [
                'Security Camera Footage',
                'Financial Records',
                'Medical Records',
                'Email Correspondence',
                'Contract Documents'
            ],
            'pleading' => [
                'Answer to Complaint',
                'Cross-Claim',
                'Counter-Claim',
                'Reply Brief'
            ],
            'discovery' => [
                'Request for Production',
                'Interrogatories',
                'Deposition Transcript',
                'Request for Admissions'
            ],
            'exhibit' => [
                'Exhibit A - Contract',
                'Exhibit B - Photographs',
                'Exhibit C - Financial Statement',
                'Exhibit D - Expert Report'
            ],
            'correspondence' => [
                'Attorney Letter',
                'Settlement Offer',
                'Client Communication',
                'Court Correspondence'
            ]
        ];

        $names = $documentNames[$category] ?? ['Legal Document'];
        return $this->faker->randomElement($names);
    }

    /**
     * Generate a realistic file name.
     */
    private function generateFileName(string $category, string $extension): string
    {
        $baseNames = [
            'complaint' => 'complaint',
            'motion' => 'motion',
            'order' => 'order',
            'evidence' => 'evidence',
            'pleading' => 'pleading',
            'discovery' => 'discovery',
            'exhibit' => 'exhibit',
            'correspondence' => 'letter'
        ];

        $baseName = $baseNames[$category] ?? 'document';
        $timestamp = $this->faker->date('Y-m-d');
        
        return "{$baseName}_{$timestamp}_{$this->faker->randomNumber(4)}.{$extension}";
    }

    /**
     * Get MIME type based on file extension.
     */
    private function getMimeType(string $extension): string
    {
        return match($extension) {
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'txt' => 'text/plain',
            default => 'application/octet-stream',
        };
    }

    /**
     * Generate realistic tags based on document category.
     */
    private function generateTags(string $category): ?array
    {
        $commonTags = ['urgent', 'reviewed', 'draft', 'final', 'confidential'];
        $categoryTags = [
            'complaint' => ['filing', 'initial-pleading', 'damages'],
            'motion' => ['motion-practice', 'procedural', 'substantive'],
            'order' => ['court-order', 'ruling', 'decision'],
            'evidence' => ['proof', 'documentation', 'exhibits'],
            'pleading' => ['responsive-pleading', 'answer', 'defense'],
            'discovery' => ['fact-finding', 'interrogatories', 'production'],
            'exhibit' => ['attachment', 'supporting-doc', 'reference'],
            'correspondence' => ['communication', 'letter', 'external']
        ];

        if ($this->faker->boolean(0.7)) {
            $tags = $this->faker->randomElements($commonTags, $this->faker->numberBetween(1, 2));
            if (isset($categoryTags[$category])) {
                $tags = array_merge($tags, $this->faker->randomElements($categoryTags[$category], 1));
            }
            return array_unique($tags);
        }

        return null;
    }

    /**
     * Indicate that the document is pending processing.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => 'pending',
            'ai_processed_at' => null,
        ]);
    }

    /**
     * Indicate that the document has been processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => 'completed',
            'ai_processed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the document is of a specific category.
     */
    public function ofCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
            'name' => $this->generateDocumentName($category),
        ]);
    }

    /**
     * Indicate that the document belongs to a specific case.
     */
    public function forCase(LegalCase $case): static
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $case->id,
        ]);
    }

    /**
     * Indicate that the document is a PDF.
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_name' => str_replace(pathinfo($attributes['file_name'], PATHINFO_EXTENSION), 'pdf', $attributes['file_name']),
            'mime_type' => 'application/pdf',
        ]);
    }
}
