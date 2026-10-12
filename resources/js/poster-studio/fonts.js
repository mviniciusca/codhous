// Garante que as fontes usadas estejam carregadas antes de medir/renderir texto ou exportar.
import { cache } from 'fabric';

const loaded = new Set();

export async function ensureFont(family, weight = 400, style = 'normal') {
    const key = `${family}|${weight}|${style}`;

    if (loaded.has(key) || !document.fonts?.load) return;

    try {
        await document.fonts.load(`${style} ${weight} 48px "${family}"`, 'AaÇçÃã0123');
        loaded.add(key);
        cache.clearFontCache(family);
    } catch (error) {
        console.warn('[poster-studio] fonte indisponível', family, error);
    }
}

export async function ensureDocumentFonts(doc) {
    const texts = (doc.elements ?? []).filter((el) => el.type === 'text');

    await Promise.all(texts.map((el) => ensureFont(el.fontFamily, el.fontWeight ?? 400, el.fontStyle ?? 'normal')));
}
