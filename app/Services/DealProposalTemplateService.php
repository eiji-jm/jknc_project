<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DealProposalTemplateService
{
    private const TEMPLATE_PATH = 'proposal-templates/deal-proposal-template.docx';
    private const MICROSOFT_WORD_PATH = 'C:\\Program Files\\Microsoft Office\\root\\Office16\\WINWORD.EXE';
    private const LIBRE_OFFICE_PATH = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
    private const BULLET_LIST_PLACEHOLDERS = [
        '{{WHAT_YOU_WILL_RECEIVE}}',
        '{{REQUIREMENTS_SOLE}}',
        '{{REQUIREMENTS_JURIDICAL}}',
        '{{REQUIREMENTS_OPTIONAL}}',
    ];

    public function generate(array $data, string $fileName): string
    {
        $templatePath = $this->resolveTemplatePath();
        if ($templatePath === null) {
            throw new RuntimeException('Proposal template file was not found.');
        }

        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZIP extension is not available for DOCX generation.');
        }

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir) && !mkdir($tempDir, 0777, true) && !is_dir($tempDir)) {
            throw new RuntimeException('Unable to prepare temporary directory for proposal generation.');
        }

        $tempPath = tempnam($tempDir, 'deal-proposal-');
        if ($tempPath === false) {
            throw new RuntimeException('Unable to prepare temporary proposal file.');
        }

        $docxPath = $tempPath.'.docx';
        if (!copy($templatePath, $docxPath)) {
            @unlink($tempPath);
            throw new RuntimeException('Unable to copy proposal template.');
        }
        @unlink($tempPath);

        $archive = new ZipArchive();
        if ($archive->open($docxPath) !== true) {
            @unlink($docxPath);
            throw new RuntimeException('Unable to open proposal template archive.');
        }

        $replacements = $this->placeholderMap($data);

        for ($index = 0; $index < $archive->numFiles; $index++) {
            $entryName = $archive->getNameIndex($index);
            if (!is_string($entryName) || !str_starts_with($entryName, 'word/') || !str_ends_with($entryName, '.xml')) {
                continue;
            }

            $xml = $archive->getFromIndex($index);
            if (!is_string($xml) || $xml === '') {
                continue;
            }

            $updated = $this->replacePlaceholdersInXml($xml, $replacements);
            if ($updated !== $xml) {
                $archive->addFromString($entryName, $updated);
            }
        }

        $archive->close();

        $relativePath = 'generated-proposals/deals/'.$fileName;
        Storage::disk('public')->put($relativePath, file_get_contents($docxPath));
        @unlink($docxPath);

        return $relativePath;
    }

    public function generatePdfPreview(string $docxRelativePath): ?string
    {
        if (!Storage::disk('public')->exists($docxRelativePath)) {
            return null;
        }

        $sourcePath = Storage::disk('public')->path($docxRelativePath);
        $tempDir = storage_path('app/temp/proposal-pdf-preview-'.uniqid('', true));

        if (!is_dir($tempDir) && !mkdir($tempDir, 0777, true) && !is_dir($tempDir)) {
            return null;
        }

        $pdfName = pathinfo($sourcePath, PATHINFO_FILENAME).'.pdf';
        $generatedPdfPath = $tempDir.DIRECTORY_SEPARATOR.$pdfName;

        if (!$this->convertDocxToPdf($sourcePath, $tempDir, $generatedPdfPath)) {
            $this->cleanupDirectory($tempDir);
            return null;
        }

        $relativePdfPath = preg_replace('/\.docx$/i', '.pdf', $docxRelativePath) ?: ($docxRelativePath.'.pdf');
        Storage::disk('public')->put($relativePdfPath, file_get_contents($generatedPdfPath));

        $this->cleanupDirectory($tempDir);

        return $relativePdfPath;
    }

    private function convertDocxToPdf(string $sourcePath, string $tempDir, string $generatedPdfPath): bool
    {
        if ($this->convertWithMicrosoftWord($sourcePath, $generatedPdfPath)) {
            return true;
        }

        return $this->convertWithLibreOffice($sourcePath, $tempDir, $generatedPdfPath);
    }

    private function convertWithMicrosoftWord(string $sourcePath, string $generatedPdfPath): bool
    {
        $wordPath = $this->resolveMicrosoftWordPath();
        if ($wordPath === null) {
            return false;
        }

        $command = implode('; ', [
            "\$ErrorActionPreference = 'Stop'",
            '$word = New-Object -ComObject Word.Application',
            '$word.Visible = $false',
            '$word.DisplayAlerts = 0',
            '$document = $word.Documents.Open(' . $this->powershellLiteral($sourcePath) . ')',
            '$document.SaveAs([ref] ' . $this->powershellLiteral($generatedPdfPath) . ', [ref] 17)',
            '$document.Close()',
            '$word.Quit()',
        ]);

        $process = new Process([
            'powershell.exe',
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-Command',
            $command,
        ]);

        $process->setTimeout(120);
        $process->run();

        return $process->isSuccessful() && is_file($generatedPdfPath);
    }

    private function convertWithLibreOffice(string $sourcePath, string $tempDir, string $generatedPdfPath): bool
    {
        $libreOfficePath = $this->resolveLibreOfficePath();
        if ($libreOfficePath === null) {
            return false;
        }

        $process = new Process([
            $libreOfficePath,
            '--headless',
            '--convert-to',
            'pdf',
            '--outdir',
            $tempDir,
            $sourcePath,
        ]);

        $process->setTimeout(60);
        $process->run();

        return $process->isSuccessful() && is_file($generatedPdfPath);
    }

    private function resolveTemplatePath(): ?string
    {
        $configuredPath = trim((string) env('DEAL_PROPOSAL_TEMPLATE_PATH', ''));
        $candidates = array_filter([
            $configuredPath !== '' ? $configuredPath : null,
            storage_path('app/'.self::TEMPLATE_PATH),
            storage_path('app/public/'.self::TEMPLATE_PATH),
            resource_path('doc_templates/deal-proposal-template.docx'),
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveLibreOfficePath(): ?string
    {
        $configuredPath = trim((string) env('LIBRE_OFFICE_PATH', ''));
        $candidates = array_filter([
            $configuredPath !== '' ? $configuredPath : null,
            self::LIBRE_OFFICE_PATH,
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveMicrosoftWordPath(): ?string
    {
        $configuredPath = trim((string) env('MICROSOFT_WORD_PATH', ''));
        $candidates = array_filter([
            $configuredPath !== '' ? $configuredPath : null,
            self::MICROSOFT_WORD_PATH,
            'C:\\Program Files (x86)\\Microsoft Office\\root\\Office16\\WINWORD.EXE',
            'C:\\Program Files\\Microsoft Office\\Office16\\WINWORD.EXE',
            'C:\\Program Files (x86)\\Microsoft Office\\Office16\\WINWORD.EXE',
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function powershellLiteral(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    private function placeholderMap(array $data): array
    {
        return [
            '{{YEAR}}' => (string) ($data['year'] ?? ''),
            '{{SERVICE_TYPE}}' => (string) ($data['service_type'] ?? ''),
            '{{DATE}}' => (string) ($data['date'] ?? ''),
            '{{CLIENT_NAME}}' => (string) ($data['client_name'] ?? ''),
            '{{BUSINESS_NAME}}' => (string) ($data['business_name'] ?? ''),
            '{{LOCATION}}' => (string) ($data['location'] ?? ''),
            '{{REF_ID}}' => (string) ($data['reference_id'] ?? ''),
            '{{OUR_PROPOSAL_TEXT}}' => $this->normalizeMultilineValue($data['our_proposal_text'] ?? ''),
            '{{SCOPE_OF_SERVICE}}' => $this->normalizeMultilineValue($data['scope_of_service'] ?? ''),
            '{{WHAT_YOU_WILL_RECEIVE}}' => $this->normalizeBulletListValue($data['what_you_will_receive'] ?? ''),
            '{{REQUIREMENTS_SOLE}}' => $this->normalizeBulletListValue($data['requirements_sole'] ?? ''),
            '{{REQUIREMENTS_JURIDICAL}}' => $this->normalizeBulletListValue($data['requirements_juridical'] ?? ''),
            '{{REQUIREMENTS_OPTIONAL}}' => $this->normalizeBulletListValue($data['requirements_optional'] ?? ''),
            '{{PRICE_REGULAR}}' => $this->formatMoney($data['price_regular'] ?? 0),
            '{{PRICE_DISCOUNT}}' => $this->formatMoney($data['price_discount'] ?? 0),
            '{{PRICE_SUBTOTAL}}' => $this->formatMoney($data['price_subtotal'] ?? 0),
            '{{PRICE_TAX}}' => $this->formatMoney($data['price_tax'] ?? 0),
            '{{PRICE_TOTAL}}' => $this->formatMoney($data['price_total'] ?? 0),
            '{{PRICE_DOWN}}' => $this->formatMoney($data['price_down'] ?? 0),
            '{{PRICE_BALANCE}}' => $this->formatMoney($data['price_balance'] ?? 0),
            '{{CRUD_ID}}' => (string) ($data['crud_id'] ?? ''),
            '{{PREPARED_BY_NAME}}' => (string) ($data['prepared_by_name'] ?? ''),
            '{{PREPARED_BY_ID}}' => (string) ($data['prepared_by_id'] ?? ''),
        ];
    }

    private function replacePlaceholdersInXml(string $xml, array $replacements): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = true;
        $document->formatOutput = false;

        if (!@$document->loadXML($xml)) {
            return $xml;
        }

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $textNodes = $xpath->query('//w:t');
        if ($textNodes === false) {
            return $xml;
        }

        $updated = false;

        foreach (iterator_to_array($textNodes) as $textNode) {
            $text = $textNode->textContent;
            if (!array_key_exists($text, $replacements)) {
                continue;
            }

            $replacement = (string) $replacements[$text];
            $runNode = $textNode->parentNode;
            if (!$runNode instanceof \DOMElement || $runNode->localName !== 'r') {
                $textNode->nodeValue = $replacement;
                $updated = true;
                continue;
            }

            $this->replaceRunText($document, $runNode, $replacement);
            if (in_array($text, self::BULLET_LIST_PLACEHOLDERS, true)) {
                $this->normalizeListParagraphLayout($document, $runNode);
            }
            $updated = true;
        }

        $updated = $this->enforceProposalSectionBreak($document, $replacements) || $updated;
        $updated = $this->removeUnusedRequirementHeadings($document, $replacements) || $updated;

        return $updated ? $document->saveXML() : $xml;
    }

    private function enforceProposalSectionBreak(\DOMDocument $document, array $replacements): bool
    {
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraphs = $xpath->query('//w:p');
        if ($paragraphs === false) {
            return false;
        }

        $proposalText = trim((string) ($replacements['{{OUR_PROPOSAL_TEXT}}'] ?? ''));
        if ($proposalText === '') {
            return false;
        }

        foreach ($paragraphs as $paragraph) {
            if (!$paragraph instanceof \DOMElement) {
                continue;
            }

            $text = trim((string) $xpath->evaluate('string(.)', $paragraph));
            if ($text !== $proposalText) {
                continue;
            }

            $serviceTypeParagraph = $this->previousParagraph($paragraph);
            $headingParagraph = $serviceTypeParagraph ? $this->previousParagraph($serviceTypeParagraph) : null;
            if (!$serviceTypeParagraph instanceof \DOMElement || !$headingParagraph instanceof \DOMElement) {
                return false;
            }

            $updated = false;
            $updated = $this->setParagraphFlag($document, $headingParagraph, 'pageBreakBefore') || $updated;
            $updated = $this->setParagraphFlag($document, $headingParagraph, 'keepNext') || $updated;
            $updated = $this->setParagraphFlag($document, $serviceTypeParagraph, 'keepNext') || $updated;

            return $updated;
        }

        return false;
    }

    private function previousParagraph(\DOMElement $paragraph): ?\DOMElement
    {
        $current = $paragraph->previousSibling;
        while ($current) {
            if ($current instanceof \DOMElement && $current->localName === 'p') {
                return $current;
            }
            $current = $current->previousSibling;
        }

        return null;
    }

    private function setParagraphFlag(\DOMDocument $document, \DOMElement $paragraph, string $flagName): bool
    {
        $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $paragraphProperties = null;

        foreach ($paragraph->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === 'pPr') {
                $paragraphProperties = $child;
                break;
            }
        }

        if (!$paragraphProperties instanceof \DOMElement) {
            $paragraphProperties = $document->createElementNS($namespace, 'w:pPr');
            $paragraph->insertBefore($paragraphProperties, $paragraph->firstChild);
        }

        foreach ($paragraphProperties->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === $flagName) {
                $child->setAttributeNS($namespace, 'w:val', '1');
                return true;
            }
        }

        $flag = $document->createElementNS($namespace, 'w:'.$flagName);
        $flag->setAttributeNS($namespace, 'w:val', '1');
        $paragraphProperties->appendChild($flag);

        return true;
    }

    private function replaceRunText(\DOMDocument $document, \DOMElement $runNode, string $replacement): void
    {
        $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $runProperties = null;

        foreach ($runNode->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === 'rPr') {
                $runProperties = $child->cloneNode(true);
                break;
            }
        }

        while ($runNode->firstChild) {
            $runNode->removeChild($runNode->firstChild);
        }

        if ($runProperties instanceof \DOMNode) {
            $runNode->appendChild($runProperties);
        }

        $lines = preg_split("/\r\n|\n|\r/", $replacement) ?: [''];
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $runNode->appendChild($document->createElementNS($namespace, 'w:br'));
            }

            $textElement = $document->createElementNS($namespace, 'w:t');
            if ($line !== trim($line) || str_contains($line, '  ')) {
                $textElement->setAttribute('xml:space', 'preserve');
            }
            $textElement->appendChild($document->createTextNode($line));
            $runNode->appendChild($textElement);
        }
    }

    private function normalizeListParagraphLayout(\DOMDocument $document, \DOMElement $runNode): void
    {
        $paragraphNode = $runNode->parentNode;
        if (!$paragraphNode instanceof \DOMElement || $paragraphNode->localName !== 'p') {
            return;
        }

        $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $paragraphProperties = null;

        foreach ($paragraphNode->childNodes as $child) {
            if ($child instanceof \DOMElement && $child->localName === 'pPr') {
                $paragraphProperties = $child;
                break;
            }
        }

        if (!$paragraphProperties instanceof \DOMElement) {
            $paragraphProperties = $document->createElementNS($namespace, 'w:pPr');
            $paragraphNode->insertBefore($paragraphProperties, $paragraphNode->firstChild);
        }

        foreach (iterator_to_array($paragraphProperties->childNodes) as $child) {
            if ($child instanceof \DOMElement && $child->localName === 'jc') {
                $paragraphProperties->removeChild($child);
            }
        }

        $alignment = $document->createElementNS($namespace, 'w:jc');
        $alignment->setAttributeNS($namespace, 'w:val', 'left');
        $paragraphProperties->appendChild($alignment);
    }

    private function removeUnusedRequirementHeadings(\DOMDocument $document, array $replacements): bool
    {
        $removed = false;
        $removed = $this->removeParagraphContainingText(
            $document,
            'For Sole Proprietor / Professional / Individual;',
            blank($replacements['{{REQUIREMENTS_SOLE}}'] ?? '')
        ) || $removed;
        $removed = $this->removeParagraphContainingText(
            $document,
            'For Juridical / Corporation / Partnership;',
            blank($replacements['{{REQUIREMENTS_JURIDICAL}}'] ?? '')
        ) || $removed;

        return $removed;
    }

    private function removeParagraphContainingText(\DOMDocument $document, string $needle, bool $shouldRemove): bool
    {
        if (!$shouldRemove) {
            return false;
        }

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraphs = $xpath->query('//w:p');
        if ($paragraphs === false) {
            return false;
        }

        foreach ($paragraphs as $paragraph) {
            if (!$paragraph instanceof \DOMElement) {
                continue;
            }

            $text = trim((string) $xpath->evaluate('string(.)', $paragraph));
            if ($text !== $needle) {
                continue;
            }

            $paragraph->parentNode?->removeChild($paragraph);
            return true;
        }

        return false;
    }

    private function normalizeMultilineValue(string $value): string
    {
        return trim(str_replace(["\r\n", "\r"], "\n", $value));
    }

    private function normalizeBulletListValue(string $value): string
    {
        $lines = collect(preg_split("/\r\n|\n|\r/", $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->map(fn ($line) => '• '.$line)
            ->values()
            ->all();

        return implode("\n", $lines);
    }

    private function formatMoney(mixed $value): string
    {
        return number_format((float) $value, 2);
    }

    private function cleanupDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory.DIRECTORY_SEPARATOR.$entry;
            if (is_file($path)) {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}
