// Helpers do documento gráfico (schema v1, ver app/Services/PosterStudio/PosterDocument.php).

export const clone = (value) => JSON.parse(JSON.stringify(value));

export const uid = () => 'el_' + Math.random().toString(36).slice(2, 12);

/** ColorRef → hex. {role} usa a paleta do documento; string é cor personalizada; null é transparente. */
export function resolveColor(ref, palette) {
    if (ref && typeof ref === 'object' && ref.role) {
        return palette?.colors?.[ref.role] ?? null;
    }

    return typeof ref === 'string' ? ref : null;
}

export const isLinked = (ref) => !!(ref && typeof ref === 'object' && ref.role);

export const TYPE_LABELS = {
    text: 'Texto',
    image: 'Imagem',
    rect: 'Retângulo',
    ellipse: 'Círculo',
    icon: 'Ícone',
    qrcode: 'QR Code',
};

const common = (doc, overrides) => ({
    id: uid(),
    x: doc.width / 2 - (overrides.width ?? 300) / 2,
    y: doc.height / 2 - (overrides.height ?? 100) / 2,
    angle: 0,
    opacity: 1,
    locked: false,
    hidden: false,
    bind: null,
    ...overrides,
});

/** Fábricas de elementos novos, já centralizados no canvas. */
export const factories = {
    heading: (doc) => common(doc, {
        type: 'text', name: 'Título', text: 'Seu título aqui', width: doc.width * 0.8, height: 90,
        fontFamily: 'Montserrat', fontSize: Math.round(doc.width / 13), fontWeight: 900, fontStyle: 'normal',
        textAlign: 'center', lineHeight: 1.05, charSpacing: 0, fill: { role: 'text' },
    }),
    subheading: (doc) => common(doc, {
        type: 'text', name: 'Subtítulo', text: 'Um subtítulo curto', width: doc.width * 0.7, height: 50,
        fontFamily: 'Inter', fontSize: Math.round(doc.width / 26), fontWeight: 600, fontStyle: 'normal',
        textAlign: 'center', lineHeight: 1.2, charSpacing: 0, fill: { role: 'muted' },
    }),
    body: (doc) => common(doc, {
        type: 'text', name: 'Texto', text: 'Escreva aqui um parágrafo curto sobre o seu serviço.', width: doc.width * 0.6, height: 80,
        fontFamily: 'Inter', fontSize: Math.round(doc.width / 38), fontWeight: 400, fontStyle: 'normal',
        textAlign: 'left', lineHeight: 1.35, charSpacing: 0, fill: { role: 'text' },
    }),
    label: (doc) => common(doc, {
        type: 'text', name: 'Etiqueta', text: 'ETIQUETA', width: doc.width * 0.4, height: 30,
        fontFamily: 'Inter', fontSize: Math.round(doc.width / 45), fontWeight: 800, fontStyle: 'normal',
        textAlign: 'center', lineHeight: 1, charSpacing: 250, fill: { role: 'primary' },
    }),
    rect: (doc) => common(doc, { type: 'rect', name: 'Retângulo', width: 360, height: 220, fill: { role: 'primary' }, stroke: null, strokeWidth: 0, radius: 0 }),
    rounded: (doc) => common(doc, { type: 'rect', name: 'Cartão', width: 420, height: 240, fill: { role: 'surface' }, stroke: null, strokeWidth: 0, radius: 32 }),
    pill: (doc) => common(doc, { type: 'rect', name: 'Botão', width: 460, height: 110, fill: { role: 'primary' }, stroke: null, strokeWidth: 0, radius: 55 }),
    outline: (doc) => common(doc, { type: 'rect', name: 'Moldura', width: 400, height: 260, fill: null, stroke: { role: 'primary' }, strokeWidth: 8, radius: 16 }),
    line: (doc) => common(doc, { type: 'rect', name: 'Linha', width: 200, height: 8, fill: { role: 'primary' }, stroke: null, strokeWidth: 0, radius: 4 }),
    band: (doc) => common(doc, { type: 'rect', name: 'Faixa', x: 0, width: doc.width, height: 140, fill: { role: 'dark' }, stroke: null, strokeWidth: 0, radius: 0 }),
    ellipse: (doc) => common(doc, { type: 'ellipse', name: 'Círculo', width: 260, height: 260, fill: { role: 'secondary' }, stroke: null, strokeWidth: 0 }),
    icon: (doc, icon = 'Star') => common(doc, { type: 'icon', name: 'Ícone', icon, width: 120, height: 120, fill: { role: 'primary' }, strokeWidth: 2 }),
    qrcode: (doc, value = 'https://wa.me/') => common(doc, {
        type: 'qrcode', name: 'QR Code', width: 240, height: 240, value, fill: { role: 'dark' }, background: '#FFFFFF',
    }),
    image: (doc, src, natural = null) => {
        const max = Math.min(doc.width, doc.height) * 0.6;
        const ratio = natural ? natural.width / natural.height : 1;
        const width = ratio >= 1 ? max : max * ratio;
        const height = ratio >= 1 ? max / ratio : max;

        return common(doc, {
            type: 'image', name: 'Imagem', src, width, height, fit: 'cover', radius: 0,
            brightness: 0, contrast: 0, saturation: 0, flipX: false, placeholder: !src,
        });
    },
};

/** Elementos fora da área do canvas (total ou parcialmente). */
export function outOfBounds(el, doc) {
    if (el.hidden || el.angle) return false;

    return el.x < -1 || el.y < -1 || el.x + el.width > doc.width + 1 || el.y + el.height > doc.height + 1;
}

/** Áreas cobertas pela interface do Instagram/WhatsApp em formatos verticais. */
export function safeAreas(doc) {
    if (doc.height / doc.width < 1.7) return [];

    const band = Math.round(doc.height * 0.13);

    return [
        { x: 0, y: 0, width: doc.width, height: band },
        { x: 0, y: doc.height - band, width: doc.width, height: band },
    ];
}
