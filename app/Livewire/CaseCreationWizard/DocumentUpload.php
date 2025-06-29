<?php

namespace App\Livewire\CaseCreationWizard;

use Livewire\Component;
use Livewire\Attributes\Validate;

class DocumentUpload extends Component
{
    // User type properties
    public $userType = 'pro_se';
    public $isLegalProfessional = false;

    // Document placeholders array (preparing for future file upload functionality)
    public $documents = [];

    // Form fields for adding document placeholder
    #[Validate('required|string|min:3|max:255', message: 'Please enter a document title.')]
    public $title = '';

    #[Validate('required|string', message: 'Please select a document type.')]
    public $type = '';

    #[Validate('required|string', message: 'Please select a document category.')]
    public $category = '';

    #[Validate('nullable|string|max:500')]
    public $description = '';

    #[Validate('nullable|date')]
    public $receivedDate = '';

    #[Validate('nullable|date')]
    public $dueDate = '';

    // Document type options - conditional based on user type
    public function getDocumentTypesProperty()
    {
        if ($this->isLegalProfessional) {
            // Comprehensive list for legal professionals
            return [
                'pleading' => 'Pleading',
                'motion' => 'Motion',
                'discovery' => 'Discovery Document',
                'evidence' => 'Evidence',
                'correspondence' => 'Correspondence',
                'court_order' => 'Court Order',
                'transcript' => 'Transcript',
                'expert_report' => 'Expert Report',
                'medical_record' => 'Medical Record',
                'financial_record' => 'Financial Record',
                'contract' => 'Contract/Agreement',
                'insurance_document' => 'Insurance Document',
                'government_document' => 'Government Document',
                'witness_statement' => 'Witness Statement',
                'photograph' => 'Photograph/Image',
                'other' => 'Other'
            ];
        } else {
            // Simplified list for pro-se users with explanations
            return [
                'complaint' => 'Complaint/Petition (The document that starts your case)',
                'response' => 'Response/Answer (Reply to the other party)',
                'evidence' => 'Evidence (Photos, receipts, contracts, etc.)',
                'correspondence' => 'Letters/Emails (Communication about your case)',
                'court_document' => 'Court Document (Orders, notices from court)',
                'financial_document' => 'Financial Document (Bills, receipts, bank statements)',
                'medical_document' => 'Medical Document (If applicable to your case)',
                'witness_statement' => 'Witness Statement (Written statements from witnesses)',
                'photograph' => 'Photos/Images (Pictures relevant to your case)',
                'other' => 'Other Important Document'
            ];
        }
    }

    // Document category options
    public function getDocumentCategoriesProperty()
    {
        if ($this->isLegalProfessional) {
            return [
                'filed_with_court' => 'Filed with Court',
                'to_be_filed' => 'To Be Filed',
                'received_from_opposing' => 'Received from Opposing Party',
                'internal_work_product' => 'Internal Work Product',
                'evidence_exhibits' => 'Evidence/Exhibits',
                'research_reference' => 'Research/Reference',
                'correspondence' => 'Correspondence',
                'discovery_materials' => 'Discovery Materials'
            ];
        } else {
            return [
                'my_documents' => 'My Documents (I created/have)',
                'court_documents' => 'From the Court (Official court papers)',
                'other_party_documents' => 'From Other Party (They sent me)',
                'evidence' => 'Evidence (Proof for my case)',
                'to_file' => 'Need to File (Will submit to court)',
                'reference' => 'Reference (For my information)'
            ];
        }
    }

    public function mount($userType = 'pro_se', $isLegalProfessional = false)
    {
        $this->userType = $userType;
        $this->isLegalProfessional = $isLegalProfessional;
    }

    public function addDocumentPlaceholder()
    {
        // Validate the form
        $this->validate([
            'title' => 'required|string|min:3|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'description' => 'nullable|string|max:500',
            'receivedDate' => 'nullable|date',
            'dueDate' => 'nullable|date|after_or_equal:today'
        ], [], [
            'title' => 'document title',
            'type' => 'document type',
            'category' => 'document category',
            'description' => 'description',
            'receivedDate' => 'received date',
            'dueDate' => 'due date'
        ]);

        // Add to documents array as placeholder
        $newDocument = [
            'id' => uniqid(),
            'title' => $this->title,
            'type' => $this->type,
            'category' => $this->category,
            'description' => $this->description,
            'received_date' => $this->receivedDate,
            'due_date' => $this->dueDate,
            'status' => 'placeholder', // Indicates this is a placeholder for future upload
            'created_at' => now()->toDateTimeString()
        ];

        $this->documents[] = $newDocument;

        // Reset form
        $this->reset(['title', 'type', 'category', 'description', 'receivedDate', 'dueDate']);

        // Show success message
        session()->flash('document-success', 'Document placeholder added successfully! You can upload the actual file later.');

        // Emit to parent
        $this->dispatch('documents-updated', $this->documents);
    }

    public function removeDocumentPlaceholder($documentId)
    {
        $this->documents = collect($this->documents)->reject(function ($document) use ($documentId) {
            return $document['id'] === $documentId;
        })->values()->toArray();

        session()->flash('document-success', 'Document placeholder removed successfully.');
        $this->dispatch('documents-updated', $this->documents);
    }

    public function validateDocuments()
    {
        // Documents are optional for all user types
        return true;
    }

    public function getDocumentDisplayType($type)
    {
        $types = $this->documentTypes;
        return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public function getDocumentDisplayCategory($category)
    {
        $categories = $this->documentCategories;
        return $categories[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    public function formatDate($date)
    {
        try {
            return \Carbon\Carbon::parse($date)->format('M j, Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function render()
    {
        return view('livewire.case-creation-wizard.document-upload', [
            'documentTypes' => $this->documentTypes,
            'documentCategories' => $this->documentCategories
        ]);
    }
}
