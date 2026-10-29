<?php

return array (
  'ui' => 
  array (
    'searchCommands' => 'Command search',
    'theme' => 'Theme',
    'download' => 'Download',
    'copy' => 'Copy',
    'livePreview' => 'Live preview',
    'draftContract' => 'Draft contract',
    'draftContractText' => 'Contract text',
    'buildStatus' => 'Status',
    'draftReady' => 'Ready',
    'buildStatusText' => 'The document is ready to download',
    'reminder' => 'Reminder',
    'reviewRequired' => 'Review required',
    'reminderText' => 'Before signing, it is recommended to check the data and the details of the parties.',
    'buildPlan' => 'Contract structure',
    'txtDocument' => 'TXT document',
    'pdfDocument' => 'PDF document',
    'wordDocument' => 'Word document',
    'copied' => 'Copied',
    'txtDownloaded' => 'TXT document downloaded successfully',
    'pdfDownloaded' => 'PDF document downloaded successfully',
    'pdfError' => 'Error while creating the PDF',
    'docxDownloaded' => 'DOCX document downloaded successfully',
    'docxError' => 'Error while creating the DOCX',
    'generating' => 'Generating the contract...',
    'commandPaletteInDevelopment' => 'Command search is under development',
    'rubCurrencySymbol' => 'TJS',
    'notSpecified' => 'Not specified',
    'withoutNumber' => 'Without number',
    'party1' => 'Party 1',
    'party2' => 'Party 2',
    'applicableLaw' => 'the applicable legislation',
    'defaultPenaltyRule' => 'For a delay in performing its obligations, the Party at fault shall pay a penalty of 0.1% of the outstanding amount for each day of delay.',
    'defaultSpecialTerms' => 'Additional terms are determined by agreement of the Parties.',
    'notSelected' => 'Not selected',
    'stepContractType' => 'Contract type',
    'stepParams' => 'Parameters',
    'stepSections' => 'Sections',
    'autosave' => 'Auto-save',
    'step1' => 'STEP 1',
    'step2' => 'STEP 2',
    'step3' => 'STEP 3',
    'reset' => 'Reset',
    'numberPlaceholder' => 'For example: LA-001/26',
    'defaultCity' => 'Dushanbe',
    'defaultLaw' => 'Law of the Republic of Tajikistan',
    'defaultSpecialTermsValue' => 'The Contractor provides a monthly report on the work performed and records the Customer’s comments within 2 business days.',
    'selectedLabel' => 'Selected:',
    'fontSmaller' => 'Decrease font size',
    'fontBigger' => 'Increase font size',
    'notificationsToast' => 'Notifications: 3 new events',
    'confirmReset' => 'Reset all form data?',
    'copiedToast' => 'The text has been copied to the clipboard',
    'docxLibMissing' => 'The docx library is not loaded',
    'noText' => 'No text',
    'docxErrorPrefix' => 'DOCX creation error: ',
    'cityPrefix' => 'city of',
  ),
  'templates' => 
  array (
    'service' => 
    array (
      'label' => 'Contract for paid services',
      'description' => 'For providing services and performing works',
      'badge' => 'Main',
      'roleA' => 'Customer',
      'roleB' => 'Contractor',
      'defaultSubject' => 'provision of software development services',
    ),
    'supply' => 
    array (
      'label' => 'Supply contract',
      'description' => 'For the supply of goods and products',
      'badge' => 'Business',
      'roleA' => 'Buyer',
      'roleB' => 'Supplier',
      'defaultSubject' => 'supply of goods',
    ),
    'nda' => 
    array (
      'label' => 'Non-disclosure agreement (NDA)',
      'description' => 'To protect confidential information',
      'badge' => 'Confidential',
      'roleA' => 'Disclosing party',
      'roleB' => 'Receiving party',
      'defaultSubject' => 'transfer and protection of confidential information',
    ),
  ),
  'sections' => 
  array (
    'definitions' => 
    array (
      'label' => 'Definitions and terms',
      'description' => 'Explanation of the key concepts of the contract',
    ),
    'scope' => 
    array (
      'label' => 'Subject of the contract',
      'description' => 'Description of the subject and obligations of the parties',
    ),
    'payment' => 
    array (
      'label' => 'Price and payment procedure',
      'description' => 'Financial terms of the contract',
    ),
    'acceptance' => 
    array (
      'label' => 'Acceptance and transfer procedure',
      'description' => 'Procedure for accepting the results of the work',
    ),
    'liability' => 
    array (
      'label' => 'Liability of the parties',
      'description' => 'Liability for breach of obligations',
    ),
    'ip_rights' => 
    array (
      'label' => 'Intellectual property rights',
      'description' => 'Transfer of rights to the result of the work',
    ),
    'termination' => 
    array (
      'label' => 'Term of the contract',
      'description' => 'Term of validity and termination procedure',
    ),
    'confidentiality' => 
    array (
      'label' => 'Confidentiality',
      'description' => 'Protection of information and data',
    ),
    'disputes' => 
    array (
      'label' => 'Dispute resolution',
      'description' => 'Procedure for settling disagreements',
    ),
    'force_majeure' => 
    array (
      'label' => 'Force majeure',
      'description' => 'Circumstances of insuperable force',
    ),
    'notices' => 
    array (
      'label' => 'Notices and communication',
      'description' => 'Procedure for exchanging notices',
    ),
    'entire_agreement' => 
    array (
      'label' => 'Final provisions',
      'description' => 'Final terms of the contract',
    ),
  ),
  'draft' => 
  array (
    'header' => ':customer, hereinafter referred to as ":roleA", represented by an authorized representative acting on the basis of constituent documents, of the one part, and :contractor, hereinafter referred to as ":roleB", represented by an authorized representative acting on the basis of constituent documents, of the other part, jointly referred to as the "Parties", have concluded this Agreement as follows:',
    'definitions' => '1. DEFINITIONS AND TERMS

1.1. In this Agreement, the following terms have the following meanings:
— "Works/Services" — actions of the Contractor aimed at achieving the result specified in clause 2.1 of this Agreement;
— "Result" — material or intangible outcome of the Work performed, transferred to the Customer;
— "Parties" — Customer and Contractor, jointly referred to as;
— "Business Day" — a day that is not a weekend or holiday according to the legislation of the place of conclusion of the Agreement.

1.2. Other terms used in the Agreement shall be interpreted in accordance with applicable law.',
    'scope' => '2. SUBJECT OF THE AGREEMENT

2.1. :roleB undertakes to perform :subject as assigned by :roleA, and :roleA undertakes to accept the result of the work and pay for it in the manner and under the conditions provided for by this Agreement.

2.2. The scope, content, timing of work and other conditions may be determined by additional agreements or technical specifications that are an integral part of this Agreement.

2.3. :roleB guarantees the proper quality of the work performed and compliance of the result with the requirements of :roleA specified in the technical specification.

2.4. :roleB undertakes to perform the work personally, unless otherwise agreed by the Parties in writing.',
    'payment' => '3. COST AND PAYMENT PROCEDURE

3.1. The cost of work under this Agreement is :amount (amount in words) including all taxes and fees.

3.2. Payment is made in non-cash form by transferring funds to the bank account of :roleB.

3.3. :roleA undertakes to make payment in the following order:
— advance payment in the amount of 50% of the cost of work — within 5 (five) business days from the date of signing this Agreement;
— final settlement in the amount of 50% — within :days business days from the date of signing the act of completed work.

3.4. Payment obligations are considered fulfilled from the moment funds are received to the bank account of :roleB.

3.5. In case of payment delay, :roleA pays :roleB a penalty in the amount specified in Section 5 of this Agreement.',
    'acceptance' => '4. ACCEPTANCE AND TRANSFER PROCEDURE

4.1. The result of the work is transferred to the Customer under the Acceptance Act signed by both Parties.

4.2. :roleB undertakes to notify :roleA of the readiness of the work result in writing.

4.3. :roleA undertakes within :days business days from the date of receipt of the notification and the result of the work:
— to review the result and send :roleB a reasoned refusal to accept, indicating the deficiencies; or
— sign the Acceptance Act.

4.4. In the absence of a reasoned refusal within the established period, the Result is considered accepted, and the obligations of :roleB are considered properly fulfilled.

4.5. In case of deficiencies, :roleB undertakes to eliminate them within the period agreed by the Parties at its own expense.',
    'liability' => '5. LIABILITY OF THE PARTIES

5.1. For non-performance or improper performance of obligations under this Agreement, the Parties shall be liable in accordance with applicable law.

5.2. :penalty

5.3. The Party that has violated obligations under this Agreement shall compensate the other Party for damages in full, including lost profits.

5.4. Payment of a penalty does not release the Parties from performing obligations under this Agreement.

5.5. The total liability of :roleB under this Agreement is limited to the amount actually paid by :roleA under the Agreement.',
    'ip_rights' => '6. INTELLECTUAL PROPERTY RIGHTS

6.1. Exclusive rights to the Result created under this Agreement shall pass to :roleA from the moment of full payment and signing of the Acceptance Act.

6.2. :roleB retains the right to use the developed methodologies, libraries and general-purpose tools in its further activities.

6.3. Until the transfer of rights, :roleB undertakes not to transfer the Result to third parties and not to use it for its own purposes.

6.4. :roleB guarantees that the Result does not violate the rights of third parties to intellectual property objects.',
    'termination' => '7. TERM OF THE AGREEMENT

7.1. This Agreement enters into force on :startDate and is valid until :endDate.

7.2. This Agreement may be terminated:
— by mutual agreement of the Parties;
— at the initiative of one of the Parties with written notice to the other Party at least 30 (thirty) days in advance;
— in case of material breach of the terms of the Agreement by one of the Parties.

7.3. A material breach is a breach that entails such damage to the other Party that it is largely deprived of what it was entitled to expect when concluding the Agreement.

7.4. Termination of the Agreement does not release the Parties from fulfilling obligations that arose before the date of its termination.',
    'confidentiality' => '8. CONFIDENTIALITY

8.1. The Parties undertake not to disclose information received during the performance of this Agreement to third parties without prior written consent of the other Party.

8.2. Any information transmitted by the Parties in written, electronic or other form, including commercial, technical, financial information, is considered confidential.

8.3. Confidential information does not include information:
— that has become publicly available through no fault of the receiving Party;
— lawfully obtained from third parties without an obligation of non-disclosure;
— independently developed by the receiving Party.

8.4. Confidentiality obligations remain in effect for 5 (five) years after the termination of this Agreement.',
    'disputes' => '9. DISPUTE RESOLUTION

9.1. All disputes and disagreements arising between the Parties in connection with the performance of this Agreement shall be resolved through negotiations.

9.2. The Party whose rights have been violated shall send a written claim to the other Party setting out the essence of the violation and the requirements.

9.3. The Party that received the claim shall review it and provide a reasoned response within 15 (fifteen) business days from the date of receipt.

9.4. If it is impossible to reach an agreement through the pre-trial procedure, the dispute shall be resolved in court in accordance with :law.

9.5. The Parties agree that the jurisdiction of disputes is determined by the location of the defendant.',
    'force_majeure' => '10. FORCE MAJEURE

10.1. The Parties shall be released from liability for partial or complete non-performance of obligations if it is caused by force majeure circumstances (fire, flood, earthquake, military actions, acts of state authorities, etc.) that arose after the conclusion of the Agreement.

10.2. The Party unable to fulfill its obligations shall notify the other Party within 3 (three) business days from the occurrence of such circumstances, indicating their nature and estimated duration.

10.3. Confirmation of the existence of force majeure circumstances are documents issued by competent state authorities.

10.4. If force majeure circumstances continue for more than 30 (thirty) days, any of the Parties has the right to terminate the Agreement unilaterally without compensation for damages.',
    'notices' => '11. NOTICES AND COMMUNICATION

11.1. All notices, demands and documents under the Agreement shall be sent in writing:
— by courier with confirmation of delivery;
— by registered mail with acknowledgment of receipt;
— by e-mail with confirmation of receipt.

11.2. The notice is considered received:
— upon personal delivery — on the day of delivery against signature;
— when sent by mail — on the 10th day after sending;
— by e-mail — on the day of sending, provided there is confirmation of reading.

11.3. The Parties are obliged to immediately notify each other of any changes in the details specified in Section 13 of this Agreement.

11.4. Notices shall be sent to the addresses specified in Section 13 of this Agreement.',
    'specialTerms' => '12. SPECIAL TERMS

12.1. :terms

12.2. The Parties acknowledge that this Agreement contains all agreements between them on the subject matter of the Agreement.',
    'entire_agreement' => '13. FINAL PROVISIONS

13.1. This Agreement contains the entire agreement of the Parties and cancels all previous oral and written agreements on the subject matter of the Agreement.

13.2. Any amendments and additions to this Agreement are valid only in writing, signed by authorized representatives of both Parties.

13.3. If any provision of the Agreement is found to be invalid or unenforceable, this does not affect the validity of the remaining provisions, which remain in full force.

13.4. In all other matters not regulated by this Agreement, the Parties shall be guided by :law.

13.5. The Agreement is drawn up in two copies having equal legal force, one for each of the Parties.

13.6. All appendices and additional agreements to this Agreement are an integral part thereof.',
    'signatures' => '14. ADDRESSES, DETAILS AND SIGNATURES OF THE PARTIES

:roleA:
:customer

Legal address: ____________________
Mailing address: ____________________
Tax ID: ____________________
Bank account: ____________________
Bank: ____________________
SWIFT: ____________________

____________________ / ____________________ /
(signature) (Full name)

Seal

:roleB:
:contractor

Legal address: ____________________
Mailing address: ____________________
Tax ID: ____________________
Bank account: ____________________
Bank: ____________________
SWIFT: ____________________

____________________ / ____________________ /
(signature) (Full name)

Seal',
  ),
  'timeline' => 
  array (
    'template' => 'Template: :value',
    'parties' => 'Parties: :value',
    'term' => 'Term: :value',
    'sections' => 'Sections: :value',
  ),
  'ai' => 
  array (
    'criteriaTitle' => 'Your criteria',
    'criteriaHint' => 'Conditions the AI must include in the contract',
    'criteriaPlaceholder' => 'For example: 30% prepayment within 3 business days',
    'criteriaAdd' => 'Add',
    'criteriaEmpty' => 'No criteria yet',
    'criteriaRemove' => 'Remove criterion',
    'generate' => 'Refine with AI',
    'generating' => 'AI is editing sections…',
    'stop' => 'Stop',
    'restore' => 'Restore template',
    'aiBadge' => 'AI sections',
    'aiNotice' => 'The AI rewrote some sections. The rest are still rebuilt from the form fields; press Restore template to drop the edits.',
    'chatTitle' => 'Discuss with AI',
    'chatHint' => 'The AI sees the form and the current draft',
    'chatPlaceholder' => 'What should be clarified or added to the contract?',
    'chatSend' => 'Send',
    'chatEmpty' => 'Ask which clauses the contract is missing.',
    'addCriterion' => 'Add to criteria',
    'criterionAdded' => 'Criterion added',
    'generated' => 'Sections changed',
    'restored' => 'AI edits dropped, the contract is template-based again',
    'serviceDown' => 'The service is temporarily unavailable. Please try again in a few seconds.',
    'stopped' => 'Generation stopped',
    'statusChecking' => 'Checking the service…',
    'statusOnline' => 'Service is available',
    'statusOffline' => 'Service is unavailable',
    'statusRag' => 'Code base: :count fragments',
    'statusRagOff' => 'Code base is not ready',
    'stageSelecting' => 'AI is choosing sections…',
    'stageEditing' => 'AI is editing sections: :count',
    'stageRetrying' => 'Retrying sections: :count',
    'unapplied' => 'Could not apply: :list',
    'lostValues' => 'Form values missing from the text: :list — check the contract',
  ),
);
