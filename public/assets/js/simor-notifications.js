/**
 * SIMOR BMS — UNIFIED TELEMETRY NOTIFICATION & MODAL ENGINE
 * High-performance, zero-dependency notification engine with Web Audio synthesizer
 * PT. Besmindo Materi Sewatama
 */

(function (window) {
    'use strict';

    // ════════════════════════════════════════════════════════════════════════
    // 1. WEB AUDIO SYNTHESIZER (Pure CSS/JS sound feedback, zero external MP3s)
    // ════════════════════════════════════════════════════════════════════════
    let audioCtx = null;
    function getAudioContext() {
        if (!audioCtx && (window.AudioContext || window.webkitAudioContext)) {
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                audioCtx = null;
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function playTelemetrySound(type) {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'success') {
                // Dual chime: E5 -> G#5 (Crisp luxury confirmation)
                osc.frequency.setValueAtTime(659.25, now);
                osc.frequency.exponentialRampToValueAtTime(830.61, now + 0.08);
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
                osc.start(now);
                osc.stop(now + 0.28);
            } else if (type === 'error') {
                // Low alert double pulse
                osc.frequency.setValueAtTime(260, now);
                osc.frequency.linearRampToValueAtTime(180, now + 0.18);
                gain.gain.setValueAtTime(0.12, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
                osc.start(now);
                osc.stop(now + 0.25);
            } else if (type === 'warning') {
                // Subtle attention blip
                osc.frequency.setValueAtTime(520, now);
                gain.gain.setValueAtTime(0.09, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.16);
                osc.start(now);
                osc.stop(now + 0.16);
            } else {
                // Info radar tick
                osc.frequency.setValueAtTime(740, now);
                gain.gain.setValueAtTime(0.06, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);
                osc.start(now);
                osc.stop(now + 0.12);
            }
        } catch (err) {
            // Audio policy blocked or unsupported - fail silently
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // 2. TOAST NOTIFICATION ENGINE (SimorToast)
    // ════════════════════════════════════════════════════════════════════════
    const SimorToast = {
        container: null,

        ensureContainer() {
            if (!this.container || !document.body.contains(this.container)) {
                let el = document.getElementById('simor-toast-container');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'simor-toast-container';
                    document.body.appendChild(el);
                }
                this.container = el;
            }
            return this.container;
        },

        show(options = {}) {
            const container = this.ensureContainer();

            const type = options.type || 'info';
            const title = options.title || this.getDefaultTitle(type);
            const message = options.message || '';
            const duration = typeof options.duration === 'number' ? options.duration : 4500;
            const playSound = options.sound !== false;

            const toast = document.createElement('div');
            toast.className = `simor-toast simor-toast--${type}`;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

            const tagLabel = this.getTagLabel(type);
            const iconClass = this.getIconClass(type);
            const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            toast.innerHTML = `
                <div class="simor-toast__meta">
                    <span class="simor-toast__tag-badge">
                        <i class="${iconClass}"></i>
                        <span>${tagLabel}</span>
                    </span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="simor-toast__time">${timeStr}</span>
                        <button type="button" class="simor-toast__close-btn" title="Tutup notifikasi">
                            <i class="fa-solid fa-xmark" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </div>
                <div class="simor-toast__body">
                    <div class="simor-toast__icon-shell">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="simor-toast__text">
                        <div class="simor-toast__title">${this.escapeHtml(title)}</div>
                        <div class="simor-toast__message">${this.escapeHtml(message)}</div>
                    </div>
                </div>
                ${duration > 0 ? '<div class="simor-toast__progress"></div>' : ''}
            `;

            container.appendChild(toast);

            // Trigger visual appearance animation
            requestAnimationFrame(() => {
                toast.classList.add('is-visible');
            });

            if (playSound) {
                playTelemetrySound(type);
            }

            // Dismiss Button Handler
            const closeBtn = toast.querySelector('.simor-toast__close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.dismiss(toast);
                });
            }

            // Countdown & Hover-Pause Logic
            if (duration > 0) {
                const progressBar = toast.querySelector('.simor-toast__progress');
                let startTime = Date.now();
                let remaining = duration;
                let timerId = null;
                let isPaused = false;

                const step = () => {
                    if (!isPaused) {
                        const elapsed = Date.now() - startTime;
                        const pct = Math.max(0, 100 - (elapsed / duration) * 100);
                        if (progressBar) progressBar.style.width = pct + '%';

                        if (elapsed >= duration) {
                            this.dismiss(toast);
                            return;
                        }
                    }
                    timerId = requestAnimationFrame(step);
                };

                timerId = requestAnimationFrame(step);

                toast.addEventListener('mouseenter', () => {
                    isPaused = true;
                    remaining -= (Date.now() - startTime);
                });

                toast.addEventListener('mouseleave', () => {
                    if (isPaused) {
                        isPaused = false;
                        startTime = Date.now() - (duration - remaining);
                    }
                });
            }

            return toast;
        },

        dismiss(toast) {
            if (!toast || toast.classList.contains('is-hiding')) return;
            toast.classList.remove('is-visible');
            toast.classList.add('is-hiding');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        },

        getDefaultTitle(type) {
            switch (type) {
                case 'success': return 'Operasi Berhasil';
                case 'error':   return 'Kendala Operasi';
                case 'warning': return 'Peringatan Sistem';
                default:        return 'Informasi SIMOR';
            }
        },

        getTagLabel(type) {
            switch (type) {
                case 'success': return 'TELEMETRI // OK_200';
                case 'error':   return 'ANOMALI // ERR_500';
                case 'warning': return 'ALERT // WARN_300';
                default:        return 'SYSTEM // INFO';
            }
        },

        getIconClass(type) {
            switch (type) {
                case 'success': return 'fa-solid fa-circle-check';
                case 'error':   return 'fa-solid fa-triangle-exclamation';
                case 'warning': return 'fa-solid fa-shield-halved';
                default:        return 'fa-solid fa-circle-info';
            }
        },

        escapeHtml(str) {
            if (typeof str !== 'string') return String(str ?? '');
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        // Shorthands
        success(message, title = 'Operasi Berhasil') {
            return this.show({ type: 'success', title, message });
        },
        error(message, title = 'Kendala Terdeteksi') {
            return this.show({ type: 'error', title, message, duration: 6000 });
        },
        warning(message, title = 'Peringatan Operasi') {
            return this.show({ type: 'warning', title, message });
        },
        info(message, title = 'Informasi SIMOR') {
            return this.show({ type: 'info', title, message });
        }
    };

    // ════════════════════════════════════════════════════════════════════════
    // 3. TACTILE CONFIRMATION DIALOG (SimorModal.confirm)
    // ════════════════════════════════════════════════════════════════════════
    const SimorModal = {
        backdrop: null,

        ensureBackdrop() {
            if (!this.backdrop || !document.body.contains(this.backdrop)) {
                let el = document.getElementById('simor-modal-backdrop');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'simor-modal-backdrop';
                    document.body.appendChild(el);
                }
                this.backdrop = el;
            }
            return this.backdrop;
        },

        confirm(options = {}) {
            return new Promise((resolve) => {
                const backdrop = this.ensureBackdrop();
                const type = options.type || 'danger'; // 'danger' | 'warning' | 'info' | 'success'
                const title = options.title || 'Konfirmasi Tindakan';
                const message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const confirmText = options.confirmText || (type === 'danger' ? 'Ya, Lanjutkan Hapus' : 'Konfirmasi');
                const cancelText = options.cancelText || 'Batal';
                const metaTag = options.metaTag || `TERMINAL // CONFIRM_${type.toUpperCase()}`;

                let iconClass = 'fa-solid fa-triangle-exclamation';
                let btnVariantClass = 'simor-modal-btn--danger';
                let iconColor = '#f43f5e';
                let iconBg = 'rgba(244, 63, 94, 0.14)';

                if (type === 'warning') {
                    iconClass = 'fa-solid fa-triangle-exclamation';
                    btnVariantClass = 'simor-modal-btn--primary';
                    iconColor = '#f59e0b';
                    iconBg = 'rgba(245, 158, 11, 0.14)';
                } else if (type === 'success') {
                    iconClass = 'fa-solid fa-circle-check';
                    btnVariantClass = 'simor-modal-btn--success';
                    iconColor = '#10b981';
                    iconBg = 'rgba(16, 185, 129, 0.14)';
                } else if (type === 'info') {
                    iconClass = 'fa-solid fa-circle-info';
                    btnVariantClass = 'simor-modal-btn--primary';
                    iconColor = '#0ea5e9';
                    iconBg = 'rgba(14, 165, 233, 0.14)';
                }

                backdrop.innerHTML = `
                    <div class="simor-modal-card" role="dialog" aria-modal="true" aria-labelledby="simorModalTitle">
                        <div class="simor-modal-header">
                            <div class="simor-modal-icon-badge" style="background: ${iconBg}; color: ${iconColor};">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="simor-modal-title-group" style="flex: 1; min-width: 0;">
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 700; color: ${iconColor}; margin-bottom: 3px;">
                                    ${metaTag}
                                </div>
                                <h3 id="simorModalTitle">${SimorToast.escapeHtml(title)}</h3>
                                <p>${SimorToast.escapeHtml(message)}</p>
                            </div>
                        </div>

                        <div class="simor-modal-telemetry-box">
                            <span><i class="fa-solid fa-shield-halved" style="margin-right: 5px;"></i> Verifikasi Keamanan Sesi</span>
                            <span style="font-weight: 700;">USER // OPERATOR</span>
                        </div>

                        <div class="simor-modal-actions">
                            <button type="button" class="simor-modal-btn simor-modal-btn--cancel" id="simorModalBtnCancel">
                                <i class="fa-solid fa-xmark text-xs"></i>
                                <span>${SimorToast.escapeHtml(cancelText)}</span>
                            </button>
                            <button type="button" class="simor-modal-btn ${btnVariantClass}" id="simorModalBtnConfirm">
                                <i class="fa-solid fa-check text-xs"></i>
                                <span>${SimorToast.escapeHtml(confirmText)}</span>
                            </button>
                        </div>
                    </div>
                `;

                playTelemetrySound(type === 'danger' ? 'warning' : 'info');

                backdrop.classList.add('is-open');

                const btnConfirm = document.getElementById('simorModalBtnConfirm');
                const btnCancel  = document.getElementById('simorModalBtnCancel');

                if (btnConfirm) btnConfirm.focus();

                const cleanup = (result) => {
                    backdrop.classList.remove('is-open');
                    document.removeEventListener('keydown', handleKey);
                    setTimeout(() => {
                        backdrop.innerHTML = '';
                        resolve(result);
                    }, 240);
                };

                const handleKey = (e) => {
                    if (e.key === 'Escape') {
                        cleanup(false);
                    } else if (e.key === 'Enter' && e.target === btnConfirm) {
                        cleanup(true);
                    }
                };

                document.addEventListener('keydown', handleKey);

                if (btnConfirm) btnConfirm.onclick = () => cleanup(true);
                if (btnCancel)  btnCancel.onclick  = () => cleanup(false);

                backdrop.onclick = (e) => {
                    if (e.target === backdrop) cleanup(false);
                };
            });
        }
    };

    // Attach to global window
    window.SimorToast = SimorToast;
    window.SimorModal = SimorModal;

})(window);
