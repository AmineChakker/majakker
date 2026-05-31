import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ── Post card: reactions + inline comments ────────────────────────────────────
Alpine.data('postCard', (postId, initLiked, initLikes, initSparked, initSparks) => ({
    liked:          !!initLiked,
    sparked:        !!initSparked,
    likeCount:      initLikes,
    sparkCount:     initSparks,
    showComments:   false,
    commentText:    '',
    submitting:     false,
    comments:       [],
    commentsLoaded: false,
    commentCount:   0,

    init() {
        const el = this.$el.querySelector('[data-comment-count]');
        this.commentCount = Number(el?.dataset.commentCount ?? 0);
    },

    async toggleLike() {
        this.liked      = !this.liked;
        this.likeCount += this.liked ? 1 : -1;
        await fetch(`/api/posts/${postId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ type: 'like' }),
        }).catch(() => {});
    },

    async toggleSpark() {
        this.sparked     = !this.sparked;
        this.sparkCount += this.sparked ? 1 : -1;
        await fetch(`/api/posts/${postId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ type: 'spark' }),
        }).catch(() => {});
    },

    async toggleComments() {
        this.showComments = !this.showComments;
        if (this.showComments && !this.commentsLoaded) await this.loadComments();
    },

    async loadComments() {
        try {
            const res = await fetch(`/api/posts/${postId}/comments`);
            if (res.ok) {
                this.comments = await res.json();
                this.commentsLoaded = true;
            }
        } catch {}
    },

    async submitComment() {
        const body = this.commentText.trim();
        if (!body || this.submitting) return;
        this.submitting = true;
        try {
            const res = await fetch(`/api/posts/${postId}/comments`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body:    JSON.stringify({ body }),
            });
            if (res.ok) {
                const comment = await res.json();
                this.comments.push(comment);
                this.commentText = '';
                this.commentCount++;
            }
        } catch {}
        this.submitting = false;
    },

    timeAgo(dateStr) {
        const d = Math.floor((Date.now() - new Date(dateStr)) / 60000);
        if (d < 1)  return 'à l\'instant';
        if (d < 60) return `il y a ${d} min`;
        const h = Math.floor(d / 60);
        if (h < 24) return `il y a ${h} h`;
        return `il y a ${Math.floor(h / 24)} j`;
    },
}));

// ── Composer: rich post with uploads ─────────────────────────────────────────
Alpine.data('composer', (uploadUrl, postUrl) => ({
    text:        '',
    attachments: [],  // { tempId, id, url, kind, name, size, uploading, error }
    dragging:    false,
    expanded:    false,
    posting:     false,
    postError:   '',

    expand() { this.expanded = true; },

    get uploading()  { return this.attachments.some(a => a.uploading); },
    get hasError()   { return this.attachments.some(a => a.error); },
    get canSubmit()  { return !this.uploading && !this.posting && (this.text.trim().length > 0 || this.uploadedIds.length > 0); },
    get uploadedIds(){ return this.attachments.filter(a => a.id).map(a => a.id); },
    get charCount()  { return this.text.length; },

    triggerFile(accept) {
        const input    = document.createElement('input');
        input.type     = 'file';
        input.accept   = accept;
        input.multiple = true;
        input.onchange = e => this.handleFiles(e.target.files);
        input.click();
    },

    handleDrop(e) {
        this.dragging = false;
        this.handleFiles(e.dataTransfer.files);
        this.expand();
    },

    async handleFiles(files) {
        for (const file of [...files]) await this.uploadFile(file);
    },

    async uploadFile(file) {
        if (this.attachments.length >= 10) return;
        this.expand();

        const tempId  = Math.random().toString(36).slice(2);
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        // Client-side 200 MB guard (before even trying)
        if (file.size > 200 * 1024 * 1024) {
            this.attachments.push({ tempId, id: null, uploading: false,
                error: `${file.name} est trop volumineux (max 200 Mo).`,
                name: file.name, kind: 'file', url: null, isImage: false, isVideo: false,
                size: humanSize(file.size) });
            return;
        }

        // Immediate local preview for images
        const preview = isImage ? await toDataUrl(file).catch(() => null) : null;

        this.attachments.push({ tempId, id: null, url: preview, kind: kindOf(file),
            name: file.name, size: humanSize(file.size), uploading: true,
            isImage, isVideo, error: null });

        // Compress images client-side — ONLY if > 1.8 MB, and only use
        // the compressed result if it is actually SMALLER than the original.
        let fileToUpload = file;
        if (isImage && file.size > 1.8 * 1024 * 1024) {
            try {
                const compressed = await compressImage(file, 1920, 0.85);
                if (compressed.size < file.size) fileToUpload = compressed;
            } catch { /* keep original */ }
        }

        // Build FormData — include both _token body field AND X-CSRF-TOKEN header
        const fd = new FormData();
        fd.append('file',   fileToUpload, file.name);
        fd.append('_token', csrfToken());

        try {
            const res = await fetch(uploadUrl, {
                method:  'POST',
                body:    fd,
                headers: {
                    'X-CSRF-TOKEN':     csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',  // forces Laravel JSON errors
                    'Accept':           'application/json',
                },
            });

            if (res.ok) {
                const data = await res.json();
                const idx  = this.attachments.findIndex(a => a.tempId === tempId);
                if (idx !== -1) Object.assign(this.attachments[idx], {
                    id: data.id, url: data.url, kind: data.kind,
                    size: data.file_size_human, uploading: false, error: null,
                    isImage: data.kind === 'image',
                    isVideo: data.kind === 'video',
                });
                return;
            }

            // ── Error responses ──────────────────────────────────────────────
            let msg = `Échec (erreur ${res.status}) — ${file.name}`;

            if (res.status === 413) {
                msg = `${file.name} dépasse la limite du serveur. Redémarrez avec : php -d upload_max_filesize=200M artisan serve`;
            } else if (res.status === 419) {
                msg = 'Session expirée. Rechargez la page et réessayez.';
            } else if (res.status === 422) {
                try {
                    const j = await res.json();
                    msg = Object.values(j.errors ?? {})[0]?.[0]
                       ?? j.message
                       ?? msg;
                } catch {}
            } else if (res.status === 500) {
                msg = `Erreur serveur (500). Vérifiez les logs Laravel.`;
            }

            this.setAttachmentError(tempId, msg);

        } catch (e) {
            this.setAttachmentError(tempId, `Erreur réseau — vérifiez votre connexion. (${file.name})`);
        }
    },

    setAttachmentError(tempId, msg) {
        const idx = this.attachments.findIndex(a => a.tempId === tempId);
        if (idx !== -1) Object.assign(this.attachments[idx], { uploading: false, error: msg });
    },

    removeAttachment(tempId) {
        this.attachments = this.attachments.filter(a => a.tempId !== tempId);
    },

    async doSubmit() {
        if (!this.canSubmit) return;
        this.posting   = true;
        this.postError = '';

        // Build FormData from the form element
        const form = this.$refs.form;
        const fd   = new FormData(form);

        // Remove any stale attachment_ids that might be in the form
        // and add the current ones from our reactive state
        // (FormData doesn't support deleting by name reliably in all browsers,
        //  so we build a fresh one)
        const freshFd = new FormData();
        freshFd.append('_token', csrfToken());
        freshFd.append('body',   this.text);

        this.uploadedIds.forEach(id => freshFd.append('attachment_ids[]', id));

        try {
            const res = await fetch(postUrl, {
                method:  'POST',
                body:    freshFd,
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            });

            const contentType = res.headers.get('content-type') ?? '';
            const isJson      = contentType.includes('application/json');

            if (res.ok) {
                if (isJson) {
                    const data = await res.json();
                    window.location.href = data.redirect ?? window.location.href;
                } else {
                    window.location.reload();
                }
                return;
            }

            if (res.redirected) {
                window.location.href = res.url;
                return;
            }

            // Error response
            if (isJson) {
                const data = await res.json();
                this.postError = Object.values(data.errors ?? {})[0]?.[0] ?? 'Erreur lors de la publication.';
            } else if (res.status === 419) {
                this.postError = 'Session expirée. Rechargez la page et réessayez.';
            } else {
                this.postError = `Erreur ${res.status} lors de la publication.`;
            }
        } catch {
            this.postError = 'Erreur réseau. Vérifiez votre connexion.';
        }
        this.posting = false;
    },
}));

// ── Helpers ───────────────────────────────────────────────────────────────────
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function humanSize(bytes) {
    if (bytes < 1024)    return bytes + ' o';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1048576).toFixed(1) + ' Mo';
}

function kindOf(file) {
    if (file.type.startsWith('image/')) return 'image';
    if (file.type.startsWith('video/')) return 'video';
    return 'file';
}

function toDataUrl(file) {
    return new Promise(res => {
        const r = new FileReader();
        r.onload = e => res(e.target.result);
        r.readAsDataURL(file);
    });
}

/** Compress an image using Canvas to max width/height, returning a Blob as a File */
function compressImage(file, maxDim = 1920, quality = 0.85) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            let { width, height } = img;
            if (width > maxDim || height > maxDim) {
                if (width > height) { height = Math.round(height * maxDim / width); width = maxDim; }
                else                { width  = Math.round(width  * maxDim / height); height = maxDim; }
            }
            const canvas = document.createElement('canvas');
            canvas.width  = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(img, 0, 0, width, height);
            canvas.toBlob(blob => {
                if (!blob) { reject(new Error('Compression failed')); return; }
                resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
            }, 'image/jpeg', quality);
        };
        img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('Load failed')); };
        img.src = url;
    });
}

Alpine.start();
