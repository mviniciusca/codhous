// Extração aproximada de paleta a partir de uma imagem (análise local, sem serviço externo).
// Quantiza as cores, escolhe a cor saturada mais frequente como principal e deriva os demais papéis.

const toHex = ([r, g, b]) => '#' + [r, g, b].map((v) => Math.round(v).toString(16).padStart(2, '0')).join('').toUpperCase();

const mix = (a, b, t) => a.map((v, i) => v + (b[i] - v) * t);

function hsl([r, g, b]) {
    r /= 255; g /= 255; b /= 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    const l = (max + min) / 2;
    const s = max === min ? 0 : l > 0.5 ? (max - min) / (2 - max - min) : (max - min) / (max + min);
    return { s, l };
}

const luminance = ([r, g, b]) => (0.299 * r + 0.587 * g + 0.114 * b) / 255;

export function extractPalette(image) {
    const size = 64;
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(image, 0, 0, size, size);

    const { data } = ctx.getImageData(0, 0, size, size);
    const buckets = new Map();

    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] < 128) continue;
        const key = (data[i] >> 4) << 8 | (data[i + 1] >> 4) << 4 | (data[i + 2] >> 4);
        const bucket = buckets.get(key) ?? { count: 0, sum: [0, 0, 0] };
        bucket.count++;
        bucket.sum[0] += data[i];
        bucket.sum[1] += data[i + 1];
        bucket.sum[2] += data[i + 2];
        buckets.set(key, bucket);
    }

    const colors = [...buckets.values()]
        .map((b) => ({ rgb: b.sum.map((v) => v / b.count), count: b.count }))
        .sort((a, b) => b.count - a.count);

    if (!colors.length) throw new Error('Imagem sem pixels visíveis.');

    const vivid = colors.filter((c) => { const { s, l } = hsl(c.rgb); return s > 0.35 && l > 0.2 && l < 0.75; });
    const primary = (vivid[0] ?? colors[0]).rgb;
    const accentCandidate = vivid.find((c) => Math.abs(hsl(c.rgb).l - hsl(primary).l) > 0.12 || colorDistance(c.rgb, primary) > 90);
    const darkest = [...colors].slice(0, 40).sort((a, b) => luminance(a.rgb) - luminance(b.rgb))[0].rgb;
    const dark = luminance(darkest) < 0.25 ? darkest : mix(primary, [10, 10, 15], 0.8);

    return {
        primary: toHex(primary),
        secondary: toHex(mix(primary, [255, 255, 255], 0.35)),
        accent: toHex(accentCandidate ? accentCandidate.rgb : mix(primary, [0, 0, 0], 0.25)),
        background: '#FFFFFF',
        surface: toHex(mix(primary, [255, 255, 255], 0.92)),
        text: '#111827',
        muted: '#4B5563',
        onPrimary: luminance(primary) > 0.6 ? '#111111' : '#FFFFFF',
        dark: toHex(dark),
        onDark: '#FFFFFF',
    };
}

function colorDistance(a, b) {
    return Math.sqrt(a.reduce((sum, v, i) => sum + (v - b[i]) ** 2, 0));
}
