<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealProposal;
use App\Models\Company;
use App\Services\DealProposalTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Throwable;

class DealProposalController extends Controller
{
    public function __construct(private readonly DealProposalTemplateService $templateService)
    {
    }

    private const COMPANY_PHONE = '0995-535-8729';
    private const COMPANY_EMAIL = 'start@jknc.io';
    private const COMPANY_WEBSITE = 'jknc.io';
    private const COMPANY_ADDRESS = '3F, Cebu Holdings Center, Cebu Business Park, Cebu City, Philippines 6000.';

    private const EXECUTIVE_SUMMARY = [
        "John Kelly & Company is a management consulting and corporate advisory company that assists businesses in growing, improving how they operate, and making better decisions for the future. With over 30 years of combined experience across the public and private sectors, the company works closely with organizations to strengthen systems, improve management discipline, and support compliance and governance needs with clarity and professionalism. John Kelly & Company is supported by a multidisciplinary team with legal, financial, operational, and governance expertise, including Atty. Jose B. Ogang, CPA, MMPSM, former Mediator-Arbiter of the Department of Labor and Employment (DOLE); Jose Tomayo Rio, MM-BA, CPA, former Municipal Accountant of LGU Madridejos, Cebu; Lyndon Earl P. Rio, RN, CB, with extensive experience as an accountant and bookkeeper across various industries; and John Kelly Abalde, CLSSBB, CPM, a corporate secretary and board director serving organizations across multiple industries-working together to support clients in managing their businesses effectively and sustainably.",
        "Our vision is to build a world where people, systems, and ideas work together where management is disciplined, individuals are empowered, and progress is shared. By 2030, we aim to be a global example of how well-managed and forward-thinking businesses can create lasting, positive change.",
        "Our mission is simple: to build future-ready businesses today. We do this by helping leaders and teams become more organized, capable, and confident - creating strong foundations that make growth sustainable, innovation achievable, and success possible for everyone involved.",
    ];

    private const ROLE_AND_VALUE = [
        "As your trusted partner, John Kelly & Company is here to guide and support you in keeping your business well-managed, transparent, and compliant. We believe that good management is not just about following rules, it's about building trust, responsibility, and confidence in how a business is run.",
        'You can count on us to handle the details carefully while keeping communication open and simple. We work closely with you to make sure every step is clear and well-coordinated. Our goal is to help your business stay organized, compliant, and ready to grow with the assurance that everything is managed properly and with integrity.',
    ];

    private const WHY_PARTNER = [
        "At John Kelly & Company, we believe in helping businesses grow with clarity, honesty, and purpose. Over the years, we've worked closely with trusted mentors, lawyers, and certified public accountants who share our goal of helping businesses run better and stronger. Through these partnerships and our hands-on approach, we continue to guide companies toward steady growth and lasting success.",
        'Our range of services is designed to help businesses stay organized, compliant, and ready for growth through practical guidance, reliable support, and professional care which include the following areas of support.',
    ];

    private const SERVICE_AREAS = [
        [
            'no' => '1',
            'service_area' => 'Corporate & Regulatory Advisory',
            'scope' => [
                'Business Registration and Licensing (SEC, DTI, BIR, LGU)',
                'Corporate Secretary Outsourcing and Board Documentation',
                'Corporate Housekeeping (GIS, AFS, Resolutions, Minutes)',
                'Regulatory Compliance Review and Legal Due Diligence',
                'Ownership and Asset Transfers (Shares, Land Titles, Vehicles, Business Assets)',
                'DOLE Representation and Labor Compliance Support',
                'Government Accreditation and Permit Coordination (PEZA, BOI, DOST, CDA, DTI Export Bureau)',
                'Tax Incentives and Special Law Applications (CREATE Act and related fiscal incentives)',
                'Intellectual Property Registration (Trademark, Copyright, Patent - IPOPhil)',
                'Private and Government Grant Applications (DOST, DTI, NEDA, NICP, and related programs)',
                'Foreign Business Entry and Establishment Advisory',
            ],
        ],
        [
            'no' => '2',
            'service_area' => 'Accounting & Compliance Advisory',
            'scope' => [
                'Bookkeeping and General Accounting',
                'Financial Statement Preparation and Management Reporting',
                'Tax Preparation, Filing, and Advisory (VAT, Withholding, Income Tax)',
                'Payroll Processing and Statutory Remittances (SSS, PhilHealth, Pag-IBIG)',
                'Internal Audit Assistance and Financial Control Review',
                'Accounting System Setup and Implementation',
                'Audit Coordination and Compliance Documentation',
            ],
        ],
        [
            'no' => '3',
            'service_area' => 'Governance & Policy Advisory',
            'scope' => [
                'HR, Finance, Sales, and Administrative Policy Development',
                'Policy Audit and Internal Control Evaluation',
                'Corporate Governance Frameworks and Board or Committee Charters',
                'Risk and Compliance Manual Creation',
                'Organizational Structuring and Role Definition',
                'Business Process Optimization and Systems Efficiency Consulting',
                'Process Mapping and Workflow Standardization',
                'Governance Integration for Foreign-Linked Entities',
                'Policy Implementation and Compliance Monitoring',
            ],
        ],
        [
            'no' => '4',
            'service_area' => 'People & Talent Solutions',
            'scope' => [
                'Staffing for Accounting, HR, and Administrative Roles',
                'Executive Outsourcing (CFO-for-Hire, COO-for-Hire, Corporate Secretary-for-Hire)',
                'HR and Payroll Administration Services',
                'Managed Business Support Teams (Local and International)',
                'Remote Workforce Oversight and Productivity Reporting',
                'Cross-Border HR, Finance, and Administrative Outsourcing',
                'Data Privacy, Confidentiality, and Compliance Monitoring',
            ],
        ],
        [
            'no' => '5',
            'service_area' => 'Learning & Capability Development',
            'scope' => [
                'Lean Six Sigma Certification (White, Yellow, Green Belt)',
                'Corporate Governance and Revised Corporation Code Training',
                'Taxation, Payroll, and Compliance Workshops',
                'Leadership and Management Development Programs',
                'Business Process Improvement and Productivity Seminars',
            ],
        ],
    ];

    private const HIGHLIGHTS = [
        [
            'title' => 'Guided Support',
            'body' => "We're here to help your business stay on track by providing steady guidance and keeping things well-prepared and easy to follow.",
        ],
        [
            'title' => 'Personalized Approach',
            'body' => 'Every business is different, so we take the time to understand your needs and adjust our process to fit what works best for you.',
        ],
        [
            'title' => 'Open Communication',
            'body' => 'We keep you informed at all times so decisions are clear, updates are timely, and everyone moves forward together with confidence.',
        ],
        [
            'title' => 'Integrity and Care',
            'body' => 'We value honesty, respect, and responsibility in everything we do - ensuring that our work always reflects the trust you place in us.',
        ],
    ];

    private const COMMITMENT = [
        'Every business we work with deserves clarity, respect, and dependable support. That is why we make it our promise to handle every task with honesty, care, and attention to detail. We understand that behind every document and process are people working hard to build something meaningful and we aim to make their work easier, more organized, and more secure.',
        'Our approach is guided by consistency and genuine partnership. Over the years, we have worked hand in hand with trusted mentors, lawyers, and accountants who share our vision of helping businesses grow the right way - with discipline, transparency, and integrity at every step.',
    ];

    private const AGREEMENT_INCLUSIONS = [
        'Permit facilitation services performed strictly within the scope of this Agreement and solely for its implementation, including lawful coordination, submission, and follow-up with the government offices covered by and coordinated under this Agreement.',
        'Printing, photocopying, mailing, and preparation of basic documentation as may be reasonably required within the scope of this Agreement for its proper performance and execution in relation to the government offices coordinated with.',
        'Limited transportation (fuel) expenses necessarily and actually incurred within the scope of this Agreement, exclusively for permit-related acts undertaken pursuant hereto in connection with the government offices coordinated with, and confined to the applicable jurisdiction.',
    ];

    private const AGREEMENT_EXCLUSIONS = [
        'Transportation and accommodation expenses for off-site work.',
        'Government permit, license, and filing fees, including fees imposed by national and local government agencies.',
        'Notarization fees, penalties, surcharges, or late fees, if any.',
        'Costs of physical certificates, official forms, and certificate paper, when required.',
        'Third-party service fees, including courier services, document authentication, translation, printing, or similar incidental expenses.',
        'Rush processing fees or special handling requests required to meet accelerated timelines.',
        'Other incidental matters, such as document authentication, translation, rush requests, third-party service fees, etc., that may be required to complete a task or filing. Additional similar expenses not listed but reasonably necessary for task completion may also apply as agreed upon by both parties.',
    ];

    private const SUPPLEMENTAL_FEES = [
        ['service' => 'Printing of Client Document', 'description' => 'Printing of any client-provided document on A4, Legal, or Short Bond paper.', 'fee' => 'P5 per page'],
        ['service' => 'Archive Fee', 'description' => 'Fee for retrieving a document from archives and reissuing it to clients or stakeholders.', 'fee' => 'P50 per document'],
        ['service' => 'Delivery Fee (Metro Cebu)', 'description' => 'Delivery from JK&C office to a designated drop-off point within Metro Cebu.', 'fee' => 'P250 per drop-off'],
        ['service' => 'Photocopy (A4, Legal, Short Bond Paper)', 'description' => 'Photocopying of documents in A4, Legal, or Short Bond paper.', 'fee' => 'P5 per page'],
        ['service' => 'Digital Archive Copy', 'description' => 'Providing a digital copy of a document from the archive, sent to the client digitally.', 'fee' => 'P50 per document'],
        ['service' => 'Notarization of Simple Documents', 'description' => 'Notarization of simple documents, as defined by the Integrated Bar of the Philippines (IBP) - those that are single-party or routine in nature, not involving property rights or financial obligations.', 'fee' => 'P800 per document'],
        ['service' => 'Notarization of Complex Documents', 'description' => "Notarization of complex or non-simple documents, which involve property transfers, corporate acts, financial transactions, or multiple parties. Price varies depending on the lawyer's evaluation and complexity of verification required.", 'fee' => 'Subject to Evaluation'],
    ];

    private const TERMS_AND_CONDITIONS = [
        [
            'title' => 'Acceptance and Commencement',
            'items' => [
                "Work will commence upon the client's formal acceptance of this proposal and receipt of fifty percent (50%) of the agreed professional fee as an initial payment.",
                'The remaining fifty percent (50%) balance shall be due and payable upon completion of the agreed scope of work, prior to the release of final documents or deliverables, unless otherwise agreed in writing.',
            ],
        ],
        [
            'title' => "Client's Responsibilities",
            'intro' => 'To ensure a smooth and timely process, the client is expected to:',
            'items' => [
                'Provide accurate, complete, and timely information, documents, and approvals as may be required by John Kelly & Company and the relevant city or municipal government.',
                'Complete and submit all documents required by the city or municipality where the business operates, in accordance with applicable local regulations.',
                'Review, confirm, and provide feedback on drafts or documents prior to signing or submission.',
                'Notify John Kelly & Company in advance of any meetings, inspections, or activities that require our attendance, coordination, or documentation.',
                'Settle all agreed professional fees, government charges, and reimbursable expenses within three (3) calendar days from billing or written notice.',
            ],
        ],
        [
            'title' => "John Kelly & Company's Responsibilities",
            'intro' => 'In return, John Kelly & Company will:',
            'items' => [
                'Perform all agreed tasks with care, honesty, and professionalism, in accordance with the Scope of Service/Assistance outlined in this proposal.',
                "Review available documents and identify applicable requirements based on the client's business type and location.",
                'Prepare, organize, and assist in completing required forms and supporting documents within the agreed scope.',
                'Coordinate and follow up with the relevant government offices, as authorized by the client.',
                'Provide updates on progress and inform the client of material developments or requirements.',
                'Keep accurate, organized, and secure records of all documents prepared, submitted, or received in connection with the engagement.',
            ],
        ],
        [
            'title' => 'Client Compliance, Regulatory Delays, and Immediate Demandability',
            'paragraphs' => [
                'The client acknowledges that the processing, approval, or issuance of BIR registrations, filings, clearances, or related documents is subject to compliance with requirements imposed by the Bureau of Internal Revenue (BIR) and other relevant government agencies, as applicable. Such requirements may include, but are not limited to, the submission of prescribed forms, supporting documents, tax filings, payments, clarifications, or responses to official notices, as determined by the concerned authorities.',
                "John Kelly & Company's engagement is strictly limited to assistance, coordination, documentation preparation, submission, and follow-up within the scope of this proposal. John Kelly & Company does not guarantee approval, acceptance, processing timelines, assessments, or outcomes, all of which are determined solely by the BIR and other relevant government agencies.",
                "Any delay, suspension, or non-issuance of registrations, filings, or related documents arising from the client's failure, refusal, or inability to comply with requirements imposed by the BIR or other relevant government agencies-including incomplete submissions, unresolved findings, unpaid taxes or penalties, or failure to act on official directives-shall not be attributable to John Kelly & Company.",
                "Where the client fails or refuses to comply with applicable requirements, or otherwise prevents the completion of the process, John Kelly & Company shall be deemed to have performed its obligations to the extent permitted by the client's cooperation and the agreed scope of service.",
                'In such cases, professional fees already incurred, together with any outstanding balances and reimbursable expenses, shall remain payable in accordance with the agreed terms of this engagement.',
            ],
        ],
        [
            'title' => 'No Responsibility for Government Assessment or Financial Declarations',
            'paragraphs' => [
                "The client acknowledges that all sales figures, income, expenses, declarations, and financial information submitted to the city or municipality for purposes of assessment, taxation, or permit computation are provided by the client or derived from the client's records.",
                "John Kelly & Company does not prepare, alter, underdeclare, overdeclare, adjust, or manipulate any figures relating to sales, income, expenses, or other financial data for local government assessment purposes. The company relies solely on information furnished by the client and submits such information as provided.",
                'All assessments, tax computations, fees, penalties, or charges imposed by the city or municipality are determined exclusively by the relevant government authorities. John Kelly & Company has no control over, and shall not be held responsible for, the amount assessed, billed, or required by any local government unit.',
                "Any liability arising from inaccurate, incomplete, or false information provided by the client-including penalties, surcharges, or delays-shall be the sole responsibility of the client, and shall not be attributed to John Kelly & Company.",
            ],
        ],
        [
            'title' => 'Release, Waiver, and Quitclaim',
            'paragraphs' => [
                "The client expressly acknowledges that John Kelly & Company is engaged solely to provide assistance, coordination, documentation, and advisory support within the scope of this proposal and is not involved in the actual operation, management, or day-to-day conduct of the client's business.",
                'To the fullest extent permitted by law, the client fully releases, waives, and forever discharges John Kelly & Company, including its stockholders, directors, officers, managers, consultants, employees, suppliers, agents, and any related stakeholders, from any and all claims, demands, actions, liabilities, losses, damages, penalties, fines, costs, or expenses of whatever kind or nature, whether known or unknown, that may arise from or relate to:',
            ],
            'items' => [
                'Any act, omission, fault, negligence, or non-compliance of the client.',
                'Any assessment, penalty, surcharge, fine, sanction, or enforcement action imposed by the city, municipality, or any government authority.',
                "Any findings, violations, deficiencies, or issues discovered during inspections or evaluations by government agencies.",
                "Any operational, financial, regulatory, health, safety, environmental, fire, building, water testing, HVAC, or similar compliance matters outside the scope of John Kelly & Company's engagement.",
            ],
            'outro' => "The client further acknowledges that any penalties, charges, or adverse findings imposed by the city or municipality are the result of the client's operations, records, declarations, or compliance status, and shall not be attributed to John Kelly & Company. The client hereby executes this release, waiver, and quitclaim voluntarily and with full understanding, and agrees that it shall not file, cause to be filed, or pursue any claim, complaint, action, or proceeding against John Kelly & Company or any of its covered persons arising from the matters described above. This release and waiver shall survive the completion or termination of this engagement and shall remain fully binding upon the client.",
        ],
        [
            'title' => 'Communication and Coordination',
            'paragraphs' => [
                "All communication will go through the client's designated representative.",
                'Coordination will be done through official email or any agreed channels to ensure proper documentation and accountability.',
            ],
        ],
        [
            'title' => 'Severability',
            'paragraphs' => [
                'If any provision of this proposal or agreement is declared invalid, illegal, or unenforceable by a court or competent authority, such provision shall be severed from the agreement and shall not affect the validity, legality, or enforceability of the remaining provisions.',
                'All other provisions shall continue to remain in full force and effect, as if the invalid or unenforceable provision had never been included.',
            ],
        ],
        [
            'title' => 'Termination',
            'paragraphs' => [
                'Either party may end the engagement with thirty (30) days written notice, provided that all pending obligations are settled.',
            ],
        ],
        [
            'title' => 'No Employer-Employee Relationship',
            'paragraphs' => [
                'This engagement does not create an employer-employee, principal-agent, partnership, or joint venture relationship between the client and John Kelly & Company, or any of its consultants, officers, employees, or representatives.',
                'John Kelly & Company acts solely as an independent service provider, and nothing in this proposal or engagement shall be construed to give either party authority to bind the other. Each party shall remain fully responsible for its own personnel, obligations, liabilities, taxes, and statutory compliance.',
            ],
        ],
        [
            'title' => 'Confidentiality',
            'paragraphs' => [
                'All information and documents shared during the engagement will be kept strictly confidential and used only for legitimate business purposes, unless disclosure is required by law or approved by the client.',
            ],
        ],
        [
            'title' => 'Governing Law and Venue',
            'paragraphs' => [
                'This agreement shall follow the laws of the Republic of the Philippines. Any disputes shall be resolved through good faith discussion, and if necessary, submitted to the proper courts of Cebu City.',
            ],
        ],
    ];

    private const ENGAGEMENT_TEAM = [
        ['name' => 'Mr. John Kelly D. Abalde', 'designation' => 'Senior Consultant', 'branch' => 'Cebu City HQ Branch', 'email' => 'john.abalde@jknc.io'],
        ['name' => 'Mr. Lyndon Earl Rio', 'designation' => 'Senior Consultant', 'branch' => 'Cebu City HQ Branch', 'email' => 'l.rio@jknc.io'],
        ['name' => 'Ms. Ma. Lourdes T. Mata', 'designation' => 'Associate', 'branch' => 'Cebu City HQ Branch', 'email' => 'm.mata@jknc.io'],
        ['name' => 'Ms. Rubeca Potayre', 'designation' => 'Associate', 'branch' => 'Cebu City HQ Branch', 'email' => 'r.potayre@jknc.io'],
        ['name' => 'Ms. Immaculate Espina', 'designation' => 'Associate', 'branch' => 'Lapu-Lapu Branch', 'email' => ''],
        ['name' => 'Ms. Carmela Ortiz', 'designation' => 'Associate', 'branch' => 'Cebu City HQ Branch', 'email' => ''],
    ];

    public function show(Deal $deal): View
    {
        $deal->loadMissing('contact', 'proposal');

        $proposal = $deal->proposal ?: DealProposal::create($this->defaultProposalPayload($deal));

        return $this->renderProposalPage($deal, $proposal, false);
    }

    public function previewPage(Deal $deal): View
    {
        $deal->loadMissing('contact', 'proposal');

        $proposal = $deal->proposal ?: new DealProposal($this->defaultProposalPayload($deal));

        return $this->renderProposalPage($deal, $proposal, true);
    }

    private function renderProposalPage(Deal $deal, DealProposal $proposal, bool $readOnlyPreview): View
    {
        if ($proposal->exists) {
            $proposal->refresh();
        }

        $documentData = $this->documentData($deal, $proposal);
        $requirementGroup = $this->selectedRequirementGroup($deal);
        $generatedPdfPath = null;
        $previewError = null;

        try {
            $baseName = Str::slug((string) ($deal->deal_code ?: 'deal-proposal')).'-proposal.docx';
            $generatedPdfPath = $this->generateProposalPdf($documentData, $baseName);
        } catch (Throwable $exception) {
            $generatedPdfPath = $this->generateProposalPdf(
                $documentData,
                Str::slug((string) ($deal->deal_code ?: 'deal-proposal')).'-proposal.docx'
            );
            $previewError = $generatedPdfPath ? null : $exception->getMessage();
        }

        return view('deals.proposal.show', [
            'deal' => $deal,
            'proposal' => $proposal,
            'documentData' => $documentData,
            'proposalDocumentHtml' => $this->renderProposalDocument($documentData),
            'generatedPdfUrl' => $generatedPdfPath ? route('uploads.show', ['path' => $generatedPdfPath]) : null,
            'generatedPdfDownloadUrl' => $generatedPdfPath ? route('uploads.show', ['path' => $generatedPdfPath, 'download' => 1]) : null,
            'requirementGroup' => $requirementGroup,
            'previewError' => $previewError,
            'readOnlyPreview' => $readOnlyPreview,
        ]);
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $deal->loadMissing('contact', 'proposal');
        $proposal = $deal->proposal ?: DealProposal::create($this->defaultProposalPayload($deal));

        $validated = $this->validatedPayload($request);

        $proposal->fill($validated);
        $proposal->save();

        return redirect()
            ->route('deals.proposal.show', $deal)
            ->with('success', 'Proposal saved successfully.');
    }

    public function preview(Request $request, Deal $deal): JsonResponse
    {
        $deal->loadMissing('contact', 'proposal');
        $proposal = $deal->proposal ?: new DealProposal($this->defaultProposalPayload($deal));

        $validated = $this->validatedPayload($request);
        $previewProposal = new DealProposal(array_merge(
            $proposal->toArray(),
            $validated,
            ['deal_id' => $deal->id],
        ));

        $documentData = $this->documentData($deal, $previewProposal);
        $baseName = Str::slug((string) ($deal->deal_code ?: 'deal-proposal')).'-proposal-preview.docx';
        $pdfPath = $this->generateProposalPdf($documentData, $baseName);

        return response()->json([
            'html' => $this->renderProposalDocument($documentData),
            'pdf_url' => $pdfPath ? route('uploads.show', ['path' => $pdfPath]) : null,
            'pdf_download_url' => $pdfPath ? route('uploads.show', ['path' => $pdfPath, 'download' => 1]) : null,
        ]);
    }

    private function defaultProposalPayload(Deal $deal): array
    {
        $contact = $deal->contact;
        $clientName = trim(collect([
            $deal->first_name ?: $contact?->first_name,
            $deal->middle_name ?: $contact?->middle_name,
            $deal->last_name ?: $contact?->last_name,
        ])->filter()->implode(' '));

        $serviceType = $deal->services ?: 'BIR Compliance Services';
        $total = (float) ($deal->total_estimated_engagement_value ?? 0);
        $down = round($total * 0.5, 2);
        $requirementGroup = $this->selectedRequirementGroup($deal);

        return [
            'deal_id' => $deal->id,
            'reference_id' => 'PROP-'.$deal->deal_code,
            'crud_id' => 'DEAL-'.$deal->id,
            'proposal_date' => now()->toDateString(),
            'location' => $deal->company_address ?: $deal->address ?: 'Philippines',
            'service_type' => $serviceType,
            'scope_of_service' => (string) ($deal->scope_of_work ?: 'To be finalized based on the approved engagement scope and required BIR compliance deliverables.'),
            'what_you_will_receive' => $this->defaultDeliverables($deal),
            'our_proposal_text' => 'We are pleased to submit this proposal for your consideration. John Kelly & Company will provide the required advisory, preparation, coordination, and compliance support aligned with your engagement requirements.',
            'requirements_sole' => $requirementGroup === 'sole'
                ? "Client Contact Form\nClient Information Form\nTIN ID\nBusiness Permit / Mayor's Permit\nBIR Certificate of Registration"
                : '',
            'requirements_juridical' => $requirementGroup === 'juridical'
                ? "Client Contact Form\nBusiness Information Form\nSEC / CDA Certificate of Registration\nBIR Certificate of Registration\nLatest GIS / Officers Information"
                : '',
            'requirements_optional' => "Special Power of Attorney\nBoard Resolution / Secretary's Certificate\nAdditional supporting compliance records as may be required",
            'price_regular' => $total,
            'price_discount' => 0,
            'price_subtotal' => $total,
            'price_tax' => 0,
            'price_total' => $total,
            'price_down' => $down,
            'price_balance' => max($total - $down, 0),
            'prepared_by_name' => $deal->assigned_consultant ?: (string) auth()->user()?->name,
            'prepared_by_id' => (string) auth()->id(),
        ];
    }

    private function defaultDeliverables(Deal $deal): string
    {
        $service = $deal->services ?: 'BIR Compliance Service';
        $product = $deal->products ?: 'Compliance records and supporting documents';

        return implode("\n", [
            $service,
            $product,
            'Process guidance and compliance coordination',
            'Status updates and documentary checklist support',
        ]);
    }

    private function documentData(Deal $deal, DealProposal $proposal): array
    {
        $contact = $deal->contact;
        $clientName = trim(collect([
            $deal->first_name ?: $contact?->first_name,
            $deal->middle_name ?: $contact?->middle_name,
            $deal->last_name ?: $contact?->last_name,
        ])->filter()->implode(' '));

        $serviceType = $proposal->service_type ?: ($deal->services ?: 'BIR Compliance Services');
        $scope = $proposal->scope_of_service ?: '';
        $deliverables = $proposal->what_you_will_receive ?: '';
        $proposalText = $proposal->our_proposal_text ?: '';
        $location = $proposal->location ?: ($deal->company_address ?: $deal->address ?: 'Philippines');
        $requirementGroup = $this->selectedRequirementGroup($deal);

        return [
            'year' => optional($proposal->proposal_date)->format('Y') ?: now()->format('Y'),
            'date' => optional($proposal->proposal_date)->format('F d, Y') ?: now()->format('F d, Y'),
            'service_type' => $serviceType,
            'client_name' => $clientName ?: 'Client Name',
            'business_name' => $deal->company_name ?: 'Business Name',
            'reference_id' => $proposal->reference_id ?: 'PROP-'.$deal->deal_code,
            'crud_id' => $proposal->crud_id ?: 'DEAL-'.$deal->id,
            'location' => $location,
            'scope_of_service' => $scope,
            'what_you_will_receive' => $deliverables,
            'our_proposal_text' => $proposalText,
            'requirements_sole' => $requirementGroup === 'sole' ? ($proposal->requirements_sole ?: '') : '',
            'requirements_juridical' => $requirementGroup === 'juridical' ? ($proposal->requirements_juridical ?: '') : '',
            'requirements_optional' => $proposal->requirements_optional ?: '',
            'price_regular' => (float) ($proposal->price_regular ?? 0),
            'price_discount' => (float) ($proposal->price_discount ?? 0),
            'price_subtotal' => (float) ($proposal->price_subtotal ?? 0),
            'price_tax' => (float) ($proposal->price_tax ?? 0),
            'price_total' => (float) ($proposal->price_total ?? 0),
            'price_down' => (float) ($proposal->price_down ?? 0),
            'price_balance' => (float) ($proposal->price_balance ?? 0),
            'prepared_by_name' => $proposal->prepared_by_name ?: (string) auth()->user()?->name,
            'prepared_by_id' => $proposal->prepared_by_id ?: (string) auth()->id(),
            'company_phone' => self::COMPANY_PHONE,
            'company_email' => self::COMPANY_EMAIL,
            'company_website' => self::COMPANY_WEBSITE,
            'company_address' => self::COMPANY_ADDRESS,
            'executive_summary' => self::EXECUTIVE_SUMMARY,
            'role_and_value' => self::ROLE_AND_VALUE,
            'why_partner' => self::WHY_PARTNER,
            'service_areas' => self::SERVICE_AREAS,
            'proposal_highlights' => self::HIGHLIGHTS,
            'commitment' => self::COMMITMENT,
            'agreement_inclusions' => self::AGREEMENT_INCLUSIONS,
            'agreement_exclusions' => self::AGREEMENT_EXCLUSIONS,
            'supplemental_fees' => self::SUPPLEMENTAL_FEES,
            'terms_and_conditions' => self::TERMS_AND_CONDITIONS,
            'engagement_team' => self::ENGAGEMENT_TEAM,
            'engagement_team_intro' => "John Kelly & Company assigns a team of consultants and associates who collectively take responsibility for overseeing the project engagement, ensuring consistent guidance, clear communication, and smooth coordination throughout the duration of the engagement.",
            'supplemental_fee_note' => '(All rates are exclusive of VAT and/or withholding tax, if applicable)',
            'proposal_intro' => 'Our Proposal',
            'requirements_intro' => 'To proceed smoothly, we may request the following:',
            'requirements_note' => 'Additional requirements not listed above may be requested depending on the specific circumstances of the business and the requirements of the relevant government agency. Any such requirements will be communicated if and when identified. These requirements are determined by the applicable authority and are outside our control.',
            'system_note' => "This proposal is system-generated through the John Kelly & Company BIR Compliance Services Management System and was generated electronically under Reference ID: ".($proposal->crud_id ?: 'DEAL-'.$deal->id)." in the ordinary course of business; no handwritten or electronic signature is required for its validity, and this document shall be considered legally valid, binding for reference and evaluation purposes, and admissible as an official business record pursuant to applicable Philippine laws on electronic documents and electronic transactions, with any subsequent approval, payment, or engagement arising from this proposal to be governed by the final service agreement, official receipt, or written confirmation issued by John Kelly & Company.",
        ];
    }

    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'reference_id' => ['nullable', 'string', 'max:255'],
            'crud_id' => ['nullable', 'string', 'max:255'],
            'proposal_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'scope_of_service' => ['nullable', 'string'],
            'what_you_will_receive' => ['nullable', 'string'],
            'our_proposal_text' => ['nullable', 'string'],
            'requirements_sole' => ['nullable', 'string'],
            'requirements_juridical' => ['nullable', 'string'],
            'requirements_optional' => ['nullable', 'string'],
            'price_regular' => ['nullable', 'numeric'],
            'price_discount' => ['nullable', 'numeric'],
            'price_subtotal' => ['nullable', 'numeric'],
            'price_tax' => ['nullable', 'numeric'],
            'price_total' => ['nullable', 'numeric'],
            'price_down' => ['nullable', 'numeric'],
            'price_balance' => ['nullable', 'numeric'],
            'prepared_by_name' => ['nullable', 'string', 'max:255'],
            'prepared_by_id' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function selectedRequirementGroup(Deal $deal): string
    {
        if (strtolower((string) $deal->customer_type) !== 'business') {
            return 'sole';
        }

        $organization = $this->resolvedBusinessOrganization($deal);

        return $organization === 'sole_proprietorship' ? 'sole' : 'juridical';
    }

    private function resolvedBusinessOrganization(Deal $deal): ?string
    {
        $contact = $deal->contact;
        $companyName = trim((string) $deal->company_name);

        if ($companyName !== '') {
            $linkedCompany = Company::query()
                ->with('latestBif')
                ->where('company_name', $companyName)
                ->first();

            if (filled($linkedCompany?->latestBif?->business_organization)) {
                return (string) $linkedCompany->latestBif->business_organization;
            }
        }

        foreach ([
            $contact?->organization_type,
            $contact?->business_type_organization,
        ] as $value) {
            $mapped = $this->mapContactOrganizationToBusinessOrganization((string) $value);
            if ($mapped !== null) {
                return $mapped;
            }
        }

        return null;
    }

    private function mapContactOrganizationToBusinessOrganization(string $value): ?string
    {
        $normalized = Str::lower(trim($value));

        return match ($normalized) {
            'sole proprietorship', 'sole_proprietorship', 'sole proprietor', 'individual', 'natural person' => 'sole_proprietorship',
            'partnership' => 'partnership',
            'corporation', 'stock' => 'corporation',
            'cooperative' => 'cooperative',
            'ngo', 'non-stock' => 'ngo',
            'others', 'other' => 'other',
            default => null,
        };
    }

    private function renderProposalDocument(array $documentData): string
    {
        return ViewFacade::make('deals.proposal.partials.document', [
            'documentData' => $documentData,
        ])->render();
    }

    private function generateProposalPdf(array $documentData, string $docxFileName): ?string
    {
        $relativePdfPath = 'generated-proposals/deals/'.preg_replace('/\.docx$/i', '.pdf', $docxFileName);

        try {
            $pdf = Pdf::loadView('deals.proposal.pdf', [
                'documentData' => $documentData,
            ])->setPaper('a4', 'portrait');

            Storage::disk('public')->put($relativePdfPath, $pdf->output());

            return $relativePdfPath;
        } catch (Throwable) {
            return null;
        }
    }
}
