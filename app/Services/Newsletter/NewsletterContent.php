<?php

namespace App\Services\Newsletter;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Conteúdo da newsletter: limpa o HTML do editor (só tags seguras, links http/https/mailto)
 * e aplica estilos inline, que é o que os clientes de e-mail (Gmail, Outlook) respeitam.
 */
class NewsletterContent
{
    public const MAX_TEXT_LENGTH = 20000;

    public const MAX_HTML_BYTES = 200_000;

    /** Estilos inline por tag, pensados para fundo branco e fonte do sistema */
    private const INLINE_STYLES = [
        'p' => 'margin:0 0 16px;',
        'div' => 'margin:0 0 4px;',
        'h1' => 'margin:24px 0 12px;font-size:22px;line-height:1.3;font-weight:700;color:#111827;',
        'h2' => 'margin:24px 0 12px;font-size:20px;line-height:1.3;font-weight:700;color:#111827;',
        'h3' => 'margin:20px 0 10px;font-size:17px;line-height:1.35;font-weight:700;color:#111827;',
        'a' => 'color:#2350f5;text-decoration:underline;',
        'strong' => 'font-weight:700;',
        'b' => 'font-weight:700;',
        'blockquote' => 'margin:16px 0;padding:4px 0 4px 16px;border-left:3px solid #d1d5db;color:#4b5563;',
        'pre' => 'margin:16px 0;padding:12px 14px;border-radius:6px;background:#f3f4f6;font-family:Consolas,Menlo,monospace;font-size:13px;line-height:1.5;white-space:pre-wrap;word-break:break-word;',
        'code' => 'font-family:Consolas,Menlo,monospace;font-size:13px;',
        'ul' => 'margin:0 0 16px;padding:0 0 0 22px;',
        'ol' => 'margin:0 0 16px;padding:0 0 0 22px;',
        'li' => 'margin:0 0 6px;',
        'figure' => 'margin:16px 0;',
        'figcaption' => 'margin:6px 0 0;font-size:12px;color:#6b7280;',
        'img' => 'display:block;max-width:100%;height:auto;border:0;outline:none;text-decoration:none;',
        'hr' => 'border:0;border-top:1px solid #e5e7eb;margin:24px 0;',
    ];

    public static function sanitize(?string $html): string
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return '';
        }

        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowMediaSchemes(['http', 'https'])
            ->allowRelativeLinks(false)
            ->allowRelativeMedias(false)
            // Atributos internos do Trix não interessam ao e-mail
            ->dropAttribute('data-trix-attachment', '*')
            ->dropAttribute('data-trix-attributes', '*')
            ->dropAttribute('data-trix-content-type', '*')
            ->dropAttribute('class', '*')
            ->dropAttribute('id', '*')
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->forceAttribute('a', 'target', '_blank')
            ->withMaxInputLength(self::MAX_HTML_BYTES * 2);

        return trim((new HtmlSanitizer($config))->sanitize($html));
    }

    /** HTML já limpo → HTML com estilos inline para o corpo do e-mail */
    public static function forEmail(string $sanitized): string
    {
        if ($sanitized === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="nl-root">'.$sanitized.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('nl-root');

        if (! $root) {
            return $sanitized;
        }

        foreach ((new DOMXPath($dom))->query('.//*', $root) as $element) {
            /** @var DOMElement $element */
            $style = self::INLINE_STYLES[strtolower($element->tagName)] ?? null;

            if ($style) {
                $element->setAttribute('style', $style);
            }

            // Imagens do editor chegam com largura fixa; no e-mail elas encolhem até a coluna
            if ($element->tagName === 'img') {
                $element->removeAttribute('height');
                $width = (int) $element->getAttribute('width');
                $width > 0 && $width <= 536
                    ? $element->setAttribute('width', (string) $width)
                    : $element->setAttribute('width', '536');
            }
        }

        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $dom->saveHTML($child);
        }

        return $html;
    }

    public static function plainText(?string $html): string
    {
        $html = preg_replace('/<(br|\/p|\/div|\/h[1-6]|\/li|\/blockquote|\/pre|\/figure)\b[^>]*>/i', "$0\n", (string) $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t\x{00A0}]+/u", ' ', $text);

        return trim(preg_replace("/\n\s*\n+/", "\n\n", $text));
    }

    public static function textLength(?string $html): int
    {
        return mb_strlen(preg_replace('/\s+/u', ' ', self::plainText($html)));
    }

    /** Sem texto e sem imagem: o editor costuma deixar só "<div><br></div>" */
    public static function isBlank(?string $html): bool
    {
        return self::plainText($html) === '' && ! str_contains(strtolower((string) $html), '<img');
    }

    /** Texto oculto que os clientes mostram ao lado do assunto na caixa de entrada */
    public static function preheader(?string $html, int $limit = 140): string
    {
        return (string) str(preg_replace('/\s+/u', ' ', self::plainText($html)))->limit($limit);
    }

    public static function hash(string $subject, string $body, ?string $banner): string
    {
        return hash('sha256', mb_strtolower(trim($subject))."\n".trim($body)."\n".(string) $banner);
    }
}
