// Converte elementos do documento em objetos Fabric e vice-versa.
// Convenção: origin left/top, x/y = left/top do objeto, width/height = caixa sem escala.
import { Ellipse, FabricImage, FabricText, Group, Rect, Textbox, filters, loadSVGFromString, util } from 'fabric';
import QRCode from 'qrcode';
import { resolveColor } from './document';
import { iconSvg } from './icons';

const imageCache = new Map();

/** Carrega (uma vez) a imagem de uma URL do próprio site. */
export function loadImageElement(src) {
    if (!imageCache.has(src)) {
        const promise = util.loadImage(src, { crossOrigin: 'anonymous' }).catch((error) => {
            imageCache.delete(src);
            throw error;
        });
        imageCache.set(src, promise);
    }

    return imageCache.get(src);
}

const color = (ref, ctx) => resolveColor(ref, ctx.palette);

function lockProps(locked) {
    return {
        lockMovementX: locked,
        lockMovementY: locked,
        lockScalingX: locked,
        lockScalingY: locked,
        lockRotation: locked,
        hasControls: !locked,
        hoverCursor: locked ? 'default' : 'move',
    };
}

function baseProps(el) {
    return {
        left: el.x,
        top: el.y,
        angle: el.angle || 0,
        opacity: el.opacity ?? 1,
        visible: !el.hidden,
        originX: 'left',
        originY: 'top',
        lockScalingFlip: true,
        ...lockProps(!!el.locked),
    };
}

/**
 * Assinatura das propriedades que exigem recriar o objeto (imagem, SVG, QR).
 * Se mudar, applyElement devolve false e o editor reconstrói o objeto.
 */
export function signature(el, ctx) {
    switch (el.type) {
        case 'image':
            return `${el.src}|${el.placeholder ? 1 : 0}`;
        case 'icon':
            return `${el.icon}|${color(el.fill, ctx)}|${el.strokeWidth}`;
        case 'qrcode':
            return `${el.value}|${color(el.fill, ctx)}|${color(el.background, ctx)}`;
        default:
            return el.type;
    }
}

async function svgGroup(svg, size) {
    const { objects, options } = await loadSVGFromString(svg);
    const frame = new Rect({ left: 0, top: 0, width: size, height: size, fill: 'transparent', strokeWidth: 0 });
    const group = new Group([frame, ...objects.filter(Boolean)], { subTargetCheck: false, interactive: false });
    group.svgSize = Math.max(options.width || size, options.height || size);

    return group;
}

function placeholder(el, message = 'Sem imagem') {
    const w = Math.max(el.width, 10);
    const h = Math.max(el.height, 10);
    const box = new Rect({
        left: 0, top: 0, width: w, height: h, rx: el.radius || 0, ry: el.radius || 0,
        fill: 'rgba(148,163,184,0.25)', stroke: 'rgba(100,116,139,0.8)', strokeWidth: Math.max(2, Math.min(w, h) / 120),
        strokeDashArray: [12, 8], strokeUniform: true,
    });
    const label = new FabricText(message, {
        left: w / 2, top: h / 2, originX: 'center', originY: 'center',
        fontFamily: 'Inter', fontWeight: 600, fill: 'rgba(71,85,105,0.95)',
        fontSize: Math.max(14, Math.min(44, Math.min(w, h) / 9)),
    });
    const group = new Group([box, label], { subTargetCheck: false, interactive: false });
    group.isPlaceholder = true;
    group.baseWidth = w;
    group.baseHeight = h;

    return group;
}

export async function createObject(el, ctx) {
    let obj;

    switch (el.type) {
        case 'text':
            obj = new Textbox(el.text ?? '', {
                width: el.width,
                fontFamily: el.fontFamily,
                fontSize: el.fontSize,
                fontWeight: el.fontWeight,
                fontStyle: el.fontStyle || 'normal',
                textAlign: el.textAlign || 'left',
                lineHeight: el.lineHeight ?? 1.15,
                charSpacing: el.charSpacing ?? 0,
                fill: color(el.fill, ctx) ?? 'transparent',
                editable: !el.locked,
                splitByGrapheme: false,
            });
            break;

        case 'rect':
            obj = new Rect({ width: el.width, height: el.height, strokeUniform: true });
            break;

        case 'ellipse':
            obj = new Ellipse({ rx: el.width / 2, ry: el.height / 2, strokeUniform: true });
            break;

        case 'image':
            if (!el.src || el.placeholder) {
                obj = placeholder(el);
                break;
            }
            try {
                obj = new FabricImage(await loadImageElement(el.src));
            } catch {
                obj = placeholder(el, 'Imagem indisponível');
                obj.isBroken = true;
            }
            break;

        case 'icon':
            obj = await svgGroup(iconSvg(el.icon, color(el.fill, ctx) ?? '#000000', el.strokeWidth ?? 2), 24);
            break;

        case 'qrcode': {
            const svg = await QRCode.toString(el.value || ' ', {
                type: 'svg',
                margin: 1,
                errorCorrectionLevel: 'M',
                color: { dark: color(el.fill, ctx) ?? '#000000', light: color(el.background, ctx) ?? '#00000000' },
            });
            const size = Number(svg.match(/viewBox="0 0 (\d+)/)?.[1] ?? 25);
            obj = await svgGroup(svg, size);
            break;
        }

        default:
            throw new Error(`Tipo de elemento desconhecido: ${el.type}`);
    }

    obj.elId = el.id;
    obj.sig = signature(el, ctx);
    applyElement(obj, el, ctx);

    return obj;
}

/**
 * Aplica as propriedades do elemento ao objeto existente.
 * Retorna false quando o objeto precisa ser recriado (ver signature()).
 * Pode ajustar a geometria do elemento (ex.: imagem "contain"); o editor sincroniza depois.
 */
export function applyElement(obj, el, ctx) {
    if (obj.sig !== signature(el, ctx)) return false;

    obj.set(baseProps(el));

    switch (el.type) {
        case 'text':
            obj.set({
                text: el.text ?? '',
                width: el.width,
                fontFamily: el.fontFamily,
                fontSize: el.fontSize,
                fontWeight: el.fontWeight,
                fontStyle: el.fontStyle || 'normal',
                textAlign: el.textAlign || 'left',
                lineHeight: el.lineHeight ?? 1.15,
                charSpacing: el.charSpacing ?? 0,
                fill: color(el.fill, ctx) ?? 'transparent',
                editable: !el.locked,
                scaleX: 1,
                scaleY: 1,
            });
            obj.initDimensions();
            el.height = Math.round(obj.height * 100) / 100;
            break;

        case 'rect': {
            const stroke = color(el.stroke, ctx);
            obj.set({
                width: el.width, height: el.height, scaleX: 1, scaleY: 1,
                rx: Math.min(el.radius || 0, el.width / 2, el.height / 2),
                ry: Math.min(el.radius || 0, el.width / 2, el.height / 2),
                fill: color(el.fill, ctx) ?? 'transparent',
                stroke: stroke && el.strokeWidth ? stroke : null,
                strokeWidth: stroke ? el.strokeWidth || 0 : 0,
            });
            break;
        }

        case 'ellipse': {
            const stroke = color(el.stroke, ctx);
            obj.set({
                rx: el.width / 2, ry: el.height / 2, scaleX: 1, scaleY: 1,
                fill: color(el.fill, ctx) ?? 'transparent',
                stroke: stroke && el.strokeWidth ? stroke : null,
                strokeWidth: stroke ? el.strokeWidth || 0 : 0,
            });
            break;
        }

        case 'image':
            if (obj.isPlaceholder) {
                obj.set({ scaleX: el.width / obj.baseWidth, scaleY: el.height / obj.baseHeight });
            } else {
                fitImage(obj, el);
            }
            break;

        case 'icon':
        case 'qrcode': {
            const base = obj.svgSize || obj.width;
            if (el.type === 'qrcode') el.height = el.width;
            obj.set({ scaleX: el.width / base, scaleY: el.height / base });
            break;
        }
    }

    obj.setCoords();

    return true;
}

/** Recorte "cover" (preenche a moldura) ou "contain" (moldura acompanha a proporção da foto). */
function fitImage(obj, el) {
    const img = obj.getElement();
    const nw = img.naturalWidth || img.width;
    const nh = img.naturalHeight || img.height;
    let scale;

    if (el.fit === 'contain') {
        scale = Math.min(el.width / nw, el.height / nh);
        const w = nw * scale;
        const h = nh * scale;

        if (Math.abs(w - el.width) > 0.5 || Math.abs(h - el.height) > 0.5) {
            if (!el.angle) {
                el.x += (el.width - w) / 2;
                el.y += (el.height - h) / 2;
            }
            el.width = Math.round(w * 100) / 100;
            el.height = Math.round(h * 100) / 100;
            obj.set({ left: el.x, top: el.y });
        }

        obj.set({ cropX: 0, cropY: 0, width: nw, height: nh, scaleX: scale, scaleY: scale });
    } else {
        scale = Math.max(el.width / nw, el.height / nh);
        const cw = el.width / scale;
        const ch = el.height / scale;
        obj.set({ cropX: (nw - cw) / 2, cropY: (nh - ch) / 2, width: cw, height: ch, scaleX: scale, scaleY: scale });
    }

    obj.set({ flipX: !!el.flipX });

    obj.clipPath = el.radius > 0
        ? new Rect({
            width: obj.width, height: obj.height, originX: 'center', originY: 'center',
            rx: Math.min(el.radius / scale, obj.width / 2, obj.height / 2),
            ry: Math.min(el.radius / scale, obj.width / 2, obj.height / 2),
        })
        : undefined;

    const wanted = [];
    if (el.brightness) wanted.push(new filters.Brightness({ brightness: el.brightness }));
    if (el.contrast) wanted.push(new filters.Contrast({ contrast: el.contrast }));
    if (el.saturation) wanted.push(new filters.Saturation({ saturation: el.saturation }));

    const key = `${el.brightness}|${el.contrast}|${el.saturation}`;
    if (obj.filterKey !== key) {
        obj.filters = wanted;
        obj.applyFilters();
        obj.filterKey = key;
    }

    obj.dirty = true;
}

/**
 * Lê a transformação feita pelo usuário (mover, redimensionar, girar), incorpora a escala
 * nas dimensões do elemento e devolve o patch de geometria.
 */
export function readGeometry(obj, el) {
    const patch = { x: round(obj.left), y: round(obj.top), angle: round(obj.angle || 0) };
    const sx = obj.scaleX || 1;
    const sy = obj.scaleY || 1;

    switch (el.type) {
        case 'text':
            if (Math.abs(sx - 1) > 0.001 || Math.abs(sy - 1) > 0.001) {
                patch.fontSize = round(el.fontSize * sy);
                patch.width = round(obj.width * sx);
            } else {
                patch.width = round(obj.width);
            }
            break;

        case 'rect':
            patch.width = round(obj.width * sx);
            patch.height = round(obj.height * sy);
            break;

        case 'ellipse':
            patch.width = round(obj.rx * 2 * sx);
            patch.height = round(obj.ry * 2 * sy);
            break;

        case 'image':
            patch.width = round((obj.isPlaceholder ? obj.baseWidth : obj.width) * sx);
            patch.height = round((obj.isPlaceholder ? obj.baseHeight : obj.height) * sy);
            break;

        case 'icon':
        case 'qrcode': {
            const base = obj.svgSize || obj.width;
            patch.width = round(base * sx);
            patch.height = round(base * sy);
            break;
        }
    }

    return patch;
}

const round = (n) => Math.round(n * 100) / 100;
