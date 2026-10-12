// Renderização fora da tela: miniaturas de templates e de variações de cor.
import { StaticCanvas } from 'fabric';
import { resolveColor } from './document';
import { ensureDocumentFonts } from './fonts';
import { createObject } from './renderer';

export async function renderDocument(doc, { width = 320, palette = null, format = 'jpeg', quality = 0.85 } = {}) {
    const source = palette ? { ...doc, palette } : doc;
    const scale = width / source.width;

    await ensureDocumentFonts(source);

    const ctx = { palette: source.palette };
    const objects = await Promise.all(source.elements.map((el) => createObject({ ...el }, ctx).catch(() => null)));

    const canvas = new StaticCanvas(document.createElement('canvas'), {
        width: Math.round(source.width * scale),
        height: Math.round(source.height * scale),
        enableRetinaScaling: false,
        renderOnAddRemove: false,
    });

    canvas.setZoom(scale);
    canvas.backgroundColor = resolveColor(source.background, source.palette) ?? (format === 'jpeg' ? '#FFFFFF' : '');
    objects.forEach((obj) => obj && canvas.add(obj));
    canvas.renderAll();

    try {
        return canvas.toDataURL({ format, quality, multiplier: 1 });
    } finally {
        canvas.dispose();
    }
}

/** Fila simples para não renderizar dezenas de miniaturas ao mesmo tempo. */
const queue = [];
let running = 0;

export function renderQueued(doc, options) {
    return new Promise((resolve, reject) => {
        queue.push({ doc, options, resolve, reject });
        pump();
    });
}

function pump() {
    while (running < 2 && queue.length) {
        const job = queue.shift();
        running++;
        renderDocument(job.doc, job.options)
            .then(job.resolve, job.reject)
            .finally(() => {
                running--;
                pump();
            });
    }
}
