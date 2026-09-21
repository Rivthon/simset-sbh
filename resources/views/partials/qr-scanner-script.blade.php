@once
    <script src="/vendor/html5-qrcode.min.js"></script>
@endonce

<script>
    (() => {
        const script = document.currentScript;
        const localScriptUrl = '/vendor/html5-qrcode.min.js';
        const cdnScriptUrl = 'https://unpkg.com/html5-qrcode';
        let loaderPromise = null;

        const hasHtml5Qrcode = () => typeof window.Html5Qrcode !== 'undefined';

        const loadScript = (url) => new Promise((resolve, reject) => {
            console.log('QR scanner mencoba load script:', url);

            const existing = document.querySelector(`script[src="${url}"]`);
            if (existing?.dataset.loaded === 'true') {
                resolve();
                return;
            }

            const element = document.createElement('script');
            const timeout = window.setTimeout(() => {
                console.error('QR scanner timeout load script:', url);
                reject(new Error(`Timeout loading ${url}`));
            }, 6000);

            element.addEventListener('load', () => {
                window.clearTimeout(timeout);
                element.dataset.loaded = 'true';
                console.log('QR scanner script loaded:', url, 'Html5Qrcode tersedia:', hasHtml5Qrcode());
                resolve();
            }, { once: true });

            element.addEventListener('error', (error) => {
                window.clearTimeout(timeout);
                console.error('QR scanner gagal load script:', url, error);
                reject(error);
            }, { once: true });

            element.src = url;
            element.async = true;
            document.head.appendChild(element);
        });

        const ensureHtml5Qrcode = async (setStatus = () => {}) => {
            if (hasHtml5Qrcode()) {
                console.log('QR scanner Html5Qrcode tersedia:', true);
                return true;
            }

            loaderPromise = loaderPromise || (async () => {
                console.log('QR scanner Html5Qrcode tersedia sebelum load:', false);

                try {
                    await loadScript(localScriptUrl);
                } catch (error) {
                    console.warn('QR scanner local script gagal, mencoba CDN:', error);
                }

                if (hasHtml5Qrcode()) {
                    return true;
                }

                try {
                    await loadScript(cdnScriptUrl);
                } catch (error) {
                    console.error('QR scanner CDN fallback gagal:', error);
                }

                return hasHtml5Qrcode();
            })();

            const loaded = await loaderPromise;
            console.log('QR scanner Html5Qrcode tersedia setelah loader:', loaded);

            if (loaded) {
                setStatus('Scanner siap. Klik Aktifkan Kamera.');
                return true;
            }

            setStatus('Library QR scanner gagal dimuat. Periksa koneksi internet atau file /vendor/html5-qrcode.min.js.');
            return false;
        };

        const initQrScanner = () => {
            const root = script.closest('[data-qr-scanner-root]') ?? document;
            const startButton = root.querySelector('[data-start-camera]');
            const imageButton = root.querySelector('[data-scan-image-button]');
            const imageInput = root.querySelector('[data-scan-image-input]');
            const wrap = root.querySelector('[data-camera-wrap]');
            const reader = root.querySelector('[data-camera-reader]');
            const status = root.querySelector('[data-camera-status]');
            const getInput = () => document.querySelector('[data-scan-input]');
            const getForm = () => document.querySelector('[data-scan-form]');
            let scanner = null;
            let scannerId = null;
            let isScanning = false;
            let isSubmitting = false;

            const isLocalHost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);
            const form = getForm();
            const mode = form?.action?.includes('/inventory-checks/') ? 'stock-opname' : 'normal';

            console.log('QR scanner secure context:', window.isSecureContext);
            console.log('QR scanner form action:', form?.action || '');
            console.log('QR scanner mode:', mode);
            console.log('QR scanner Html5Qrcode tersedia saat init:', hasHtml5Qrcode());
            console.log('QR scanner local script URL:', localScriptUrl);
            console.log('QR scanner CDN fallback URL:', cdnScriptUrl);

            const setStatus = (message) => {
                if (status) status.textContent = message;
            };

            const stopScanner = async () => {
                if (!scanner || !isScanning) return;

                try {
                    await scanner.stop();
                    await scanner.clear();
                } catch (error) {
                    console.warn('QR scanner stop failed:', error);
                } finally {
                    isScanning = false;
                }
            };

            const submitCode = (code) => {
                const input = getInput();
                const form = getForm();

                if (!code || !input || !form || isSubmitting) return false;

                isSubmitting = true;
                console.log('QR scanner decodedText:', code);
                console.log('QR scanner form action:', form.action);
                console.log('QR scanner mode:', mode);
                console.log('QR scanner secure context:', window.isSecureContext);

                input.value = code;
                setStatus('QR berhasil dibaca');
                stopScanner();

                window.setTimeout(() => {
                    form.submit();
                }, 400);

                return true;
            };

            const scanImageFile = async (file) => {
                if (!file) return;

                if (!await ensureHtml5Qrcode(setStatus)) {
                    return;
                }

                try {
                    await stopScanner();
                    scannerId = scannerId || 'qr-reader-' + Math.random().toString(36).slice(2);
                    reader.id = scannerId;
                    scanner = scanner || new window.Html5Qrcode(scannerId, false);

                    const decodedText = await scanner.scanFile(file, false);
                    submitCode(decodedText);
                } catch (error) {
                    console.warn('QR scanFile failed:', error);
                    setStatus('QR Code tidak terbaca dari foto. Gunakan input manual.');
                } finally {
                    if (imageInput) imageInput.value = '';
                }
            };

            startButton?.addEventListener('click', async () => {
                if (!window.isSecureContext && !isLocalHost) {
                    setStatus('Kamera live membutuhkan HTTPS. Gunakan input manual atau Scan dari Foto.');
                    return;
                }

                if (!navigator.mediaDevices?.getUserMedia) {
                    setStatus('Kamera tidak bisa diakses');
                    return;
                }

                setStatus('Memuat scanner QR...');

                if (!await ensureHtml5Qrcode(setStatus)) {
                    return;
                }

                try {
                    scannerId = scannerId || 'qr-reader-' + Math.random().toString(36).slice(2);
                    reader.id = scannerId;
                    reader.innerHTML = '';
                    wrap?.classList.remove('hidden');

                    scanner = new window.Html5Qrcode(scannerId, false);
                    setStatus('Kamera aktif, arahkan ke QR Code');

                    await scanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: (viewfinderWidth, viewfinderHeight) => {
                                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                                const target = minEdge < 320 ? Math.floor(minEdge * 0.82) : 280;
                                const size = Math.min(target, minEdge - 20);
                                return { width: size, height: size };
                            },
                            disableFlip: false,
                        },
                        (decodedText) => submitCode(decodedText),
                        () => {}
                    );
                    isScanning = true;
                } catch (error) {
                    console.warn('QR live camera failed:', error);
                    setStatus(window.isSecureContext || isLocalHost ? 'Kamera tidak bisa diakses' : 'Gunakan HTTPS untuk live camera');
                }
            });

            imageButton?.addEventListener('click', () => imageInput?.click());
            imageInput?.addEventListener('change', () => scanImageFile(imageInput.files?.[0]));
            window.addEventListener('beforeunload', () => stopScanner());

            if (!window.isSecureContext && !isLocalHost) {
                setStatus('Akses kamera live di HP membutuhkan HTTPS. Untuk testing gunakan ngrok atau domain HTTPS.');
            } else {
                setStatus(status?.textContent?.trim() || 'Kamera belum aktif');
            }

            ensureHtml5Qrcode(setStatus).then((loaded) => {
                if (loaded && status?.textContent?.trim() === 'Kamera belum aktif') {
                    setStatus('Scanner siap. Klik Aktifkan Kamera.');
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initQrScanner, { once: true });
        } else {
            initQrScanner();
        }
    })();
</script>
