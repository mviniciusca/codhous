// Editor gráfico sobre Fabric.js. O documento (this.doc) é a fonte da verdade;
// os objetos Fabric são uma projeção dele e são sincronizados nos dois sentidos.
import { ActiveSelection, Canvas } from 'fabric';
import { clone, factories, resolveColor, uid } from './document';
import { ensureDocumentFonts, ensureFont } from './fonts';
import { applyElement, createObject, readGeometry } from './renderer';

const HISTORY_LIMIT = 80;
const SNAP_PX = 7;

export class Editor {
    constructor({ canvasEl, brand, onChange, onSelection, onTransform, onGuides, onImageRequest }) {
        this.brand = brand;
        this.hooks = { onChange, onSelection, onTransform, onGuides, onImageRequest };
        this.doc = null;
        this.zoom = 1;
        this.objects = new Map();
        this.history = [];
        this.historyIndex = -1;
        this.loadToken = 0;
        this.clipboard = [];
        this.snapping = true;
        this.commitTimer = null;

        this.canvas = new Canvas(canvasEl, {
            preserveObjectStacking: true,
            selectionColor: 'rgba(23,105,255,0.08)',
            selectionBorderColor: '#1769FF',
            selectionLineWidth: 1,
            controlsAboveOverlay: true,
            stopContextMenu: true,
            fireRightClick: false,
        });

        this.bindCanvasEvents();
    }

    // ------------------------------------------------------------------ carga

    get ctx() {
        return { palette: this.doc.palette, brand: this.brand };
    }

    async load(doc, { resetHistory = true, selectIds = [] } = {}) {
        const token = ++this.loadToken;
        const next = clone(doc);

        await ensureDocumentFonts(next);

        const ctx = { palette: next.palette, brand: this.brand };
        const objects = await Promise.all(next.elements.map((el) => createObject(el, ctx).catch((error) => {
            console.error('[poster-studio] falha ao renderizar elemento', el, error);
            return null;
        })));

        if (token !== this.loadToken) return false;

        this.doc = next;
        this.canvas.discardActiveObject();
        this.canvas.remove(...this.canvas.getObjects());
        this.objects.clear();

        objects.forEach((obj) => {
            if (!obj) return;
            this.objects.set(obj.elId, obj);
            this.canvas.add(obj);
        });

        this.applyBackground();
        this.setZoom(this.zoom);

        if (resetHistory) {
            this.history = [JSON.stringify(this.doc)];
            this.historyIndex = 0;
        }

        if (selectIds.length) this.select(selectIds);

        this.canvas.requestRenderAll();
        this.emitSelection();
        this.hooks.onChange?.({ history: false, structure: true });

        return true;
    }

    applyBackground() {
        this.canvas.backgroundColor = resolveColor(this.doc.background, this.doc.palette) ?? '';
    }

    // ------------------------------------------------------------------ eventos do canvas

    bindCanvasEvents() {
        const c = this.canvas;

        c.on('selection:created', () => this.emitSelection());
        c.on('selection:updated', () => this.emitSelection());
        c.on('selection:cleared', () => this.emitSelection());

        c.on('object:moving', (e) => {
            this.snap(e.target, e.e);
            this.emitTransform();
        });
        c.on('object:scaling', () => this.emitTransform());
        c.on('object:rotating', () => this.emitTransform());
        c.on('object:resizing', () => this.emitTransform());

        c.on('object:modified', (e) => {
            this.hooks.onGuides?.([]);
            this.syncFromCanvas(e.target);
        });

        c.on('mouse:up', () => this.hooks.onGuides?.([]));

        c.on('text:changed', (e) => {
            const el = this.element(e.target.elId);
            if (!el) return;
            el.text = e.target.text;
            el.height = e.target.height;
            if (el.bind) el.bind = null;
            this.hooks.onChange?.({ history: false });
            this.emitSelection();
        });

        c.on('text:editing:exited', () => this.commit());

        c.on('mouse:dblclick', (e) => {
            const el = e.target && this.element(e.target.elId);
            if (el?.type === 'image' && !el.locked) this.hooks.onImageRequest?.(el.id);
        });
    }

    /** Depois de mover/redimensionar: incorpora escala no documento e reconstrói o que for preciso. */
    syncFromCanvas(target) {
        if (!target) return;

        const targets = target instanceof ActiveSelection ? target.getObjects() : [target];

        if (target instanceof ActiveSelection) {
            // Ao desfazer a seleção, o Fabric devolve as coordenadas absolutas aos objetos
            this.canvas.discardActiveObject();
        }

        const rebuild = [];

        targets.forEach((obj) => {
            const el = this.element(obj.elId);
            if (!el) return;

            Object.assign(el, readGeometry(obj, el));

            if (!applyElement(obj, el, this.ctx) || obj.isPlaceholder) rebuild.push(el.id);
        });

        if (target instanceof ActiveSelection) this.select(targets.map((obj) => obj.elId));

        Promise.all(rebuild.map((id) => this.rebuild(id))).then(() => {
            this.canvas.requestRenderAll();
            this.commit();
            this.emitSelection();
        });
    }

    snap(target, event) {
        if (!this.snapping || event?.altKey) {
            this.hooks.onGuides?.([]);
            return;
        }

        const threshold = SNAP_PX / this.zoom;
        const box = target.getBoundingRect();
        const movingIds = new Set(target instanceof ActiveSelection ? target.getObjects().map((o) => o.elId) : [target.elId]);

        const xs = [0, this.doc.width / 2, this.doc.width];
        const ys = [0, this.doc.height / 2, this.doc.height];

        this.objects.forEach((obj, id) => {
            if (movingIds.has(id) || !obj.visible) return;
            const b = obj.getBoundingRect();
            xs.push(b.left, b.left + b.width / 2, b.left + b.width);
            ys.push(b.top, b.top + b.height / 2, b.top + b.height);
        });

        const best = (edges, candidates) => {
            let result = null;
            edges.forEach((edge) => candidates.forEach((candidate) => {
                const delta = candidate - edge;
                if (Math.abs(delta) <= threshold && (!result || Math.abs(delta) < Math.abs(result.delta))) {
                    result = { delta, pos: candidate };
                }
            }));
            return result;
        };

        const sx = best([box.left, box.left + box.width / 2, box.left + box.width], xs);
        const sy = best([box.top, box.top + box.height / 2, box.top + box.height], ys);
        const guides = [];

        if (sx) {
            target.set({ left: target.left + sx.delta });
            guides.push({ type: 'v', pos: sx.pos });
        }
        if (sy) {
            target.set({ top: target.top + sy.delta });
            guides.push({ type: 'h', pos: sy.pos });
        }

        target.setCoords();
        this.hooks.onGuides?.(guides);
    }

    // ------------------------------------------------------------------ seleção

    element(id) {
        return this.doc?.elements.find((el) => el.id === id) ?? null;
    }

    selectedIds() {
        const active = this.canvas.getActiveObject();
        if (!active) return [];
        if (active instanceof ActiveSelection) return active.getObjects().map((o) => o.elId);
        return [active.elId];
    }

    select(ids) {
        const objects = [].concat(ids).map((id) => this.objects.get(id)).filter((o) => o && o.visible);

        this.canvas.discardActiveObject();

        if (objects.length === 1) {
            this.canvas.setActiveObject(objects[0]);
        } else if (objects.length > 1) {
            this.canvas.setActiveObject(new ActiveSelection(objects, { canvas: this.canvas }));
        }

        this.canvas.requestRenderAll();
        this.emitSelection();
    }

    clearSelection() {
        this.canvas.discardActiveObject();
        this.canvas.requestRenderAll();
        this.emitSelection();
    }

    emitSelection() {
        this.hooks.onSelection?.(this.selectedIds());
    }

    emitTransform() {
        if (this.transformFrame) return;
        this.transformFrame = requestAnimationFrame(() => {
            this.transformFrame = null;
            const active = this.canvas.getActiveObject();
            if (!active) return;
            const box = active.getBoundingRect();
            this.hooks.onTransform?.({
                x: Math.round(box.left), y: Math.round(box.top),
                width: Math.round(box.width), height: Math.round(box.height),
                angle: Math.round(active.angle || 0),
            });
        });
    }

    // ------------------------------------------------------------------ edição

    /**
     * Altera propriedades de um elemento. commit=false para mudanças contínuas
     * (sliders, digitação): o histórico registra quando o usuário para.
     */
    async update(id, patch, { commit = true } = {}) {
        const el = this.element(id);
        if (!el) return;

        if (patch.fontFamily || patch.fontWeight || patch.fontStyle) {
            await ensureFont(patch.fontFamily ?? el.fontFamily, patch.fontWeight ?? el.fontWeight, patch.fontStyle ?? el.fontStyle);
        }

        Object.assign(el, patch);

        const obj = this.objects.get(id);
        if (!obj || !applyElement(obj, el, this.ctx)) {
            await this.rebuild(id);
        }

        this.canvas.requestRenderAll();
        commit ? this.commit() : this.scheduleCommit();
        this.emitSelection();
    }

    async updateMany(ids, patch) {
        for (const id of ids) {
            await this.update(id, typeof patch === 'function' ? patch(this.element(id)) : patch, { commit: false });
        }
        this.commit();
    }

    /** Recria o objeto Fabric de um elemento mantendo a posição na pilha e a seleção. */
    async rebuild(id) {
        const el = this.element(id);
        const old = this.objects.get(id);
        if (!el) return;

        const wasSelected = this.selectedIds().includes(id);
        const obj = await createObject(el, this.ctx);
        const index = this.doc.elements.indexOf(el);

        if (old) this.canvas.remove(old);
        this.objects.set(id, obj);
        this.canvas.insertAt(Math.min(index, this.canvas.getObjects().length), obj);

        if (wasSelected) this.canvas.setActiveObject(obj);
        this.canvas.requestRenderAll();
    }

    async add(el, { select = true } = {}) {
        if (el.type === 'text') await ensureFont(el.fontFamily, el.fontWeight, el.fontStyle);

        this.doc.elements.push(el);
        const obj = await createObject(el, this.ctx);
        this.objects.set(el.id, obj);
        this.canvas.add(obj);

        if (select) this.select([el.id]);
        this.commit();

        return el.id;
    }

    async addFromFactory(kind, ...args) {
        return this.add(factories[kind](this.doc, ...args));
    }

    remove(ids = this.selectedIds()) {
        const removable = ids.filter((id) => !this.element(id)?.locked);
        if (!removable.length) return;

        this.canvas.discardActiveObject();
        removable.forEach((id) => {
            const obj = this.objects.get(id);
            if (obj) this.canvas.remove(obj);
            this.objects.delete(id);
        });
        this.doc.elements = this.doc.elements.filter((el) => !removable.includes(el.id));

        this.canvas.requestRenderAll();
        this.commit();
        this.emitSelection();
    }

    async duplicate(ids = this.selectedIds(), offset = 30) {
        const copies = [];

        for (const id of ids) {
            const el = this.element(id);
            if (!el) continue;

            const copy = { ...clone(el), id: uid(), x: el.x + offset, y: el.y + offset, locked: false, name: el.name };
            const index = this.doc.elements.indexOf(el) + 1;
            this.doc.elements.splice(index, 0, copy);

            const obj = await createObject(copy, this.ctx);
            this.objects.set(copy.id, obj);
            this.canvas.insertAt(index, obj);
            copies.push(copy.id);
        }

        this.select(copies);
        this.commit();

        return copies;
    }

    copy(ids = this.selectedIds()) {
        this.clipboard = ids.map((id) => clone(this.element(id))).filter(Boolean);
    }

    async paste() {
        const ids = [];
        for (const el of this.clipboard) {
            const copy = { ...clone(el), id: uid(), x: el.x + 30, y: el.y + 30, locked: false };
            this.doc.elements.push(copy);
            const obj = await createObject(copy, this.ctx);
            this.objects.set(copy.id, obj);
            this.canvas.add(obj);
            ids.push(copy.id);
        }
        this.clipboard = this.clipboard.map((el) => ({ ...el, x: el.x + 30, y: el.y + 30 }));
        if (ids.length) {
            this.select(ids);
            this.commit();
        }
    }

    /** Muda a posição do elemento na pilha de camadas. */
    reorder(id, direction) {
        const elements = this.doc.elements;
        const from = elements.findIndex((el) => el.id === id);
        if (from < 0) return;

        const to = {
            up: Math.min(from + 1, elements.length - 1),
            down: Math.max(from - 1, 0),
            top: elements.length - 1,
            bottom: 0,
        }[direction] ?? direction;

        this.moveTo(id, to);
    }

    moveTo(id, to) {
        const elements = this.doc.elements;
        const from = elements.findIndex((el) => el.id === id);
        if (from < 0 || from === to) return;

        const [el] = elements.splice(from, 1);
        elements.splice(to, 0, el);
        this.canvas.moveObjectTo(this.objects.get(id), to);

        this.canvas.requestRenderAll();
        this.commit();
        this.hooks.onChange?.({ history: false, structure: true });
    }

    nudge(dx, dy) {
        const ids = this.selectedIds().filter((id) => !this.element(id)?.locked);
        if (!ids.length) return;

        // Coordenadas dos filhos de uma seleção múltipla são relativas a ela
        this.canvas.discardActiveObject();

        ids.forEach((id) => {
            const el = this.element(id);
            el.x += dx;
            el.y += dy;
            applyElement(this.objects.get(id), el, this.ctx);
        });

        this.select(ids);
        this.scheduleCommit();
    }

    /** Alinha ao canvas (1 elemento) ou à caixa da seleção (vários). */
    align(mode) {
        const ids = this.selectedIds().filter((id) => !this.element(id)?.locked);
        if (!ids.length) return;

        this.canvas.discardActiveObject();

        const boxes = ids.map((id) => ({ id, box: this.objects.get(id).getBoundingRect() }));
        const area = ids.length === 1
            ? { left: 0, top: 0, right: this.doc.width, bottom: this.doc.height }
            : {
                left: Math.min(...boxes.map((b) => b.box.left)),
                top: Math.min(...boxes.map((b) => b.box.top)),
                right: Math.max(...boxes.map((b) => b.box.left + b.box.width)),
                bottom: Math.max(...boxes.map((b) => b.box.top + b.box.height)),
            };

        boxes.forEach(({ id, box }) => {
            const el = this.element(id);
            let dx = 0;
            let dy = 0;

            if (mode === 'left') dx = area.left - box.left;
            if (mode === 'center') dx = (area.left + area.right) / 2 - (box.left + box.width / 2);
            if (mode === 'right') dx = area.right - (box.left + box.width);
            if (mode === 'top') dy = area.top - box.top;
            if (mode === 'middle') dy = (area.top + area.bottom) / 2 - (box.top + box.height / 2);
            if (mode === 'bottom') dy = area.bottom - (box.top + box.height);

            el.x += dx;
            el.y += dy;
            applyElement(this.objects.get(id), el, this.ctx);
        });

        this.select(ids);
        this.commit();
    }

    /** Distribui 3+ elementos com espaçamento igual. */
    distribute(axis) {
        const ids = this.selectedIds();
        if (ids.length < 3) return;

        this.canvas.discardActiveObject();

        const start = axis === 'h' ? 'left' : 'top';
        const size = axis === 'h' ? 'width' : 'height';
        const items = ids.map((id) => ({ id, box: this.objects.get(id).getBoundingRect() })).sort((a, b) => a.box[start] - b.box[start]);
        const first = items[0].box[start];
        const last = items.at(-1).box[start] + items.at(-1).box[size];
        const total = items.reduce((sum, item) => sum + item.box[size], 0);
        const gap = (last - first - total) / (items.length - 1);

        let cursor = first;
        items.forEach(({ id, box }) => {
            const el = this.element(id);
            const delta = cursor - box[start];
            if (axis === 'h') el.x += delta;
            else el.y += delta;
            applyElement(this.objects.get(id), el, this.ctx);
            cursor += box[size] + gap;
        });

        this.select(ids);
        this.commit();
    }

    // ------------------------------------------------------------------ documento

    async setPalette(palette) {
        this.doc.palette = clone(palette);
        await this.reapplyAll();
        this.commit();
    }

    async setBackground(ref) {
        this.doc.background = ref;
        this.applyBackground();
        this.canvas.requestRenderAll();
        this.commit();
    }

    setCaption(text) {
        this.doc.caption = text;
        this.scheduleCommit();
    }

    async reapplyAll() {
        this.applyBackground();
        const rebuild = [];
        this.doc.elements.forEach((el) => {
            const obj = this.objects.get(el.id);
            if (!obj || !applyElement(obj, el, this.ctx)) rebuild.push(el.id);
        });
        await Promise.all(rebuild.map((id) => this.rebuild(id)));
        this.canvas.requestRenderAll();
        this.emitSelection();
    }

    /** Substitui o documento inteiro (template, formato) mantendo o histórico. */
    async replaceDocument(doc) {
        await this.load(doc, { resetHistory: false });
        this.fit();
        this.commit();
    }

    /** Atualiza elementos vinculados com os dados atuais da empresa. */
    async refreshBindings(brand) {
        this.brand = brand;
        const values = brand.bindings ?? {};
        const changed = [];

        this.doc.elements.forEach((el) => {
            const value = el.bind ? values[el.bind] : null;
            if (value == null) return;

            if (el.type === 'text') el.text = (el.bindTemplate ?? '{value}').replace('{value}', value);
            if (el.type === 'image') Object.assign(el, { src: value, placeholder: false });
            if (el.type === 'qrcode') el.value = value;
            changed.push(el.id);
        });

        await this.reapplyAll();
        if (changed.length) this.commit();

        return changed.length;
    }

    // ------------------------------------------------------------------ histórico

    scheduleCommit(delay = 450) {
        clearTimeout(this.commitTimer);
        this.hooks.onChange?.({ history: false });
        this.commitTimer = setTimeout(() => this.commit(), delay);
    }

    commit() {
        clearTimeout(this.commitTimer);
        const snapshot = JSON.stringify(this.doc);

        if (this.history[this.historyIndex] === snapshot) return;

        this.history = this.history.slice(0, this.historyIndex + 1);
        this.history.push(snapshot);
        if (this.history.length > HISTORY_LIMIT) this.history.shift();
        this.historyIndex = this.history.length - 1;

        this.hooks.onChange?.({ history: true });
    }

    get canUndo() {
        return this.historyIndex > 0;
    }

    get canRedo() {
        return this.historyIndex < this.history.length - 1;
    }

    async undo() {
        if (!this.canUndo) return;
        this.commit();
        this.historyIndex = Math.max(0, this.historyIndex - 1);
        await this.restore();
    }

    async redo() {
        if (!this.canRedo) return;
        this.historyIndex += 1;
        await this.restore();
    }

    async restore() {
        const selection = this.selectedIds();
        const doc = JSON.parse(this.history[this.historyIndex]);
        const sizeChanged = !this.doc || doc.width !== this.doc.width || doc.height !== this.doc.height;

        await this.load(doc, { resetHistory: false, selectIds: selection.filter((id) => doc.elements.some((el) => el.id === id)) });
        if (sizeChanged) this.fit();
        this.hooks.onChange?.({ history: true });
    }

    // ------------------------------------------------------------------ zoom

    setZoom(zoom) {
        this.zoom = Math.min(4, Math.max(0.05, zoom));
        this.canvas.setDimensions({ width: Math.round(this.doc.width * this.zoom), height: Math.round(this.doc.height * this.zoom) });
        this.canvas.setZoom(this.zoom);
        this.canvas.requestRenderAll();

        return this.zoom;
    }

    fit(container = this.container) {
        if (!container || !this.doc) return this.zoom;
        this.container = container;

        const padding = window.innerWidth < 768 ? 24 : 72;
        const zoom = Math.min(
            (container.clientWidth - padding) / this.doc.width,
            (container.clientHeight - padding) / this.doc.height,
        );

        return this.setZoom(Math.max(0.05, zoom));
    }

    // ------------------------------------------------------------------ exportação

    /**
     * Renderiza a arte nas dimensões finais do documento (independe do zoom da tela).
     * Controles, guias e placeholders vazios não entram no arquivo.
     */
    async exportImage({ format = 'png', quality = 0.92, scale = 1, transparent = false } = {}) {
        await ensureDocumentFonts(this.doc);

        const selection = this.selectedIds();
        this.canvas.discardActiveObject();

        const hidden = [];
        this.objects.forEach((obj) => {
            if (obj.isPlaceholder && obj.visible) {
                obj.visible = false;
                hidden.push(obj);
            }
        });

        const background = this.canvas.backgroundColor;
        if (format === 'png' && transparent) this.canvas.backgroundColor = '';
        if (format === 'jpeg' && !background) this.canvas.backgroundColor = '#FFFFFF';

        // Renderiza em 1:1 com o documento: o tamanho do arquivo é exato, independente do zoom da tela
        const zoom = this.zoom;
        this.canvas.setDimensions({ width: this.doc.width, height: this.doc.height });
        this.canvas.setZoom(1);

        try {
            return this.canvas.toDataURL({ format, quality, multiplier: scale, enableRetinaScaling: false });
        } catch (error) {
            if (error?.name === 'SecurityError') {
                throw new Error('Uma das imagens vem de outro domínio e o navegador bloqueou a exportação. Envie a imagem pela biblioteca do estúdio.');
            }
            throw error;
        } finally {
            this.canvas.backgroundColor = background;
            hidden.forEach((obj) => { obj.visible = true; });
            this.setZoom(zoom);
            if (selection.length) this.select(selection);
            this.canvas.requestRenderAll();
        }
    }

    thumbnail(maxSize = 480) {
        try {
            return this.canvas.toDataURL({
                format: 'jpeg',
                quality: 0.8,
                multiplier: maxSize / Math.max(this.doc.width, this.doc.height) / this.zoom,
                enableRetinaScaling: false,
            });
        } catch {
            return null;
        }
    }

    placeholderCount() {
        return [...this.objects.values()].filter((obj) => obj.isPlaceholder && obj.visible).length;
    }

    dispose() {
        this.canvas.dispose();
    }
}
