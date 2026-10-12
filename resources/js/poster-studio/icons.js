import { icons } from 'lucide';

// Ícones sugeridos primeiro; a busca cobre toda a biblioteca Lucide.
export const FEATURED_ICONS = [
    'Phone', 'MessageCircle', 'Mail', 'MapPin', 'Globe', 'Instagram', 'Facebook', 'Clock', 'CalendarCheck',
    'Check', 'CircleCheck', 'BadgeCheck', 'ShieldCheck', 'Star', 'Award', 'Trophy', 'ThumbsUp', 'Heart',
    'Truck', 'HardHat', 'Construction', 'Building2', 'Factory', 'Warehouse', 'BrickWall', 'Hammer', 'Wrench',
    'Drill', 'Shovel', 'Ruler', 'Forklift', 'Container', 'Package', 'Zap', 'Timer', 'Gauge', 'Droplets',
    'Leaf', 'Sun', 'Sparkles', 'Flame', 'Percent', 'BadgePercent', 'Tag', 'Gift', 'Megaphone', 'Info',
    'TriangleAlert', 'Quote', 'Users', 'Handshake', 'Headset', 'Lightbulb', 'Target', 'TrendingUp',
    'ArrowRight', 'ArrowDown', 'ChevronRight', 'Plus', 'House', 'PartyPopper', 'Rocket', 'Crown',
];

export const iconNames = () => Object.keys(icons);

export const iconExists = (name) => !!icons[name];

const escapeAttr = (value) => String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');

/** SVG do ícone com o traço na cor pedida (viewBox 24×24). */
export function iconSvg(name, color = 'currentColor', strokeWidth = 2, size = 24) {
    const nodes = icons[name] ?? icons.Star;
    const children = nodes
        .map(([tag, attrs]) => `<${tag} ${Object.entries(attrs).map(([k, v]) => `${k}="${escapeAttr(v)}"`).join(' ')}/>`)
        .join('');

    return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="${escapeAttr(color)}" stroke-width="${strokeWidth}" stroke-linecap="round" stroke-linejoin="round">${children}</svg>`;
}

/** "CircleCheck" → "Circle check" para rótulos. */
export const iconLabel = (name) => name.replace(/([a-z])([A-Z0-9])/g, '$1 $2').replace(/^./, (c) => c.toUpperCase());
