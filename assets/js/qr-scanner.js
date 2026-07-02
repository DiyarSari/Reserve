(function () {
    'use strict';

    var video = null;
    var stream = null;
    var detector = null;
    var scanning = false;

    function setStatus(message) {
        var status = document.getElementById('qrScannerStatus');
        if (status) {
            status.textContent = message;
        }
    }

    function normalizeToken(rawValue) {
        var value = String(rawValue || '').trim();
        if (!value) {
            return '';
        }

        if (/^https?:\/\//i.test(value)) {
            try {
                var url = new URL(value);
                var tokenFromQuery = url.searchParams.get('token');
                if (tokenFromQuery) {
                    value = tokenFromQuery;
                }
            } catch (error) {
            }
        }

        value = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        if (!value) {
            return '';
        }

        if (value.length <= 16) {
            return value.match(/.{1,4}/g).join('-');
        }
        return value;
    }

    function submitToken(token) {
        var input = document.getElementById('qrTokenInput');
        var form = document.getElementById('qrTokenForm');
        if (!input || !form) {
            return;
        }

        var normalized = normalizeToken(token);
        if (!normalized) {
            setStatus('QR token okunamadı. Manuel giriş yapın.');
            return;
        }

        input.value = normalized;
        form.submit();
    }

    function scanLoop() {
        if (!scanning || !detector || !video) {
            return;
        }

        detector.detect(video)
            .then(function (codes) {
                if (codes.length > 0 && codes[0].rawValue) {
                    scanning = false;
                    submitToken(codes[0].rawValue);
                    return;
                }
                window.requestAnimationFrame(scanLoop);
            })
            .catch(function () {
                window.requestAnimationFrame(scanLoop);
            });
    }

    function stopScanner() {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach(function (track) {
                track.stop();
            });
        }
        stream = null;
        setStatus('Scanner stopped.');
    }

    function startScanner() {
        video = document.getElementById('qrScannerVideo');
        if (!video) {
            return;
        }

        if (!('BarcodeDetector' in window)) {
            setStatus('This browser does not support native QR scanning. Please enter the token manually.');
            return;
        }

        detector = new BarcodeDetector({ formats: ['qr_code'] });

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (mediaStream) {
                stream = mediaStream;
                video.srcObject = mediaStream;
                return video.play();
            })
            .then(function () {
                scanning = true;
                setStatus('Scanning QR code...');
                scanLoop();
            })
            .catch(function () {
                setStatus('Camera could not be opened. Please use manual token entry.');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var startButton = document.getElementById('startQrScanner');
        var stopButton = document.getElementById('stopQrScanner');

        if (startButton) {
            startButton.addEventListener('click', startScanner);
        }
        if (stopButton) {
            stopButton.addEventListener('click', stopScanner);
        }
    });
})();
