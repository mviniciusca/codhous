// Componente Alpine do Estúdio de Posts. O Editor (Fabric) fica fora do estado reativo:
// o Alpine só guarda cópias simples do que a interface precisa mostrar.
import { Editor } from './editor';
import { clone, factories, isLinked, outOfBounds, resolveColor, safeAreas, TYPE_LABELS } from './document';
import { FEATURED_ICONS, iconLabel, iconNames, iconSvg } from './icons';
import { renderQueued } from './preview';
import { loadImageElement } from './renderer';
import { extractPalette } from './palette-extractor';

const AUTOSAVE_DELAY = 1500;
const RETRY_DELAY = 15000;

export function posterStudio(config) {
    let editor = null;
    let saveTimer = null;
    let retryTimer = null;
    let variationTimer = null;
    let draftTimer = null;
    let saving = null;
    let resizeObserver = null;

    const draftKey = `poster-studio:draft:${config.design.id}`;
    const storage = {
        get(key) { try { return JSON.parse(localStorage.getItem(key)); } catch { return null; } },
        set(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); } catch { /* sem storage */ } },
        remove(key) { try { localStorage.removeItem(key); } catch { /* sem storage */ } },
    };

    return {
        config,
        name: config.design.name,
        revision: config.design.revision,
        brand: config.brand,
        palettes: config.palettes,
        assets: config.assets,
        family: config.family,
        templates: config.templates,
        formats: config.formats,
        fonts: Object.keys(config.fonts),
        showShortcuts: false,
        colorRoles: config.colorRoles,
        typeLabels: TYPE_LABELS,

        ready: false,
        fatal: null,
        tool: 'templates',
        leftOpen: window.innerWidth >= 1024,
        rightOpen: window.innerWidth >= 1280,
        rightTab: 'design',
        menuOpen: false,
        zoomMenu: false,
        fitMode: true,

        selection: [],
        sel: null,
        layers: [],
        doc: { width: 1080, height: 1080, format: '', palette: { colors: {} }, background: null, caption: '' },
        zoom: 1,
        canUndo: false,
        canRedo: false,
        guides: [],
        showSafeArea: true,
        transform: null,

        status: 'saved',
        statusMessage: '',
        savedAt: config.design.updatedAt,
        changeCounter: 0,
        draftRecovery: null,

        iconQuery: '',
        imageQuery: '',
        imageSource: 'uploads',
        imageTarget: null,
        uploading: false,
        uploadProgress: 0,
        uploadError: null,
        templatePreviews: {},
        variationPreviews: {},
        customFormat: { width: 1080, height: 1080 },
        paletteName: '',
        recentColors: storage.get('poster-studio:recent-colors') ?? [],
        confirmDialog: null,
        toast: null,

        exportOptions: { format: 'png', quality: 0.92, scale: 1, transparent: false, filename: '' },
        exporting: false,
        exportResult: null,
        exportError: null,
        exportConfirmed: false,

        // ------------------------------------------------------------- ciclo de vida

        async init() {
            this.exportOptions.filename = this.slug(this.name);

            try {
                editor = new Editor({
                    canvasEl: this.$refs.canvas,
                    brand: this.brand,
                    onChange: (info) => this.handleChange(info),
                    onSelection: (ids) => this.handleSelection(ids),
                    onTransform: (box) => { this.transform = box; },
                    onGuides: (guides) => { this.guides = guides; },
                    onImageRequest: (id) => this.openImagePicker(id),
                });

                await editor.load(config.document);
                this.zoom = editor.fit(this.$refs.workspace);
                this.syncDocument();
                this.ready = true;
            } catch (error) {
                console.error(error);
                this.fatal = 'Não foi possível abrir o editor. Recarregue a página.';
                return;
            }

            resizeObserver = new ResizeObserver(() => {
                if (this.fitMode) this.zoom = editor.fit(this.$refs.workspace);
            });
            resizeObserver.observe(this.$refs.workspace);

            this.checkDraft();
            this.renderTemplatePreviews();

            window.addEventListener('keydown', (e) => this.handleKeydown(e));
            window.addEventListener('beforeunload', (e) => {
                if (['pending', 'saving', 'error', 'conflict'].includes(this.status)) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            this.$watch('rightTab', (tab) => tab === 'variations' && this.renderVariations());
            this.$watch('tool', (tool) => {
                if (tool === 'templates') this.renderTemplatePreviews();
                if (window.innerWidth < 1024) this.leftOpen = true;
            });
        },

        destroy() {
            resizeObserver?.disconnect();
            editor?.dispose();
        },

        // ------------------------------------------------------------- sincronização editor → UI

        handleChange({ history }) {
            this.syncDocument();

            if (!this.ready) return;

            this.changeCounter++;
            this.status = 'pending';
            this.exportResult = null;

            clearTimeout(draftTimer);
            draftTimer = setTimeout(() => storage.set(draftKey, { revision: this.revision, at: Date.now(), doc: editor.doc }), 400);

            this.scheduleSave();

            if (history && this.rightTab === 'variations') {
                clearTimeout(variationTimer);
                variationTimer = setTimeout(() => this.renderVariations(), 1200);
            }
        },

        handleSelection(ids) {
            this.selection = ids;
            this.sel = ids.length === 1 ? clone(editor.element(ids[0])) : null;
            this.transform = null;

            if (ids.length && this.rightTab !== 'design') this.rightTab = 'design';
        },

        syncDocument() {
            const doc = editor.doc;

            this.doc = {
                width: doc.width,
                height: doc.height,
                format: doc.format,
                templateId: doc.templateId,
                palette: clone(doc.palette),
                background: clone(doc.background),
                caption: doc.caption ?? '',
            };
            this.layers = doc.elements.map((el) => ({
                id: el.id,
                name: el.name || TYPE_LABELS[el.type],
                type: el.type,
                hidden: !!el.hidden,
                locked: !!el.locked,
                bind: el.bind,
                out: outOfBounds(el, doc),
                placeholder: el.type === 'image' && (el.placeholder || !el.src),
            })).reverse();
            this.canUndo = editor.canUndo;
            this.canRedo = editor.canRedo;

            if (this.sel) {
                const current = editor.element(this.sel.id);
                this.sel = current ? clone(current) : null;
            }
        },

        // ------------------------------------------------------------- salvamento

        scheduleSave(delay = AUTOSAVE_DELAY) {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => this.save(), delay);
        },

        async save(force = false) {
            clearTimeout(saveTimer);
            clearTimeout(retryTimer);

            if (saving) {
                await saving;
                if (this.status !== 'pending' && !force) return;
            }

            if (this.status === 'conflict' && !force) return;

            const counter = this.changeCounter;
            this.status = 'saving';

            saving = (async () => {
                try {
                    const result = await this.$wire.save(editor.doc, this.revision, this.name, editor.thumbnail(), force);

                    if (!result?.ok) {
                        this.status = result?.conflict ? 'conflict' : 'error';
                        this.statusMessage = result?.message ?? 'Não foi possível salvar.';
                        if (!result?.conflict) retryTimer = setTimeout(() => this.save(), RETRY_DELAY);
                        return;
                    }

                    this.revision = result.revision;
                    this.savedAt = result.savedAt;
                    this.statusMessage = '';

                    if (this.changeCounter === counter) {
                        this.status = 'saved';
                        storage.remove(draftKey);
                    } else {
                        this.status = 'pending';
                        this.scheduleSave(400);
                    }
                } catch (error) {
                    console.error(error);
                    this.status = 'error';
                    this.statusMessage = 'Sem conexão com o servidor. Tentaremos de novo em instantes.';
                    retryTimer = setTimeout(() => this.save(), RETRY_DELAY);
                }
            })();

            await saving;
            saving = null;
        },

        get statusLabel() {
            return {
                saved: this.savedAt ? `Salvo às ${this.savedAt.slice(-5)}` : 'Salvo',
                pending: 'Alterações pendentes',
                saving: 'Salvando…',
                error: 'Falha ao salvar',
                conflict: 'Conflito de versão',
            }[this.status];
        },

        renameProject() {
            this.name = (this.name || '').trim() || 'Sem título';
            this.changeCounter++;
            this.status = 'pending';
            this.scheduleSave(200);
        },

        checkDraft() {
            const draft = storage.get(draftKey);

            if (!draft?.doc) return;

            if (draft.revision !== this.revision || JSON.stringify(draft.doc) === JSON.stringify(editor.doc)) {
                storage.remove(draftKey);
                return;
            }

            this.draftRecovery = { at: new Date(draft.at).toLocaleString('pt-BR'), doc: draft.doc };
        },

        async restoreDraft() {
            const doc = this.draftRecovery?.doc;
            this.draftRecovery = null;
            if (doc) await editor.replaceDocument(doc);
        },

        discardDraft() {
            this.draftRecovery = null;
            storage.remove(draftKey);
        },

        // ------------------------------------------------------------- propriedades

        setProp(key, value, { commit = true } = {}) {
            if (!this.sel) return;

            const patch = { [key]: value };
            if (key === 'text' && this.sel.bind) patch.bind = null;

            this.sel[key] = value;
            editor.update(this.sel.id, patch, { commit });
        },

        setProps(patch) {
            if (!this.sel) return;
            Object.assign(this.sel, patch);
            editor.update(this.sel.id, patch);
        },

        setNumber(key, value, { min = -Infinity, max = Infinity, commit = true } = {}) {
            const number = parseFloat(String(value).replace(',', '.'));
            if (Number.isNaN(number)) return;
            this.setProp(key, Math.min(max, Math.max(min, number)), { commit });
        },

        /** Largura/altura mantendo a proporção de imagens, ícones e QR Codes. */
        setSize(dimension, value) {
            const number = parseFloat(String(value).replace(',', '.'));
            if (!this.sel || Number.isNaN(number) || number < 1) return;

            const keepRatio = ['image', 'icon', 'qrcode'].includes(this.sel.type);
            const patch = { [dimension]: number };

            if (keepRatio) {
                const other = dimension === 'width' ? 'height' : 'width';
                patch[other] = Math.round(number * (this.sel[other] / this.sel[dimension]) * 100) / 100;
            }

            Object.assign(this.sel, patch);
            editor.update(this.sel.id, patch);
        },

        fontWeights(family) {
            return this.config.fonts[family]?.weights ?? [400, 700];
        },

        /** Valores de transformação ao vivo durante o arraste; senão, os do documento. */
        geometry(key) {
            if (this.transform && this.selection.length) return this.transform[key];
            return this.sel ? Math.round(this.sel[key] ?? 0) : 0;
        },

        unbind() {
            this.setProp('bind', null);
        },

        bindLabel(key) {
            return this.brand.labels?.[key] ?? key;
        },

        bindValue(key) {
            return this.brand.bindings?.[key];
        },

        // ------------------------------------------------------------- cores

        resolve(ref) {
            return resolveColor(ref, this.doc.palette);
        },

        isLinked,

        colorLabel(ref) {
            if (ref == null) return 'Transparente';
            if (isLinked(ref)) return `Paleta · ${this.colorRoles[ref.role] ?? ref.role}`;
            return ref;
        },

        setColor(key, ref) {
            if (typeof ref === 'string') this.rememberColor(ref);
            this.setProp(key, ref);
        },

        rememberColor(hex) {
            hex = hex.toUpperCase();
            this.recentColors = [hex, ...this.recentColors.filter((c) => c !== hex)].slice(0, 12);
            storage.set('poster-studio:recent-colors', this.recentColors);
        },

        async applyPalette(palette) {
            await editor.setPalette({ id: palette.id, name: palette.name, colors: palette.colors });
        },

        async setPaletteColor(role, hex) {
            const colors = { ...this.doc.palette.colors, [role]: hex.toUpperCase() };
            await editor.setPalette({ id: 'custom', name: 'Personalizada', colors });
        },

        async savePalette() {
            const result = await this.$wire.savePalette(this.paletteName || 'Minha paleta', this.doc.palette.colors);

            if (result?.ok) {
                this.palettes = [...this.palettes, result.palette];
                await editor.setPalette(result.palette);
                this.paletteName = '';
                this.notify('Paleta salva.');
            }
        },

        async deletePalette(palette) {
            this.ask('Excluir paleta?', `"${palette.name}" deixará de aparecer na lista. Artes que já usam essas cores não mudam.`, async () => {
                const result = await this.$wire.deletePalette(palette.id);
                if (result?.ok) this.palettes = this.palettes.filter((p) => p.id !== palette.id);
            });
        },

        async extractFromImage() {
            const el = this.sel?.type === 'image' ? this.sel : null;

            if (!el?.src) {
                this.notify('Selecione uma imagem da arte para extrair as cores.', 'warning');
                return;
            }

            try {
                const colors = extractPalette(await loadImageElement(el.src));
                await editor.setPalette({ id: 'custom', name: 'Extraída da imagem', colors });
                this.notify('Paleta aproximada aplicada a partir da imagem.');
            } catch (error) {
                console.error(error);
                this.notify('Não foi possível analisar esta imagem.', 'danger');
            }
        },

        async setBackground(ref) {
            if (typeof ref === 'string') this.rememberColor(ref);
            await editor.setBackground(ref);
        },

        // ------------------------------------------------------------- inserção

        async addElement(kind, ...args) {
            await editor.addFromFactory(kind, ...args);
            this.closeLeftOnMobile();
        },

        async addIcon(name) {
            if (this.sel?.type === 'icon') {
                this.setProp('icon', name);
                return;
            }
            await this.addElement('icon', name);
        },

        async addQrCode() {
            await this.addElement('qrcode', this.brand.bindings?.['brand.whatsapp_url'] || 'https://');
            if (this.brand.bindings?.['brand.whatsapp_url']) this.setProp('bind', 'brand.whatsapp_url');
        },

        /** Usa o texto no elemento de texto selecionado ou cria um novo. */
        async useText(text, bind = null) {
            if (this.sel?.type === 'text') {
                this.setProp('text', text);
                if (bind) this.setProp('bind', bind);
                return;
            }

            const el = factories.subheading(editor.doc);
            Object.assign(el, { text, bind, name: bind ? this.bindLabel(bind) : 'Texto', fill: { role: 'text' } });
            await editor.add(el);
            this.closeLeftOnMobile();
        },

        async useBrandImage(key) {
            const src = this.brand.bindings?.[key];
            if (!src) return;
            await this.insertImage(src, { bind: key, name: this.bindLabel(key), fit: key === 'brand.photo' ? 'cover' : 'contain' });
        },

        openImagePicker(id = null) {
            this.imageTarget = id;
            this.tool = 'images';
            this.leftOpen = true;
        },

        /** Substitui a imagem alvo/selecionada ou insere uma nova, sem distorcer. */
        async insertImage(src, extra = {}) {
            const targetId = this.imageTarget ?? (this.sel?.type === 'image' ? this.sel.id : null);
            const target = targetId ? editor.element(targetId) : null;

            if (target) {
                await editor.update(target.id, { src, placeholder: false, bind: extra.bind ?? null });
                this.imageTarget = null;
                this.notify('Imagem substituída.');
                return;
            }

            let natural = null;
            try {
                const img = await loadImageElement(src);
                natural = { width: img.naturalWidth, height: img.naturalHeight };
            } catch {
                this.notify('Não foi possível carregar a imagem.', 'danger');
                return;
            }

            const el = factories.image(editor.doc, src, natural);
            Object.assign(el, { fit: extra.fit ?? 'cover', bind: extra.bind ?? null, name: extra.name ?? 'Imagem' });
            await editor.add(el);
            this.closeLeftOnMobile();
        },

        get libraryImages() {
            const query = this.imageQuery.trim().toLowerCase();
            const sources = {
                uploads: this.assets.map((a) => ({ id: a.id, url: a.url, name: a.name, meta: `${a.width}×${a.height}`, asset: a })),
                company: [
                    ...['brand.logo', 'brand.mascot'].filter((k) => this.brand.bindings?.[k]).map((k) => ({ url: this.brand.bindings[k], name: this.bindLabel(k), bind: k, meta: 'Marca' })),
                    ...this.brand.photos.map((p) => ({ url: p.url, name: p.name, meta: p.source })),
                ],
                equipment: this.brand.equipment.filter((e) => e.image).map((e) => ({ url: e.image, name: e.name, meta: e.category ?? 'Equipamento' })),
            }[this.imageSource] ?? [];

            return query ? sources.filter((img) => img.name.toLowerCase().includes(query)) : sources;
        },

        isAssetUsed(asset) {
            return editor?.doc.elements.some((el) => el.src === asset.url);
        },

        async uploadFiles(files) {
            this.uploadError = null;
            const { maxKb, mimes } = this.config.uploads;

            for (const file of Array.from(files)) {
                const ext = file.name.split('.').pop().toLowerCase();

                if (!mimes.includes(ext) || !file.type.startsWith('image/')) {
                    this.uploadError = `"${file.name}": use PNG, JPG ou WebP.`;
                    continue;
                }
                if (file.size > maxKb * 1024) {
                    this.uploadError = `"${file.name}" passa de ${Math.round(maxKb / 1024)} MB.`;
                    continue;
                }

                this.uploading = true;
                this.uploadProgress = 0;

                await new Promise((resolve) => {
                    this.$wire.upload('upload', file, async () => {
                        const result = await this.$wire.storeUpload();
                        if (result?.ok) {
                            this.assets = [result.asset, ...this.assets];
                        } else {
                            this.uploadError = result?.message ?? 'Falha no envio.';
                        }
                        resolve();
                    }, () => {
                        this.uploadError = `Falha ao enviar "${file.name}".`;
                        resolve();
                    }, (event) => {
                        this.uploadProgress = event.detail.progress;
                    });
                });
            }

            this.uploading = false;
            this.imageSource = 'uploads';
        },

        deleteAsset(asset) {
            const used = this.isAssetUsed(asset);
            this.ask(
                'Excluir imagem?',
                used ? 'Esta imagem está na arte aberta e em outras artes pode estar também. Nelas, aparecerá como "Imagem indisponível".' : 'A imagem será removida da biblioteca.',
                async () => {
                    const result = await this.$wire.deleteAsset(asset.id);
                    if (result?.ok) this.assets = this.assets.filter((a) => a.id !== asset.id);
                },
            );
        },

        get iconList() {
            const query = this.iconQuery.trim().toLowerCase();
            if (!query) return FEATURED_ICONS;
            return iconNames().filter((name) => name.toLowerCase().includes(query)).slice(0, 120);
        },

        iconHtml(name, size = 22) {
            return iconSvg(name, 'currentColor', 2, size);
        },

        iconLabel,

        // ------------------------------------------------------------- camadas

        selectLayer(id, event) {
            if (event?.shiftKey || event?.metaKey || event?.ctrlKey) {
                const ids = this.selection.includes(id) ? this.selection.filter((s) => s !== id) : [...this.selection, id];
                editor.select(ids);
                return;
            }
            editor.select([id]);
        },

        toggleLayer(id, key) {
            const el = editor.element(id);
            if (!el) return;
            const value = !el[key];
            editor.update(id, { [key]: value });
            if (key === 'hidden' && value && this.selection.includes(id)) editor.clearSelection();
        },

        renameLayer(id, name) {
            editor.update(id, { name: name.trim() || TYPE_LABELS[editor.element(id).type] });
        },

        moveLayer(id, direction) {
            editor.reorder(id, direction);
        },

        dragLayer: null,
        dragOver: null,

        layerIcon(type) {
            const icons = { text: 'Type', image: 'Image', rect: 'Square', ellipse: 'Circle', icon: 'Star', qrcode: 'QrCode' };
            return iconSvg(icons[type] ?? 'Square', 'currentColor', 2, 15);
        },

        dropLayer(targetId) {
            if (!this.dragLayer || this.dragLayer === targetId) return;
            const index = editor.doc.elements.findIndex((el) => el.id === targetId);
            editor.moveTo(this.dragLayer, index);
            this.dragLayer = null;
        },

        // ------------------------------------------------------------- ações sobre a seleção

        remove() { editor.remove(); },
        duplicate() { editor.duplicate(); },
        align(mode) { editor.align(mode); },
        distribute(axis) { editor.distribute(axis); },
        bringForward() { this.selection.forEach((id) => editor.reorder(id, 'up')); },
        sendBackward() { this.selection.forEach((id) => editor.reorder(id, 'down')); },

        toggleLock() {
            const lock = !this.selection.every((id) => editor.element(id)?.locked);
            editor.updateMany(this.selection, { locked: lock });
        },

        get selectionLocked() {
            return this.selection.length > 0 && this.selection.every((id) => this.layers.find((l) => l.id === id)?.locked);
        },

        clearSelection() { if (this.selection.length) editor?.clearSelection(); },

        undo() { editor.undo(); },
        redo() { editor.redo(); },

        // ------------------------------------------------------------- zoom

        setZoom(value) {
            this.fitMode = value === 'fit';
            this.zoom = this.fitMode ? editor.fit(this.$refs.workspace) : editor.setZoom(value);
            this.zoomMenu = false;
        },

        zoomBy(factor) {
            this.fitMode = false;
            this.zoom = editor.setZoom(this.zoom * factor);
        },

        get zoomPercent() {
            return Math.round(this.zoom * 100);
        },

        get safeAreaBoxes() {
            return this.showSafeArea ? safeAreas(this.doc) : [];
        },

        // ------------------------------------------------------------- templates e formatos

        async renderTemplatePreviews() {
            for (const template of this.templates) {
                if (this.templatePreviews[template.id] || !template.document) continue;
                renderQueued(template.document, { width: 240 }).then((url) => {
                    this.templatePreviews = { ...this.templatePreviews, [template.id]: url };
                }).catch(() => {});
            }
        },

        applyTemplate(template) {
            this.ask('Aplicar template?', `A composição atual será substituída por "${template.name}". Formato e paleta são mantidos, e você pode desfazer.`, async () => {
                const result = await this.$wire.applyTemplate(editor.doc, template.id);
                if (!result?.ok) return this.notify(result?.message ?? 'Falha ao aplicar o template.', 'danger');
                await editor.replaceDocument(result.document);
                this.closeLeftOnMobile();
            });
        },

        async changeFormat(format, width = null, height = null) {
            if (format === this.doc.format && format !== 'custom') return;

            const result = await this.$wire.adaptFormat(editor.doc, format, width, height);

            if (!result?.ok) {
                this.notify(result?.message ?? 'Formato inválido.', 'danger');
                return;
            }

            await editor.replaceDocument(result.document);
            this.fitMode = true;
            this.zoom = editor.fit(this.$refs.workspace);

            const template = this.templates.find((t) => t.id === this.doc.templateId);
            const group = this.formats[format]?.group;
            if (!template || !template.layouts.includes(group)) {
                this.notify('Arte adaptada automaticamente. Revise posições e textos marcados nas camadas.', 'warning');
            }
        },

        applyCustomFormat() {
            const { min, max } = this.config.customLimits;
            const width = Math.round(this.customFormat.width);
            const height = Math.round(this.customFormat.height);

            if (!(width >= min && width <= max && height >= min && height <= max)) {
                this.notify(`Use medidas entre ${min} e ${max} px.`, 'warning');
                return;
            }

            this.changeFormat('custom', width, height);
        },

        // ------------------------------------------------------------- variações

        renderVariations() {
            if (!editor) return;
            const doc = clone(editor.doc);

            this.palettes.forEach((palette) => {
                renderQueued(doc, { width: 200, palette: { id: palette.id, name: palette.name, colors: palette.colors } })
                    .then((url) => { this.variationPreviews = { ...this.variationPreviews, [palette.id]: url }; })
                    .catch(() => {});
            });
        },

        async saveVariation(palette) {
            await this.save();
            const result = await this.$wire.createVariation(editor.doc, palette.id, palette.colors);

            if (!result?.ok) return this.notify(result?.message ?? 'Não foi possível criar a variação.', 'danger');

            this.family = await this.$wire.family();
            this.notify(`Variação "${palette.name}" criada. Abra em "Versões" para editar.`);
        },

        async setAsMain() {
            const result = await this.$wire.setAsMain();
            if (result?.ok && result.family) {
                this.family = result.family;
                this.notify('Esta versão agora é a principal.');
            }
        },

        // ------------------------------------------------------------- exportação

        async exportImage() {
            this.exportError = null;
            this.exportResult = null;

            const placeholders = editor.placeholderCount();
            if (placeholders && !this.exportConfirmed) {
                this.ask('Há imagens vazias', `${placeholders} espaço(s) de imagem sem foto não aparecerão no arquivo. Exportar mesmo assim?`, () => {
                    this.exportConfirmed = true;
                    this.exportImage();
                });
                return;
            }
            this.exportConfirmed = false;

            this.exporting = true;
            await new Promise((r) => setTimeout(r, 30));

            try {
                const { format, quality, scale, transparent } = this.exportOptions;
                const dataUrl = await editor.exportImage({ format, quality: Number(quality), scale: Number(scale), transparent });
                const blob = await (await fetch(dataUrl)).blob();
                const verified = await this.measure(dataUrl);

                const expected = { width: Math.round(this.doc.width * scale), height: Math.round(this.doc.height * scale) };
                if (verified.width !== expected.width || verified.height !== expected.height) {
                    throw new Error(`O arquivo saiu com ${verified.width}×${verified.height} px em vez de ${expected.width}×${expected.height} px.`);
                }

                const extension = format === 'jpeg' ? 'jpg' : 'png';
                const filename = `${this.slug(this.exportOptions.filename || this.name)}.${extension}`;
                const url = URL.createObjectURL(blob);
                const link = Object.assign(document.createElement('a'), { href: url, download: filename });
                document.body.appendChild(link);
                link.click();
                link.remove();
                setTimeout(() => URL.revokeObjectURL(url), 10000);

                this.exportResult = { filename, width: verified.width, height: verified.height, size: this.formatBytes(blob.size), preview: dataUrl };
            } catch (error) {
                console.error(error);
                this.exportError = error.message || 'Falha ao gerar o arquivo.';
            } finally {
                this.exporting = false;
            }
        },

        measure(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve({ width: img.naturalWidth, height: img.naturalHeight });
                img.onerror = () => reject(new Error('O arquivo gerado não pôde ser lido.'));
                img.src = src;
            });
        },

        async copyCaption() {
            try {
                await navigator.clipboard.writeText(this.doc.caption);
                this.notify('Legenda copiada.');
            } catch {
                this.notify('Não foi possível copiar. Selecione o texto e copie manualmente.', 'warning');
            }
        },

        setCaption(text) {
            editor.setCaption(text);
        },

        async refreshBrand() {
            this.brand = await this.$wire.brand();
            const count = await editor.refreshBindings(this.brand);
            this.notify(count ? `${count} elemento(s) atualizados com os dados da empresa.` : 'Nenhum elemento vinculado aos dados da empresa.');
        },

        // ------------------------------------------------------------- teclado

        handleKeydown(e) {
            const target = e.target;
            const typing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName) || target.isContentEditable;
            const editingText = editor?.canvas.getActiveObject()?.isEditing;
            const mod = e.ctrlKey || e.metaKey;

            if (mod && e.key.toLowerCase() === 's') {
                e.preventDefault();
                this.save();
                return;
            }

            if (typing || editingText || !this.ready) return;

            const key = e.key.toLowerCase();

            if (mod && key === 'z') { e.preventDefault(); e.shiftKey ? this.redo() : this.undo(); return; }
            if (mod && key === 'y') { e.preventDefault(); this.redo(); return; }
            if (mod && key === 'd') { e.preventDefault(); this.duplicate(); return; }
            if (mod && key === 'c') { editor.copy(); return; }
            if (mod && key === 'v') { e.preventDefault(); editor.paste(); return; }
            if (mod && (key === '=' || key === '+')) { e.preventDefault(); this.zoomBy(1.2); return; }
            if (mod && key === '-') { e.preventDefault(); this.zoomBy(1 / 1.2); return; }
            if (mod && key === '0') { e.preventDefault(); this.setZoom('fit'); return; }

            if ((key === 'delete' || key === 'backspace') && this.selection.length) { e.preventDefault(); this.remove(); return; }
            if (key === 'escape') { editor.clearSelection(); this.confirmDialog = null; return; }

            const step = e.shiftKey ? 10 : 1;
            const arrows = { arrowleft: [-step, 0], arrowright: [step, 0], arrowup: [0, -step], arrowdown: [0, step] };
            if (arrows[key] && this.selection.length) {
                e.preventDefault();
                editor.nudge(...arrows[key]);
            }
        },

        // ------------------------------------------------------------- utilidades

        ask(title, message, action) {
            this.confirmDialog = { title, message, action };
        },

        async confirmYes() {
            const action = this.confirmDialog?.action;
            this.confirmDialog = null;
            await action?.();
        },

        notify(message, type = 'success') {
            this.toast = { message, type, id: Date.now() };
            const id = this.toast.id;
            setTimeout(() => { if (this.toast?.id === id) this.toast = null; }, 4000);
        },

        closeLeftOnMobile() {
            if (window.innerWidth < 1024) this.leftOpen = false;
        },

        slug(text) {
            return String(text || 'arte').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()
                .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 60) || 'arte';
        },

        formatBytes(bytes) {
            return bytes > 1024 * 1024 ? `${(bytes / 1024 / 1024).toFixed(1)} MB` : `${Math.round(bytes / 1024)} KB`;
        },

        formatLabel(key) {
            return key === 'custom' ? 'Personalizado' : this.formats[key]?.label ?? key;
        },
    };
}
